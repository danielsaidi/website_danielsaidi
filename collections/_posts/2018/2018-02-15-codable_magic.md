---
title: Codable magic
date:  2018-02-15 09:00:00 +0100
tags:  ios swift
---

I have finally started replacing all `NSCoding` objects in my code with `Codable`.
This post covers things that I've learned along the way.


## Disclaimer

The models in this post are fictional representations of a real world domain model
of mine. If they seem too simple or if there are bugs, it's because the code was
only written for this post.


## Model

Lets say that our app has `Movie` and `MovieGenre` structs that look like this:

```swift
struct Movie {
    
    let id: Int
    let name: String
    let releaseDate: Date
    let genre: MovieGenre
}

enum MovieGenre: String { case
    
    action,
    drama,
    ...
}
```

If these structs are central to our app, we probably want to use them in various
ways, e.g. serializing and deserializing them. Let's have a look at how we built
this functionality before, using `NSCoding`:


## NSCoding

Before `Codable`, you would let `Movie` implement `NSCoding`. However, that would
have required it to be a class   and not a struct:

```swift
import Foundation

class Movie: NSObject, NSCoding {

    
    // MARK: - Initialization

    // ...more initializers
    
    required init?(coder: NSCoder) {
        guard
            let id = coder.decodeObject(forKey: idKey) as? Int,
            let name = coder.decodeObject(forKey: nameKey) as? String,
            let date = coder.decodeObject(forKey: releaseDateKey) as? Date,
            let _genre = coder.decodeObject(forKey: genreKey) as? String,
            let genre = MovieGenre(rawValue: _genre)
            else { return nil }
        self.id = id
        self.name = name
        self.releaseDate = date
        self.genre = genre
    }
    
    
    // MARK: - Mapping Keys
    
    private let idKey = "id"
    private let nameKey = "name"
    private let releaseDateKey = "releaseDate"
    private let genreKey = "genre"
    
    
    // MARK: - Properties
    
    let id: Int
    let name: String
    let releaseDate: Date
    let genre: MovieGenre
    
    
    // MARK: - Public Functions
    
    func encode(with coder: NSCoder) {
        coder.encode(NSNumber(value: id), forKey: idKey)
        coder.encode(name, forKey: nameKey)
        coder.encode(releaseDate, forKey: releaseDateKey)
        coder.encode(genre.rawValue, forKey: genreKey)
    }
}
```

As you see, we inherit `NSObject` and implement a bunch of encoding/decoding
logic. This is tedious, especially if you have nested types. Let's take a
look at how `Codable` can make this a lot leaner.


## Codable

`Codable` doesn't require you to use classes. `Movie` can be a struct and still
implement `Codable`:

```swift
import Foundation

struct Movie: Codable {

    let id: Int
    let name: String
    let releaseDate: Date
    let genre: MovieGenre
}
```

With the code above, however, the compiler will complain that `Movie` doesn't
implement `Decodable`.

If you're new to using `Codable`, you can easily end up adding a bunch of code
to make it conform to `Codable`, for instance:


```swift
import Foundation

struct Movie: Codable {


    // MARK: - Initialization

    // ...more initializers

    required init(from decoder: Decoder) throws {
        let values = try decoder.container(keyedBy: CodingKeys.self)
        id = try values.decode(Int.self, forKey: .id)
        name = try values.decodeIfPresent(String.self, forKey: .name)
        releaseDate = try values.decode(Date.self, forKey: .releaseDate)
        genre = try values.decode(BookFormat.self, forKey: .genre)
    }


    // MARK: - Encoding

    func encode(to encoder: Encoder) throws {
        var container = encoder.container(keyedBy: CodingKeys.self)
        try container.encode(id, forKey: .id)
        try container.encode(name, forKey: .name)
        try container.encode(releaseDate, forKey: .releaseDate)
        try container.encode(genre, forKey: .genre)
    }


    // MARK: - Enums

    private enum CodingKeys: String, CodingKey { case
        id,
        name,
        releaseDate,
        genre
    }

    
    // MARK: - Properties
    
    var id: Int
    var name: String
    var releaseDate: Date
    var genre: MovieGenre
}
```

Keep in mind that this is a simple model, and that your real world models would
probably contain a lot more properties, nested types etc. Using the approach
above, the `Codable` approach would end up a lot like the `NSCoding`
implementation, with the big difference of using enum keys instead of strings.

So clearly, this approach should be used very seldom, if ever, and only when you
know exactly what you're doing and as a conscious choice. 

Instead, the correct way of fixing the problem is to make the `MovieGenre`  model
`Codable` as well:

```swift
enum MovieGenre: String, Codable {
    
    ...
}
``` 

If you do this, you don't have to add any additional code to `Movie`. Everything
will work right away! I removed all additional code and my unit tests that cover
encoding/decoding still worked.


## Conclusion

As long as your entire model is `Codable`, things seem to sort themselves out. I
love this new approach and can't wait for the Swift community to embrace it.