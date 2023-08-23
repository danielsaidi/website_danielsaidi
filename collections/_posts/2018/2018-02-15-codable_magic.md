---
title: Codable magic
date:  2018-02-15 09:00:00 +0100
tags:  swift codable
icon:  swift
---

I have finally started replacing all `NSCoding` objects in my code with the new `Codable` protocol. This article covers things that I've learned along the way.

The models in this article are fictional representations of a real world domain model. If they seem too simple or if there are bugs, it's because the code was only written for this post.


## Model

Lets say that we have `Movie` and `MovieGenre` structs that look like this:

```swift
struct Movie {
    
    let id: Int
    let name: String
    let releaseDate: Date
    let genre: MovieGenre
}

enum MovieGenre: String {
    
    case action
    case drama
    ...
}
```

We may want to use these structs in various ways, e.g. serializing and deserializing them. Let's compare how this is traditionally done with `NSCoding` and how `Codable` can simplify it.


## NSCoding

With `NSCoding`, you just let the type you want to serialize implement the `NSCoding` protocol. However, this requires the type to be a class and not a struct:

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

In the code above, we inherit `NSObject` and implement our codable logic, which involves a lot of code, strings, etc., This has to be repeated for every codable type, which is tedious and error-prone. 

Let's take a look at how `Codable` can make this a lot leaner and safer.


## Codable

With `Codable`, you just let the type you want to serialize implement the `Codable` protocol instead. `Codable` doesn't require you to use classes, so our types can keep being structs:

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

If you're new `Codable`, you would perhaps throw code at the problem to make the `Movie` codable:


```swift
import Foundation

struct Movie: Codable {

    required init(from decoder: Decoder) throws {
        let values = try decoder.container(keyedBy: CodingKeys.self)
        id = try values.decode(Int.self, forKey: .id)
        name = try values.decodeIfPresent(String.self, forKey: .name)
        releaseDate = try values.decode(Date.self, forKey: .releaseDate)
        genre = try values.decode(BookFormat.self, forKey: .genre)
    }

    func encode(to encoder: Encoder) throws {
        var container = encoder.container(keyedBy: CodingKeys.self)
        try container.encode(id, forKey: .id)
        try container.encode(name, forKey: .name)
        try container.encode(releaseDate, forKey: .releaseDate)
        try container.encode(genre, forKey: .genre)
    }

    private enum CodingKeys: String, CodingKey { case
        id,
        name,
        releaseDate,
        genre
    }
    
    var id: Int
    var name: String
    var releaseDate: Date
    var genre: MovieGenre
}
```

This is however not needed! If you look at the error message, the only reason why `Movie` isn't correctly implementing the protocol is because `MovieGenre` isn't.

All we have to do to fix the error is to make `MovieGenre` implement `Codable` too, which in this case is easily done by just adding `Codable` to the type:

```swift
enum MovieGenre: String, Codable { 
    
    case action
    case drama
    ...
}
```

This is a simple model, but even if your models contain a lot more stuff, and maybe property types that don't conform to `Codable`, this is basically how you do it.

If your model contains non-codable types, you can either change the model or implement the `Codable` protocol for your non-codable types. For instance, a `Color` can extract its RGBA data.

Happy encoding!