---
title:  Store Codable types in AppStorage
date:   2023-08-23 06:00:00 +0000
tags:   swiftui

icon:   swiftui

post:   https://nilcoalescing.com/blog/SaveCustomCodableTypesInAppStorageOrSceneStorage/
---

SwiftUI keeps evolving, but there are still some things that we have to write custom code for. Today, let's see how we can extend `Codable` to make it possible to persist it in `AppStorage` and `SceneStorage`.

{% include kankoda/data/open-source.html name="SwiftUIKit" version="3.6.0" %}

The inspiration to the `StorageCodable` protocol presented in this post came from [this article]({{page.post}}), where Natalia Panferova uses the same approach to extend all codable `Arrays` and `Dictionaries`.

Let's say that you have the following codable `User` struct:

```swift
struct User: Codable {

    let name: String
    let age: Int
}
```

Although you this type can automatically be encoded and decoded in various ways, it can't be persisted in `AppStorage` or `SceneStorage`. This means that you can't do this:

```swift
struct MyView: View {

    @AppStorage("com.myapp.user")
    var user: User?

    var body: some View {
        Text(user?.name)
    }
}
```

Let's see how we can use `RawRepresentable` to make this possible.


## The inspiration to this solution

In her [article]({{page.post}}), Natalia uses the `RawRepresentable` support that was added in iOS 15, by making arrays and dictionaries that contain `Codable` types implement the protocol:

```swift
extension Array: RawRepresentable where Element: Codable {

    public init?(rawValue: String) {
        guard
            let data = rawValue.data(using: .utf8),
            let result = try? JSONDecoder().decode([Element].self, from: data)
        else { return nil }
        self = result
    }

    public var rawValue: String {
        guard
            let data = try? JSONEncoder().encode(self),
            let result = String(data: data, encoding: .utf8)
        else { return "" }
        return result
    }
}

extension Dictionary: RawRepresentable where Key: Codable, Value: Codable {

    public init?(rawValue: String) {
        guard
            let data = rawValue.data(using: .utf8),
            let result = try? JSONDecoder().decode([Key: Value].self, from: data)
        else { return nil }
        self = result
    }

    public var rawValue: String {
        guard
            let data = try? JSONEncoder().encode(self),
            let result = String(data: data, encoding: .utf8)
        else { return "{}" }
        return result
    }
}
```

This makes it possible to use codable arrays and dictionaries with `AppStorage` and `SceneStorage`:

```swift
struct MyView: View {

    @AppStorage("com.myapp.users")
    var users: [User] = []

    var body: some View {
        Text(users.first?.name)
    }
}
```

However, since the extensions only apply to arrays and dictionaries, we still can't do this:

```swift
struct MyView: View {

    @AppStorage("com.myapp.user")
    var user: User?

    var body: some View {
        Text(user?.name)
    }
}
```

This will make SwiftUI complain that it doesn't understand what you're trying to do, since there is no `AppStorage` implementation that takes a `Codable` type, while there is one for `RawRepresentable`.


## How to make Codable support AppStorage and SceneStorage

Since [Natalia's approach]({{page.post}}) is based on `Codable`, we could fix this by extending `Codable` like this:

```swift
extension Codable: RawRepresentable {
    
    init?(rawValue: String) {
        guard
            let data = rawValue.data(using: .utf8),
            let result = try? JSONDecoder().decode(Self.self, from: data)
        else { return nil }
        self = result
    }

    var rawValue: String {
        guard
            let data = try? JSONEncoder().encode(self),
            let result = String(data: data, encoding: .utf8)
        else { return "" }
        return result
    }
}
```

However, not all `Codable` types may prefer to use JSON, I've added a separate protocol for this:

```swift
public protocol StorageCodable: Codable, RawRepresentable {}

public extension StorageCodable {
    
    init?(rawValue: String) {
        guard
            let data = rawValue.data(using: .utf8),
            let result = try? JSONDecoder().decode(Self.self, from: data)
        else { return nil }
        self = result
    }

    var rawValue: String {
        guard
            let data = try? JSONEncoder().encode(self),
            let result = String(data: data, encoding: .utf8)
        else { return "" }
        return result
    }
}
```

All you have to do now, is to make `User` implement `StorageCodable` instead of `Codable`:

```swift
struct User: StorageCodable {

    let name: String
    let age: Int
}
```

With this small change, this will now finally work:

```swift
struct MyView: View {

    @AppStorage("com.myapp.user")
    var user: User?

    var body: some View {
        Text(user?.name)
    }
}
```

One important thing to keep in mind with this approach, is that JSON encoding may affect the encoded values. For instance, JSON encoding a dynamic `Color` value to a raw data representation, could remove any light and dark mode support from the color when it's decoded.


## Conclusion

SwiftUI is becoming more and more capable, but we still have to write custom code for some things, such as making `Codable` work with SwiftUI's persistency stores. 

The `StorageCodable` protocol that was presented in this post makes it possible to persist any type in `AppStorage` and `SceneStorage`, using JSON for encoding and decoding. 

`StorageCodable` is available in the brand new [SwiftUIKit 3.6]({{project-version}}) release. I hope that you find it useful.