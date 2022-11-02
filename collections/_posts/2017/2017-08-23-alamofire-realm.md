---
title: Swift 3 + Alamofire + AlamofireObjectMapper + Realm
date:  2017-08-23 10:00:00 +0100
tags:  swift alamofire realm
icon:  swift

api:    http://danielsaidi.com/demo_Alamofire_AlamofireObjectMapper_Realm/api
cocoapods: http://cocoapods.org/
dip:    http://github.com/AliSoftware/Dip
github: http://github.com/danielsaidi/demo_Alamofire_AlamofireObjectMapper_Realm
video:  http://www.youtube.com/watch?v=LuKehlKoN7o&lc=z22qu35a4xawiriehacdp435fnpjgmq2f54mjmyhi2tw03c010c.1502618893412377

new:    http://danielsaidi.com/blog/2018/12/27/alamofire-objectmapper
---

This is a summary of my talk at [CocoaHeads Sthlm, April 3 2017]({{page.video}}),
where I talked about using Alamofire, AlamofireObjectMapper and Realm to talk to
an api, map its responses, retry and adapt requests and then use Realm to create 
offline support layer by persisting data locally.

In this post, I'll recreate the entire app from scratch, with some modifications.


## Update information

I have written a new post that uses Swift 4.2 instead of Swift 3. It also changes
some fundamental things and is more in line with modern Swift, so you should
[check it out]({{page.new}}) instead of this post.


## Video

You can watch the original talk [here]({{page.video}}). The talk focuses more on
concepts than code, so that talk and this post complete each other pretty well.


## Prerequisites

For this tutorial, I expect that you know how [CocoaPods]({{page.cocoapods}})
works. I will use terms like `podfile`, expecting you to know what it means.


## Disclaimer 

In large app projects, I prefer to extract as much code and logic as possible to
separate libraries, which I then use as decoupled building blocks. For instance,
I would keep domain logic in a domain library that doesn't know anything about
the app. In this small project, however, I will keep it all in a single target.

I use to separate public and private functions and any interface implementations
into extensions as well, but will skip that pattern in this demo, so that we get
as little code and conventions as possible. 


## Why use a static api?

In the app, we'll use a static api to fetch movies. The api is a static Jekyll
site with a small movie collection, that lets us grab top rated and top grossing
movies, as well as single movies by id.

The limited api hopefully lets us focus on Alamofire and Realm instead of having
to understand an external api, set up a developer account, handle auth logic etc.


## Step 1 - Define the domain model

Start by creating a clean Xcode project. I went with a simple iOS storyboard app,
but you can set it up however you like. We can then start by defining the domain model.

The app will fetch movie data from our api. A `Movie` has basic info about the
movie as well as a `cast` array property of `Actor` objects. For simplicity, `Actor`
only has a name and is used to show how easy recursive mapping is with Alamofire.
 
To avoid coupling the app to concrete types, let's define the model as protocols. 
Create a `Domain` folder in the project root, add a `Model` folder to it then add
these two files to `Model`:

```swift
// Movie.swift

import Foundation

protocol Movie {
    
    var id: Int { get }
    var name: String { get }
    var year: Int { get }
    var releaseDate: Date { get }
    var grossing: Int { get }
    var rating: Double { get }
    var cast: [Actor] { get }
}
```

```swift
// Actor.swift

import Foundation

protocol Actor {
    
    var name: String { get }
}
```

As you'll see later, the app will only handle protocols, not concrete types. This
makes it easy to switch out the implementations used by the app, whenever needed.

`Update` Since this was written, I have started using plain structs for data types
instead of protocols, and instead have different structs for the api model and the 
local domain model for the app. Mapping from api-specific to app-specific types
requires more code, but is a lot better.


## Step 2 - Define the domain logic

Now, let's describe how the app should fetch movies from the api. Add a `Services`
folder to `Domain`, then add this file:

```swift
// MovieService.swift

import Foundation

typealias MovieResult = (_ movie: Movie?, _ error: Error?) -> ()
typealias MoviesResult = (_ movies: [Movie], _ error: Error?) -> ()

protocol MovieService: class {
    
    func getMovie(id: Int, completion: @escaping MovieResult)
    func getTopGrossingMovies(year: Int, completion: @escaping MoviesResult)
    func getTopRatedMovies(year: Int, completion: @escaping MoviesResult)
}
```

Basically, this service protocol tells us that a movie service will let us get movies
asynchronously (well, a completion block implies it, but doesn't enforce async) -
both single movies by id as well as top grossing and top rated movie collections.


## Step 3 - Create an api specific domain model

Before we can add an api specific implementation to the project, we must add two
pods to `podfile` - `Alamofire` and `AlamofireObjectMapper`. Run `pod install`,
then open the created workspace.

Now create an `Api` folder in the project root, add a `Model` folder to it and
add these files to `Model`:

```swift
// ApiMovie.swift

import ObjectMapper

class ApiMovie: Movie, Mappable {
    
    required public init?(map: Map) {}
    
    var id = 0
    var name = ""
    var year = 0
    var releaseDate = Date(timeIntervalSince1970: 0)
    var grossing = 0
    var rating = 0.0
    var cast: [Actor] { return _cast }
    
    private var _cast = [ApiActor]()
    
    func mapping(map: Map) {
        id <- map["id"]
        name <- map["name"]
        year <- map["year"]
        releaseDate <- (map["releaseDate"], DateTransform.custom)
        grossing <- map["grossing"]
        rating <- map["rating"]
        _cast <- map["cast"]
    }
}
``` 

```swift
// ApiActor.swift

import ObjectMapper

class ApiActor: Actor, Mappable {
    
    required public init?(map: Map) {}

    var name = ""
    
    func mapping(map: Map) {
        name <- map["name"]
    }
}
``` 

These classes implement the domain model, with additional mapping. `ApiActor` is
straightforward, while `ApiMovie` can be further described:

* `releaseDate` is parsed with `DateTransform`. We may have to adjust this later.

* `Movie` has an `[Actor]` array, but the mapping requires `[ApiActor]`. We thus
use a private `_cast` property for mapping and have a calculated `cast` property.

If we have set things up properly, we should now be able to point Alamofire to a
valid url and recursively parse movie data with little effort.


## Step 4 - Setup the core api logic

Before we create an api-specific `MovieService` implementation, let's setup some
core api logic in the `api` folder, to define how to communicate with the api.


### Managing api environments

Since we often have to use different api environments in test and prod, I use to
have an enum to define api endpoints. Even though we only have a single endpoint
in this app, I still prefer to have it in place:

```swift
// ApiEnvironment.swift

import Foundation

enum ApiEnvironment: String { 
    
    case
    production = "http://danielsaidi.com/presentation_2017-04-03_AlamofireRealm/api/"
    
    var url: String {
        return rawValue
    }
}
```


### Managing api routes

With this environment in place, we can list available api routes in another enum:

```swift
// ApiRoute.swift

enum ApiRoute { 
    
    case
    movie(id: Int),
    topGrossingMovies(year: Int),
    topRatedMovies(year: Int)
    
    var path: String {
        switch self {
        case .movie(let id): return "movies/\(id)"
        case .topGrossingMovies(let year): return "movies/topGrossing/\(year)"
        case .topRatedMovies(let year): return "movies/topRated/\(year)"
        }
    }
    
    func url(for environment: ApiEnvironment) -> String {
        return "\(environment.url)/\(path)"
    }
}
```

Since `year` and `id` are dynamic route segments, we use parametered enum cases.


### Managing api context

I usually have an `ApiContext` class that holds api-specific information for the
app, such as tokens and environments. If you use a singleton, every context-based
api service is automatically affected when the context is modified.

Let's create an `ApiContext` protocol and a non-persisted implementation in a new
`Context` folder:

```swift
// ApiContext.swift

import Foundation

protocol ApiContext: class {
    
    var environment: ApiEnvironment { get set }
}
```   

```swift
// NonPersistedApiContext.swift

import Foundation

class NonPersistentApiContext: ApiContext {
    
    init(environment: ApiEnvironment) {
        self.environment = environment
    }
    
    var environment: ApiEnvironment
}
```

We can now inject this context into all api-specific service implementations. If
we later want to create a persistent context, e.g. one that saves token data in
`UserDefault`, we just have to create another implementation, then replace the
implementation we use in our app.


### Specifying basic api behavior

To simplify how to talk with the api using Alamofire, let's create a base class
for our api-based services. Add this file to a `Services` sub folder:

```swift
// AlamofireService.swift

class AlamofireService {    
    
    init(context: ApiContext) {
        self.context = context
    }
    
    
    var context: ApiContext
    
    
    func get(at route: ApiRoute, params: Parameters? = nil) -> DataRequest {
        return request(
            at: route, 
            method: .get, 
            params: params, 
            encoding: URLEncoding.default)
    }
    
    func post(at route: ApiRoute, params: Parameters? = nil) -> DataRequest {
        return request(
            at: route, 
            method: .post, 
            params: params, 
            encoding: JSONEncoding.default)
    }
    
    func put(at route: ApiRoute, params: Parameters? = nil) -> DataRequest {
        return request(
            at: route, 
            method: .put, 
            params: params, 
            encoding: JSONEncoding.default)
    }
    
    func request(at route: ApiRoute, method: HTTPMethod, params: Parameters?, encoding: ParameterEncoding) -> DataRequest {
        return Alamofire.request(
            route.url(for: context.environment), 
            method: method, 
            parameters: params, 
            encoding: encoding)
        .validate()
    }
}
``` 

Restricting our services to only request `ApiRoute` ensures that the app doesn't
make any unspecified requests. If you need to call custom URLs, I suggest adding
a `.custom` case to the `ApiRoute` enum.

Ok, that was a pretty long setup, but I think that we are now ready to load some
movies from the api.


## Step 5 - Create an api-based movie service

Let's create an api-based movie service and fetch some movies from our api! Just
add this file to the `Services` sub folder, next to `AlamofireService`:

```swift
import Alamofire
import AlamofireObjectMapper

class AlamofireMovieService: AlamofireService, MovieService {
    
    func getMovie(id: Int, completion: @escaping MovieResult) {
        get(at: .movie(id: id)).responseObject {
            (res: DataResponse<ApiMovie>) in
            completion(res.result.value, res.result.error)
        }
    }
    
    func getTopGrossingMovies(year: Int, completion: @escaping MoviesResult) {
        get(at: .topGrossingMovies(year: year)).responseArray {
            (res: DataResponse<[ApiMovie]>) in
            completion(res.result.value ?? [], res.result.error)
        }
    }
    
    func getTopRatedMovies(year: Int, completion: @escaping MoviesResult) {
        get(at: .topRatedMovies(year: year)).responseArray {
            (res: DataResponse<[ApiMovie]>) in
            completion(res.result.value ?? [], res.result.error)
        }
    }
}
```

As you see, the implementation is super-simple. It just performs get requests on
the routes and specifies a return type. Alamofire and AlamofireObjectMapper then
take care of fetching and mapping responses.

As you can see `getMovie` uses `responseObject`, while the other functions use
`responseArray` instead. This is because a single movie is returned as single object,
while top grossing and top rated movies are returned as arrays. If these arrays were
instead parts of a response object (recommended), you would have to specify a  new
api model that can map that response, then use `responseObject`. This would give you
a lot more future flexibility, so I'd suggest that you avoid using arrays in your api.


## Step 6 - Perform your very first request

We will now setup our app to fetch data from the api. Remove all the boilerplate
code and add this to your `ViewController`:

```swift
override func viewDidLoad() {
    super.viewDidLoad()
    let env = ApiEnvironment.production
    let context = NonPersistentApiContext(environment: env)
    let service = AlamofireMovieService(context: context)
    service.getTopGrossingMovies(year: 2016) { (movies, error) in
        if let error = error {
            return print(error.localizedDescription)
        }
        print("Found \(movies.count) movies:")
        movies.forEach { print("   \($0.name)") }
    }
}
```

Before you can run this code, you must allow the app to perform external requests,
which you do by adding this to `Info.plist` (in a real app, you should specify the
exact domains your app allows):

```xml
<key>NSAppTransportSecurity</key>
<dict>
    <key>NSAllowsArbitraryLoads</key>
    <true/>
</dict>
```

Run the app. If everything is correctly setup, it should print our the following:

```
Found 10 movies:
   Finding Dory
   Rouge One - A Star Wars Story
   Captain America - Civil War
   The Secret Life of Pets
   The Jungle Book
   Deadpool
   Zootopia
   Batman v Superman - Dawn of Justice
   Suicide Squad
   Doctor Strange
```

If you see this in Xcode's log, the app loads movie data from the api. Well done!

Now change the print format for each movie to look like this:

```swift
movies.forEach {
    print("   \($0.name) (\($0.releaseDate))") 
}
```

The app should now output the following instead:

```
Found 10 movies:
   Finding Dory (1970-01-01 00:33:36 +0000)
   Rouge One - A Star Wars Story (1970-01-01 00:33:36 +0000)
   Captain America - Civil War (1970-01-01 00:33:36 +0000)
   The Secret Life of Pets (1970-01-01 00:33:36 +0000)
   The Jungle Book (1970-01-01 00:33:36 +0000)
   Deadpool (1970-01-01 00:33:36 +0000)
   Zootopia (1970-01-01 00:33:36 +0000)
   Batman v Superman - Dawn of Justice (1970-01-01 00:33:36 +0000)
   Suicide Squad (1970-01-01 00:33:36 +0000)
   Doctor Strange (1970-01-01 00:33:36 +0000)
```

Oooops! Seems like the date parsing does not work. I TOLD you that we would have
fix this. Let's do it.


## Step 7 - Adjust date parsing

The problem is that the api uses a different date format than expected. This can
be solved by replacing the `DateTransform`. Create this `DateTransform` extension
and place it in an `Extensions` folder:

```swift
// DateTransform_Custom.swift

import ObjectMapper

public extension DateTransform {
    
    public static var custom: DateFormatterTransform {
        let formatter = DateFormatter()
        formatter.dateFormat = "yyyy-MM-dd"
        formatter.timeZone = TimeZone(secondsFromGMT: 0)
        return DateFormatterTransform(dateFormatter: formatter)
    }
}
```

Now change the `releaseDate` mapping in the `ApiMovie` class to look like this:

```swift
releaseDate <- (map["releaseDate"], DateTransform.custom)
```

Problem solved! The app should now output the following instead:

```
Found 10 movies:
   Finding Dory (2016-06-17 00:00:00 +0000)
   Rouge One - A Star Wars Story (2016-12-16 00:00:00 +0000)
   Captain America - Civil War (2016-05-06 00:00:00 +0000)
   The Secret Life of Pets (2016-07-08 00:00:00 +0000)
   The Jungle Book (2016-04-15 00:00:00 +0000)
   Deadpool (2016-02-12 00:00:00 +0000)
   Zootopia (2016-03-04 00:00:00 +0000)
   Batman v Superman - Dawn of Justice (2016-03-25 00:00:00 +0000)
   Suicide Squad (2016-08-05 00:00:00 +0000)
   Doctor Strange (2016-11-05 00:00:00 +0000)
```

If you inspect the other properties, you will see that they are correctly parsed
as well. Time to celebrate! ...then return here for some database persistency.


## Step 8 - Create a Realm-specific domain model

When you get data from an api, it doesn't hurt to store some data in a database,
e.g. for offline support. A very convenient database engine is `Realm`.

Add a `Realm` folder to the application root, then add a `Model` and a `Services`
folder to it. Now, let's create...wait! Before we can use Realm, we have to grab
it from CocoaPods and add it to the project. 

Add `RealmSwift` to `podfile`, then add this bottommost:

```
post_install do |installer|
    installer.pods_project.targets.each do |target|
        target.build_configurations.each do |config|
            config.build_settings['SWIFT_VERSION'] = '3.0'
        end
    end
end
```

After running `pod install`, we can now create Realm-specific models that will
either be created manually as we map and persist data from the api, or automatically
as we fetch data from the database.

Realm will take care of the latter case, but we have to find a way to easily map
api objects to Realm objects. To fix this, just add these files to the `Model` folder:

```swift
// RealmMovie.swift

import RealmSwift

class RealmMovie: Object, Movie {
    
    convenience required public init(copy obj: Movie) {
        self.init()
        id = obj.id
        name = obj.name
        year = obj.year
        releaseDate = obj.releaseDate
        grossing = obj.grossing
        rating = obj.rating
        _cast.append(contentsOf: obj.cast.map { RealmActor(copy: $0) })
    }
    

    dynamic var id = 0
    dynamic var name = ""
    dynamic var year = 0
    dynamic var releaseDate = Date(timeIntervalSince1970: 0)
    dynamic var grossing = 0
    dynamic var rating = 0.0
    var cast: [Actor] { return Array(_cast) }
    
    var _cast = List<RealmActor>()


    override class func primaryKey() -> String? {
        return "id"
    }
}
```

```swift
// RealmActor.swift

import RealmSwift

class RealmActor: Object, Actor {
    
    convenience required public init(copy obj: Actor) {
        self.init()
        name = obj.name
    }
    

    dynamic var name = ""
    

    override class func primaryKey() -> String? {
        return "name"
    }
}
```

Both classes inherit `Realm`'s `Object` class and have a convenience initializer
that copies properties from another instance of the protocol they implement.

Like in the api model, `RealmActor` is pretty straightforward, while `RealmMovie`
is more complex. It has a private `_cast` property, which is used as the backing
value for `cast`. `_cast` is a Realm `List<RealmActor>`, while `cast` is a Swift
`[Actor]`, just like in the protocol.


## Step 9 - Create a Realm-specific movie service

Now let's add a Realm-specific `MovieService` that lets us store movies from the
api in Realm. Add this file to the `Services` folder:

```swift
// RealmMovieService.swift

import RealmSwift

class RealmMovieService: MovieService {
    
    init(baseService: MovieService) {
        self.baseService = baseService
    }
    
    
    private let baseService: MovieService
    
    private var realm: Realm { return try! Realm() }
    
    
    func getMovie(id: Int, completion: @escaping MovieResult) {
        getMovieFromDb(id: id, completion: completion)
        getMovieFromService(id: id, completion: completion)
    }
    
    func getTopGrossingMovies(year: Int, completion: @escaping MoviesResult) {
        getTopGrossingMoviesFromDb(year: year, completion: completion)
        getTopGrossingMoviesFromService(year: year, completion: completion)
    }
    
    func getTopRatedMovies(year: Int, completion: @escaping MoviesResult) {
        getTopRatedMoviesFromDb(year: year, completion: completion)
        getTopRatedMoviesFromService(year: year, completion: completion)
    }
    
    
    private func getMovieFromDb(id: Int, completion: @escaping MovieResult) {
        let obj = realm.object(ofType: RealmMovie.self, forPrimaryKey: id)
        completion(obj, nil)
    }
    
    private func getMovieFromService(id: Int, completion: @escaping MovieResult) {
        baseService.getMovie(id: id) { (movie, error) in
            self.persist(movie)
            completion(movie, error)
        }
    }
    
    private func getTopGrossingMoviesFromDb(year: Int, completion: @escaping MoviesResult) {
        let objs = realm.objects(RealmMovie.self).filter("year == \(year)")
        let sorted = objs.sorted { $0.grossing > $1.grossing }
        completion(Array(sorted), nil)
    }
    
    private func getTopGrossingMoviesFromService(year: Int, completion: @escaping MoviesResult) {
        baseService.getTopGrossingMovies(year: year) {  (movies, error) in
            self.persist(movies)
            completion(movies, error)
        }
    }
    
    private func getTopRatedMoviesFromDb(year: Int, completion: @escaping MoviesResult) {
        let objs = realm.objects(RealmMovie.self).filter("year == \(year)")
        let sorted = objs.sorted { $0.rating > $1.rating }
        completion(Array(sorted), nil)
    }
    
    private func getTopRatedMoviesFromService(year: Int, completion: @escaping MoviesResult) {
        baseService.getTopRatedMovies(year: year) {  (movies, error) in
            self.persist(movies)
            completion(movies, error)
        }
    }
    
    private func persist(_ movie: Movie?) {
        guard let movie = movie else { return }
        persist([movie])
    }
    
    private func persist(_ movies: [Movie]) {
        let objs = movies.map { RealmMovie(copy: $0) }
        try! realm.write {
            realm.add(objs, update: true)
        }
    }
}
```

As you can see, `RealmMovieService`'s initializer requires another `MovieService`
instance. Why?

`RealmMovieService` is a so called `decorator`, which uses a base implementation
of a protocol it implements, to extend the base implementation with its on logic.
In this case, our `baseService` is an `AlamofireMovieService`, but the decorator
shouldn't know about how the base service works, just what the protocol promises.

In this case, `RealmMovieService` will try to get data from the database, but at
the same time, it will also try to get data from the base service. When the base
service completes, `RealmMovieService` saves any data it receives. It then calls
the completion block to notify its caller about the new data.

`Disclaimer:` This is an intentionally simple design. `RealmMovieService` always
loads data from the database **and** from the base service. In a real app, you'd
probably have some logic to determine if calling the base service is needed.


## Step 10 - Put Realm into action

Let's give whatever we have now a try. Modify `viewDidLoad` to look like this:

```swift
override func viewDidLoad() {
    super.viewDidLoad()
    let env = ApiEnvironment.production
    let context = NonPersistentApiContext(environment: env)
    let baseService = AlamofireMovieService(context: context)
    let service = RealmMovieService(baseService: baseService)
    var invokeCount = 0
    service.getTopGrossingMovies(year: 2016) { (movies, error) in
        invokeCount += 1
        if let error = error {
            print("ERROR: \(error.localizedDescription)")
        } else {
            print("Found \(movies.count) movies (callback #\(invokeCount))")
        }
    }
}
```

In the code above, we rename the Alamofire service to `baseService`, then create
a Realm service into which we inject `baseService`. The app is still loading top
grossing movies using a `service`, but now it will first check the database then
call the api. However, the app does not care about this. It only cares about the
protocol, not how it is implemented (in the code above, it actually DOES know of
the concrete implementations, but we'll fix that later).

The output will be the following, the first time we run the app with this setup:

```
Found 0 movies  (callback #1)
Found 10 movies (callback #2)
```

This happens because the database has no data, while the api will load 10 movies.
If you run the app again, the output should be:

```
Found 10 movies (callback #1)
Found 10 movies (callback #2)
```

This happens because the database now has data, which means that both completions
return movies.

Now, kill your Internet connection and call `getTopRatedMovies` instead of
`getTopGrossingMovies` (Alamofire will cache the previous result). If you run the
app again, the output should be:

```
Found 10 movies (callback #1)
ERROR: The Internet connection appears to be offline.
```

This happens because the database data can still be loaded, while the api can't
be called since the Internet connection is dead.

We now have an app with offline support, that only refreshes its data whenever a
call to the api provides new data. All we had to do was change two lines that
determine which service implementation we use.


## Step 11 - Retry failing requests

In the real world, a user most often has to authenticate her/himself in order to
use some parts of an api. Authentication often returns a set of tokens, commonly
an `auth token` and a `refresh token` (but how this works is up to the api).

If the `auth token` and `refresh token` pattern is used, authentication could
look something like this:

 * If no tokens are available and a request fails with an HTTP 401, the user may
 have to login (if the request is mandatory). If so, show a login screen/prompt.

 * If tokens are available, the app should request the api with the `auth` token.

 * If an `auth` token-based request fails with an HTTP 401, the `auth` token has
 most probably expired. The app should then save any requests that fail with 401
 and automatically use the `refresh` token to request new tokens from the api.

 * If the refresh request succeeds, the app should parse the new tokens from the
 response and retry any failed requests with these new tokens. It should use the
 new tokens from now on.

 * If the refresh request fails, the app should delete all tokens and logout the
 user. If the app requires a logged in user, the app should show a login screen.

Alamofire 4 makes this kind of logic easy to implement, since it has a 
`RequestRetrier` protocol that we can implement and inject into Alamofire. The
retrier will be notified about all failing requests, and lets you determine if a
request should be retried or not.

We will demonstrate this by faking a failing auth. First, add an `auth` route to
`ApiRoute`, and have it return `auth` as path. Our static api will always return
the same "auth token" when this route is called.

Second, add this file to `Domain/Services`:

```swift
// AuthService.swift

import Foundation

typealias AuthResult = (_ token: String?, _ error: Error?) -> ()

protocol AuthService: class {

    func authorizeApplication(completion: @escaping AuthResult)
}
``` 

This is a really simple protocol that defines how the app is to authorize itself.
Before we implement it, we have to add a way to store any auth tokens we receive.

`Disclaimer:` As an app grows, I find it easier to separate the app into context
related parts instead of class types. In other words, instead of the `Model` and
`Services` grouping, the app should be grouped into `Movies` and `Authentication`
groups instead.

Ok, back to storing auth tokens. Remember what I told you about the `ApiContext`
earlier? Well, it's a perfect place to store tokens, so let's do that. 

First, add an `authToken` property to the `ApiContext` protocol:

```swift
var authToken: String? { get set }
```

Make sure to add this property to `NonPersistentApiContext` as well:

```swift
var authToken: String?
```

Now, let's add an Alamofire-based `AuthService` implementation to `Api/Services`:


```swift
// AlamofireAuthService.swift

import Alamofire
import AlamofireObjectMapper

class AlamofireAuthService: AlamofireService, AuthService {
    
    func authorizeApplication(completion: @escaping AuthResult) {
        get(at: .auth).responseString { [weak self] (res: DataResponse<String>) in
            if let token = res.result.value {
                self?.context.authToken = token
            }
            completion(res.result.value, res.result.error)
        }
    }
}
``` 

If the request above succeeds, the token will be saved in our api context, which
makes it available to all future api requests.

Now, let's retry some requests! Add this file to the `Api` folder:

```swift
import Alamofire

class ApiRequestRetrier: RequestRetrier {
    
    init(context: ApiContext, authService: AuthService) {
        self.context = context
        self.authService = authService
    }
    
    
    private let authService: AuthService
    private let context: ApiContext
    private var isAuthorizing = false
    private var retryQueue = [RequestRetryCompletion]()
    
    
    func should(
        _ manager: SessionManager,
        retry request: Request,
        with error: Error,
        completion: @escaping RequestRetryCompletion) {
        
        guard
            shouldRetryRequest(with: request.request?.url),
            shouldRetryResponse(with: request.response?.statusCode)
            else { return completion(false, 0) }
        
        authorize(with: completion)
    }
    
    
    private func authorize(with completion: @escaping RequestRetryCompletion) {
        print("Authorizing application...")
        retryQueue.append(completion)
        guard !isAuthorizing else { return }
        isAuthorizing = true
        authService.authorizeApplication { (token, error) in
            self.printAuthResult(token, error)
            self.isAuthorizing = false
            self.context.authToken = token
            let success = token != nil
            self.retryQueue.forEach { $0(success, 0) }
            self.retryQueue.removeAll()
        }
    }

    private func printAuthResult(_ token: String?, _ error: Error?) {
        if let error = error {
            return print("Authorizing failed: \(error.localizedDescription)")
        } 
        if let token = token {
            return print("Authorizing succeded: \(token)")
        } 
        print("No token received - failing!")
    }

    private func shouldRetryRequest(with url: URL?) -> Bool {
        guard let url = url?.absoluteString else { return false }
        let authPath = ApiRoute.auth.path
        return !url.contains(authPath)
    }
    
    private func shouldRetryResponse(with statusCode: Int?) -> Bool {
        return true // statusCode == 401
    }
}

```

When a request fails, Alamofire will ask the retrier if it should be retried. The
retrier will trigger a retry if the request isn't a failing auth (read more about
the commented out 401 later). If not, it will just let the request fail, as it should.

If a request should be retried, it's added it to a retry queue. The retrier then
triggers an authorization. Once it completes, the retrier checks if it succeeded.
If so, all queued requests are retried. If not, all queued requests fail and the
retry queue is cleared.

Note that this is completely hidden from the user as well as the app itself. The
retrier works under the hood, tightly connected to Alamofire's internal workings.
It just notifies the app if the authorization fails, by failing all requests.

Inject the retrier into `Alamofire` by adding the following to our `viewDidLoad`
(note that you have to add `import Alamofire` topmost as well):

```
let manager = SessionManager.default
manager.retrier = ApiRequestRetrier(context: context, authService: authService)
```

In the real world, a 401 status code is an indication that tokens should be 
refreshed. If this refresh fails, a 401 indicates that the user has to log in, 
since the tokens are invalid. Here, however, we will never get a 401. We
therefore have to trigger these mechanisms by doing the following:

 * Kill your connection and perform a clean install, to remove all stored data.
 * Add a breakpoint to the retrier's `authService.authorizeApplication` call.
 * Run the app. The app should now fail the request and activate this breakpoint.
 * Bring the connection back online and resume the app.
 * This should make the auth request succeed and have Alamofire retry the request.

That's it! Alamofire should now retry any failing request that are not auth ones.


## Step 12 - Adapt all Alamofire requests

Sometimes, you have to add custom headers to every request you make to an api. A
common scenario is to add `Accept` information, auth tokens etc.

To adapt Alamofire requests before they are sent, you just have to implement the
`RequestAdapter` protocol and inject it into Alamofire. Add this file to the `Api`
folder:

```swift
// ApiRequestAdapter.swift

import Alamofire

class ApiRequestAdapter: RequestAdapter {
    
    public init(context: ApiContext) {
        self.context = context
    }
    
    private let context: ApiContext
    
    func adapt(_ request: URLRequest) throws -> URLRequest {
        guard let token = context.authToken else { return request }
        var request = request
        request.setValue(token, forHTTPHeaderField: "AUTH_TOKEN")
        return request
    }
}
```

As you can see, the adapter just adds any existing token to the request header.
You can then inject the adapter into `Alamofire` by adding the following to
`viewDidLoad`:

```swift
manager.adapter = ApiRequestAdapter(context: context)
```

That's it! Alamofire should now add the auth token to all requests, if it exists.


## Step 13 - Dependency Injection

I won't do this here, since it just add even more complexity to an already long
tutorial. In the demo app, however, I have an `IoC` folder in which I use a
library called [Dip](https://github.com/AliSoftware/Dip) to manage app dependencies.

With Dip in place, the view controller becomes a lot cleaner, with the benefit that
the view controller no longer knows anything about the implementations used in the app:

```swift
import UIKit
import Alamofire

class ViewController: UIViewController {

    override func viewDidLoad() {
        super.viewDidLoad()
        reloadData(self)
    }
    
    
    lazy var movieService: MovieService = IoC.resolve()
    
    
    private var movies = [Movie]()
    
    
    @IBOutlet weak var tableView: UITableView? {
        didSet {
            tableView?.delegate = self
            tableView?.dataSource = self
        }
    }
    
    @IBOutlet weak var dataPicker: UISegmentedControl?
    
    
    @IBAction func reloadData(_ sender: Any) {
        let index = dataPicker?.selectedSegmentIndex ?? 0
        index == 0
            ? movieService.getTopGrossingMovies(year: 2016, completion: moviesCompletion)
            : movieService.getTopRatedMovies(year: 2016, completion: moviesCompletion)
    }
    
    private func moviesCompletion(_ movies: [Movie], _ error: Error?) {
        if let error = error { fatalError(error.localizedDescription) }
        self.movies = movies
        self.tableView?.reloadData()
    }
}

extension ViewController: UITableViewDataSource, UITableViewDelegate {
    
    func numberOfSections(in tableView: UITableView) -> Int {
        return 1
    }
    
    func tableView(_ tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return movies.count
    }
    
    func tableView(_ tableView: UITableView, cellForRowAt indexPath: IndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCell(withIdentifier: "Cell", for: indexPath)
        let movie = movies[indexPath.row]
        let names = movie.cast.map { $0.name }
        cell.textLabel?.text = "\(movie.name) (\(movie.year))"
        cell.detailTextLabel?.text = names.joined(separator: ", ")
        return cell
    }
}
```

The result looks like this:

![Image](/assets/blog/2015/2015-08-23-app.png)

And...


## That's a wrap!

Well done! You have created an app with abstract protocols, then added Alamofire,
object mapping and Realm to the mix. You have also added request retry and adapt
logic using the `RequestRetrier` and `RequestAdapter` protocols. Wow!

I hope this was helpful. Don't hesistate to throw your thoughts and ideas at me.
Hit me up on Twitter at [@danielsaidi](http://twitter.com/danielsaidi), if you want
to discuss this further.