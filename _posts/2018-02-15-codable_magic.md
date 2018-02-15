---
title:  Codable magic
date:   2018-02-15 09:00:00 +0100
tags:	ios swift
---


It's been a long time coming, but I eventually got around to start replacing all
`NSCoding` objects in a Swift library with the (not so) new `Codable` protocol.
This blog post will cover some learnings I've collected along the way.

The protocol and implementations in this blog post are fictional representations
of the real world domain model that I work with.


## The protocol

In our code base, say that we have a protocol that looks something like this:

```swift
public protocol Movie {
    
    var id: Int { get }
    var name: String { get }
    var releaseDate: Date { get }
    var genre: MovieGenre { get }
}
```

where `MovieGenre` is an enum, as such:

```swift
public enum MovieGenre: String { case
    
    action,
    drama,
    ...
}
```

`Movie` is a very central model in the domain, and any implementations we have
should be cachable, serializable etc. and should be covered by unit tests.


## Old `NSCoding` implementation

Before `Codable`, the `StandardMovie` implementation we had could be serialized,
cached etc. since it implemented `NSCoding`:

```swift
import Foundation

public class StandardMovie: NSObject, Movie, NSCoding {

    
    // MARK: - Initialization

    // ...more initializers
    
    public required init?(coder: NSCoder) {
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
        super.init()
    }
    
    
    // MARK: - Mapping Keys
    
    fileprivate let idKey = "id"
    fileprivate let nameKey = "name"
    fileprivate let releaseDateKey = "releaseDate"
    fileprivate let genreKey = "genre"
    
    
    // MARK: - Properties
    
    public var id: Int
    public var name: String
    public var releaseDate: Date
    public var genre: MovieGenre
    
    
    // MARK: - Public Functions
    
    public func encode(with coder: NSCoder) {
        coder.encode(NSNumber(value: id), forKey: idKey)
        coder.encode(name, forKey: nameKey)
        coder.encode(releaseDate, forKey: releaseDateKey)
        coder.encode(genre.rawValue, forKey: genreKey)
    }
}
```

As you can see, we inherit `NSObject` and have to implement a bunch of encoding
and decoding. What would this look like if we used the `Codable` protocol instead?


## New `Codable` implementation

As I switched out the implementation to `Codable`, I first tried to just smack
on a `Codable` protocol to a new `Movie` type, like this:

```swift
import Foundation

public class CodableMovie: Movie, Codable {

    
    // MARK: - Properties
    
    public var id: Int
    public var name: String
    public var releaseDate: Date
    public var genre: MovieGenre
}
```

But the compiler now complained that `Decodable` was not implemented. I thus
added a bunch of encoding/decoding code to this new class, which ended up
looking a bit like this:


```swift
import Foundation

public class CodableMovie: Movie, Codable {


    // MARK: - Initialization

    // ...more initializers

    public required init(from decoder: Decoder) throws {
        let values = try decoder.container(keyedBy: CodingKeys.self)
        id = try values.decode(Int.self, forKey: .id)
        name = try values.decodeIfPresent(String.self, forKey: .name)
        releaseDate = try values.decode(Date.self, forKey: .releaseDate)
        genre = try values.decode(BookFormat.self, forKey: .genre)
    }


    // MARK: - Enums

    private enum CodingKeys: String, CodingKey { case
        id,
        name,
        releaseDate,
        genre
    }

    
    // MARK: - Properties
    
    public var id: Int
    public var name: String
    public var releaseDate: Date
    public var genre: MovieGenre
}


// MARK: - Encodable

extension StandardMovie {
    
    public func encode(to encoder: Encoder) throws {
        var container = encoder.container(keyedBy: CodingKeys.self)
        try container.encode(id, forKey: .id)
        try container.encode(name, forKey: .name)
        try container.encode(releaseDate, forKey: .releaseDate)
        try container.encode(genre, forKey: .genre)
    }
}
```

Keep in mind that this is just a example model. My real world model contains a lot
more properties, nested types etc. The `Codable` approach ended up looking a lot
like the old `NSCoding` implementation, with the biggest difference being using a
key enum instead of strings (which I could have used in my old class as well).


However, as I did this, I also made `MovieGenre` codable, by just adding `Codable`
after the `String`, like this:

```swift
public enum MovieGenre: String, Codable { case
    
    ...
}
``` 

This would later turn out to solve a lot of problems, but I did not know that
as I now begun writing tests to cover all my manually written encode/decode logic.


## A big discovery

As I mentioned, my real world model is a lot more complex, and contains nested
objects and a lot more logic. Since I want to be able to easily serialize the
entire object tree, I quickly realized that all nested models must implement
`Codable` as well.

However, since my domain model is protocol-driven, the `Movie` protocol will
contain nested objects and arrays of other protocols. This means that `Codable`
should not be added to `StandardMovie`, but rather to the `Movie` protocol
and all other domain model protocols.

As I now moved `Codable` to `Movie` and removed it from `StandardMovie`,
I had a strange feeling that something was wrong. `StandardMovie` is just one
of many `Movie` implementations, and I only implemented `Codable` for this
class. Surely, this would mean that the old error I received for not implementing
`Codable` would now arise for all other implementations as well, right?

Strangely, this was not the case. I tried encoding and decoding `ApiMovie` and
it worked great, without any additional code to handle encoding and decoding.

What the fudge is going on?


## A big realization

Then it hit me. By adding `Codable` to `MovieGenre`, all `Movie` properties
were now `Codable`, which was why I no longer got an error telling me to manually
implement `Codable`.

So, did this also mean that I could remove all my manually added code from the
`StandardMovie` class. 

Yes, yes it did. I removed all manual encode/decode code and ended up with a
tiny end result, that looked like this:


```swift
import Foundation

public class StandardMovie: Movie {
    
    
    // MARK: - Initialization
    
    // ...a couple of initializers
    
    
    // MARK: - Properties
    
    public var id: Int
    public var name: String
    public var releaseDate: Date
    public var genre: MovieGenre
}

```

That's it! My unit tests that covered the encoding/decoding logic still worked
and some of my grey hairs started reverting to black.


## Conclusion

As long as your entire model is `Codable`, things seem to sort themselves out.
This is a drastic change to the old `NSCoding` protocol. I can't wait to apply
this to the entire domain model.

Thanks for reading.

