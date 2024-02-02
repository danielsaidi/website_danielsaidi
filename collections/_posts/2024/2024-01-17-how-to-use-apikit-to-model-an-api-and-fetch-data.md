---
title:  How to use ApiKit to model an API and fetch data from it
date:   2024-01-17 06:00:00 +0000
tags:   swift api

image:  /assets/blog/2024/240117/header.jpg

tweet:  https://x.com/danielsaidi/status/1747726878868209739?s=20
toot:   https://mastodon.social/@danielsaidi/111773321977694317
---

{% include kankoda/data/open-source.html name="ApiKit" %}
In this post, we'll take a look at how to use the open-source [{{project.name}}]({{project.url}}) to model a REST-based API and fetch data from it.

![Header image]({{page.image}})

ApiKit builds on the basic concept of environments and routes and provides lightweight types that make it easy to integrate with any REST-based APIs.

ApiKit has lightweight ``ApiEnvironment`` and ``ApiRoute`` protocols that make it easy to model any REST-based API. It also has an ``ApiRequest`` that can define a route and response type, for even easier use.

Once you have an environment and routes, you can use a regular `URLSession` or a custom ``ApiClient`` to fetch any route or request from any environment.

## API environments

An ``ApiEnvironment`` refers to a specific API version or environment (prod, staging, etc.), and defines a URL as well as global request headers and query parameters.

For instance, this is how to model the [Yelp](https://yelp.com) v3 API environment, which requires an API token:

```swift
import ApiKit

enum YelpEnvironment: ApiEnvironment {

    case v3(apiToken: String)
    
    var url: String {
        switch self {
        case .v3: "https://api.yelp.com/v3/"
        }
    }
 
    var headers: [String: String]? {
        switch self {
        case .v3(let token): ["Authorization": "Bearer \(token)"]
        }
    }
    
    var queryParams: [String: String]? {
        [:]
    }
}
```

The Yelp API requires that all requests send the API token as a custom header. Other APIs may require it to be sent as a query parameter, or have no token requirements at all. 

ApiKit is flexible and supports many different requirements.


## API routes

An ``ApiRoute`` refers to an endpoint within an API. It defines an HTTP method, an environment-relative path, custom headers, query parameters, post data, etc.

For instance, this is a [Yelp](https://yelp.com) v3 API route, which defines how to fetch and search for restaurants:

```swift
import ApiKit

enum YelpRoute: ApiRoute {

    case restaurant(id: String)
    case search(params: Yelp.SearchParams)

    var path: String {
        switch self {
        case .restaurant(let id): "businesses/\(id)"
        case .search: "businesses/search"
        }
    }

    var httpMethod: HttpMethod { .get }

    var headers: [String: String]? { nil }

    var formParams: [String: String]? { nil }

    var postData: Data? { nil }
    
    var queryParams: [String: String]? {
        switch self {
        case .restaurant: nil
        case .search(let params): params.queryParams
        }
    }
}
```

The routes above use associated values to provide item IDs to the path and search parameters as query parameters. Route properties will complete or replace the environment's properties.


## API models

We also have to define `Codable` Yelp-specific models to be able to map data from the API. For instance, this is a super lightweight Yelp restaurant model:

```swift
struct YelpRestaurant: Codable {
    
    public let id: String
    public let name: String?
    public let imageUrl: String?
    
    enum CodingKeys: String, CodingKey {
        case id
        case name
        case imageUrl = "image_url"
    }
}
```

The `id` and `name` parameters use the same name as in the API, while `imageUrl` requires mapping.


## How to fetch data from an API

We can now fetch data from the Yelp API, using `URLSession` or any custom ``ApiClient``:

```swift
let client = URLSession.shared
let environment = YelpEnvironment.v3(apiToken: "TOKEN") 
let route = YelpRoute.restaurant(id: "abc123") 
let restaurant: YelpRestaurant = try await client.fetchItem(
    at: route, in: environment)
```

The client will fetch the raw data and either return the mapped result, or throw an error.


## How to fetch data even easier

We can also define a ``ApiRequest`` to avoid having to define routes and return types every time, and to reduce the risk of using an invalid return type:

```swift
struct YelpRestaurantRequest: ApiRequest {

    typealias ResponseType = YelpRestaurant

    let id: String

    var route: ApiRoute { 
        YelpRoute.restaurant(id: id)
    }
}
```

We can use `URLSession` or any custom ``ApiClient`` to fetch requests as well:

```swift
let client = URLSession.shared
let environment = YelpEnvironment.v3(apiToken: "TOKEN") 
let request = YelpRestaurantRequest(id: "abc123") 
let restaurant = try await client.fetch(
    at: request, in: environment)
```

As you can see, we don't have to define route and return type when we use requests. This however just provides extra convenience and is not required.


## Conclusion

[{{project.name}}]({{project.url}}) makes it easy to integrate with any REST-based API. The recently released 0.5 has new features that makes integrations even easier.

You can have a look at the built-in integrations and the demo app to give the library a try. I really hope you like it.