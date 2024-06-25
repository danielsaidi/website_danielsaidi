---
title:  Storing Codable types in SwiftUI AppStorage
date:   2023-08-23 06:00:00 +0000
tags:   swiftui

redirect_from: /blog/2023/08/23/storagecodable

assets: /assets/blog/23/0823/
image:  /assets/blog/23/0823.jpg
image-show: 0

post:   https://nilcoalescing.com/blog/SaveCustomCodableTypesInAppStorageOrSceneStorage/
---

SwiftUI keeps evolving, but there are still some missing things. Today, let's see how we can extend `Codable` to make it possible to persist it in `AppStorage` and `SceneStorage`.

{% include kankoda/data/open-source.html name="SwiftUIKit" version="3.6.0" %}

The inspiration to the `StorageCodable` protocol in this post comes from [this article]({{page.post}}), where Natalia uses the same approach to extend all codable `Arrays` and `Dictionaries`.


## The basic problem

Let's say that you have a `Codable` `User` struct:

```swift
struct User: Codable {

    let name: String
    let age: Int
}
```

Although this type can automatically be encoded and decoded in various ways, it can't be persisted in `AppStorage` or `SceneStorage`. This means that you can't do this:

```swift
struct MyView: View {

    @AppStorage("com.myapp.user")
    var user: User?

    var body: some View {
        Text(user?.name)
    }
}
```

One way that we can make this work, is to take a look at the `RawRepresentable` protocol.


## The inspiration to this solution

In her [article]({{page.post}}), Natalia makes arrays and dictionaries that contain `Codable` types implement the `RawRepresentable` protocol with these array and dictionary extensions:

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

This makes it possible to use arrays and dictionaries with `AppStorage` and `SceneStorage`:

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

This will make SwiftUI complain, since there's no `AppStorage` support for a single `Codable`.


## How to make Codable support AppStorage & SceneStorage

Since [Natalia's approach]({{page.post}}) is based on `Codable`, we could fix this by extending `Codable`:

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

However, since not all `Codable` types may prefer JSON, I created a separate protocol that extends `Codable` and implements `RawRepresentable` with the JSON code from above:

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

One important thing to keep in mind, is that JSON coding may affect values. For instance, JSON encoding a dynamic `Color` to raw data will remove light & dark mode support.


## Conclusion

SwiftUI is becoming more capable every year, but we still have to write custom code for some things, such as making `Codable` work with SwiftUI's persistency stores. 

The `StorageCodable` protocol in this post makes it possible to persist codable types with `AppStorage` and `SceneStorage`, using JSON for encoding and decoding. 

`StorageCodable` is available in my [SwiftUIKit]({{project-version}}) open-source project. I hope that you like it.