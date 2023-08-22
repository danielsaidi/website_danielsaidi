---
title:  Creating a property wrapper to persist Codable types
date:   2022-05-13 10:00:00 +0000
tags:   swiftui codable property-wrappers

icon:   swiftui
---

In this post, we'll take a quick look at how to create a property wrapper that can be used with `Codable`, that automatically persists its value in `UserDefaults` and updates SwiftUI when its value changes.

{% include kankoda/data/open-source.html name="SwiftUIKit" %}

SwiftUI provides us with two great property wrappers for persisting data for the entire app or for the current scene - `@AppStorage` and `@SceneStorage`:

```swift
struct MyView: View {

    @AppStorage("com.danielsaidi.demo.myInt")
    private var myInt: Int = 1

    @SceneStorage("com.danielsaidi.demo.myDouble")
    private var myDouble: Double = 1.0

    var body: some View {
        Button("\(myInt) \(myDouble)") {
            myInt += 1
            myDouble += 1
        }.buttonStyle(.bordered)
    }
}
```

If you run this code, you'll see how tapping the button updates the values, which automatically updates the view. Restarting the app restores `myInt`, which is persisted for the app, but will reset `myDouble`, since it's only persisted for the current scene.

These wrappers are easy to use, support optionals and automatically update your view. However, they don't (yet) support `Codable`, which may limit you if you want to persist more complex data.

We can define a new property wrapper that supports `Codable`, persists data in `UserDefaults` and updates SwiftUI whenever its value changes:

```swift
@propertyWrapper
public struct Persisted<Value: Codable>: DynamicProperty {

    public init(
        key: String,
        store: UserDefaults = .standard,
        defaultValue: Value) {
        self.key = key
        self.store = store
        let initialValue: Value? = Self.initialValue(for: key, in: store)
        self._value = State(initialValue: initialValue ?? defaultValue)
    }

    @State
    private var value: Value

    private let key: String
    private let store: UserDefaults

    public var wrappedValue: Value {
        get {
            value
        }
        nonmutating set {
            let data = try? JSONEncoder().encode(newValue)
            store.set(data, forKey: key)
            value = newValue
        }
    }
}

private extension Persisted {

    static func initialValue<Value: Codable>(
        for key: String,
        in store: UserDefaults
    ) -> Value? {
        guard let data = store.object(forKey: key) as? Data else { return nil }
        return try? JSONDecoder().decode(Value.self, from: data)
    }
}
```

This property wrapper implements `DynamicProperty` and takes a custom persistency key, a custom `UserDefaults` instance and a default value to apply when no value is persisted.

The property wrapper has a private `@State` property, which will trigger updates whenever it changes. It's initialized with either a previously persisted value or the provided default value.

The `wrappedValue` then brings it all together, by always returning `value`, but also persisting any new values into persistent storage.

Note how the setter is annotated with `nonmutating`. This is required if you want to edit state from your SwiftUI views or any other immutable type. Without it, you would get the following error:

```
Cannot assign to property: 'self' is immutable
```

This `Persisted` property now lets us handle plain values like ints, doubles, strings, bools etc. as well as more complex `Codable` types.

For instance, here we use the wrapper to persist a codable `User`:

```swift
struct User: Codable {

    var age: Int
}

struct ContentView: View {

    @Persisted(key: "value", defaultValue: User(age: 1))
    private var value: User

    var body: some View {
        Button("\(value.age)") {
            value = User(age: value.age + 1)
        }.buttonStyle(.bordered)
    }
}

struct ContentView_Previews: PreviewProvider {
    static var previews: some View {
        ContentView()
    }
}
```


## Conclusion

SwiftUI's `@AppStorage` and `@SceneStorage` are handly property wrappers for persisting data for the entire app or a specific scene. However, their inability to handle `Codable` types may be limiting. 

If so, the `Persisted` property wrapper that we implemented in this post can help. You can find it in the [SwiftUIKit]({{project.url}}) library. Feel free to try it out and let me know what you think.