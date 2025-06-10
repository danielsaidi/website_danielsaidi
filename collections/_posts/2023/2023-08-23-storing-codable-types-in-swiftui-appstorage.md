---
title:  Storing Codable types in SwiftUI AppStorage
date:   2023-08-23 06:00:00 +0000
tags:   swiftui

assets: /assets/blog/23/0823/
image:  /assets/blog/23/0823/image.jpg

post:   https://nilcoalescing.com/blog/SaveCustomCodableTypesInAppStorageOrSceneStorage/

bsky:   https://bsky.app/profile/danielsaidi.bsky.social/post/3lrasyvqsh223
toot:   https://mastodon.social/@danielsaidi/114658887293881071
---

SwiftUI keeps evolving, but there are still some missing things. Today, let's see how we can extend `Codable` to make it possible to persist it in `AppStorage` and `SceneStorage`.

{% include kankoda/data/open-source name="SwiftUIKit" version="3.6.0" %}

The inspiration to types in this post comes from [this article]({{page.post}}), where Natalia uses a similar approach to extend all codable `Arrays` and `Dictionaries`.


## Updated

This post was updated June 10, 2025, and now shows how to use the `StorageValue` instead of the old `StorageCodable` protocol, which stopped working in an earlier version of SwiftUI.


## The Problem

Let's say that you have a `Codable` `User` struct:

```swift
struct User: Codable {

    let name: String
    let age: Int
}
```

Although this type can automatically be encoded and decoded, it can't be used with `AppStorage` or `SceneStorage`. This means that you can't do this:

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


## The Inspiration

In her [article]({{page.post}}), Natalia makes arrays and dictionaries with `Codable` types implement `RawRepresentable` with these array and dictionary extensions:

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

However, since these extensions only apply to arrays and dictionaries, we still can't do this:

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


## The Solution

My first approach was to make all codable types automatically implement `RawRepresentable`, with an extension like this:

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

However, this stopped working at one point, where the type kept calling itself, which caused infinite recursions. So I now use a generic `StorageValue` instead:

```swift
public struct StorageValue<Value: Codable>: RawRepresentable {

    /// Create a storage value.
    public init(_ value: Value? = nil) {
        self.value = value
    }

    /// Create a storage value with a JSON encoded string.
    public init?(rawValue: String) {
        guard
            let data = rawValue.data(using: .utf8),
            let result = try? JSONDecoder().decode(Value.self, from: data)
        else { return nil }
        self = .init(result)
    }

    /// The stored value.
    public var value: Value?
}

public extension StorageValue {

    /// Whether the storage value contains an actual value.
    var hasValue: Bool {
        value != nil
    }

    /// A JSON string representation of the storage value.
    var jsonString: String {
        guard
            let data = try? JSONEncoder().encode(value),
            let result = String(data: data, encoding: .utf8)
        else { return "" }
        return result
    }

    /// A JSON string representation of the storage value.
    var rawValue: String {
        jsonString
    }
}
```

By having a separate type implementing `RawRepresentable`, we can get around the infinite recursion.

We can now persist any codable type in `AppStorage` or `SceneStorage` by wrapping it in `StorageValue`:

```swift
struct MyView: View {

    @AppStorage("com.myapp.user")
    var userValue = StorageValue<User>()

    var user: User? { userValue.value }

    var body: some View {
        Text(user?.name ?? "-")

        Button("Toggle user") {
            let hasValue = userValue.hasValue
            let daniel = User(name: "Daniel", age: 46)
            userValue.value = hasValue ? nil : daniel
        }
    }
}
```

An important thing to keep in mind is that JSON encoding dynamic values like `Color` to raw data may change the value. In the color case, storing the value will remove light & dark mode support.


## Conclusion

SwiftUI is becoming more capable every year, but we still have to do custom work for some things, such as making `Codable` work with SwiftUI's persistency stores. 

The `StorageValue` type in this post makes it possible to use any codable type with `AppStorage` and `SceneStorage`. It's available in my [SwiftUIKit]({{project.url}}) open-source project, if you want to give it a try.