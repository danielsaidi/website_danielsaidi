---
title:  Create custom environment types in SwiftUI with less code
date:   2024-04-14 04:00:00 +0000
tags:   swift swiftui open-source

assets: /assets/blog/2024/240414/
image:  /assets/blog/2024/240414/title.jpg

tweet:  https://x.com/danielsaidi/status/1779625421656535282
toot:   https://mastodon.social/@danielsaidi/112271734083721514

sdk:    https://github.com/danielsaidi/EnvironmentKit
---

In this post, I'll describe how I abused the Swift type system to come up with an approach to let us create custom SwiftUI environment values with less code than otherwise needed.


## Background

Consider that you have a `MyView` view, that has a `MyViewStyle` that can be used to style it:

```swift
struct MyView {

    let style: MyViewStyle

    var body: some View {
        style.color
    } 
}

struct MyViewStyle {

    var color: Color
}
``` 

One way to apply this style is to inject it with the initializer, with a default fallback value:

```swift
struct MyView {

    init(style: MyViewStyle = .standard) {
        self.style = style
    } 

    var style: MyViewStyle

    var body: some View {
        style.color
    } 
}

struct MyViewStyle {

    init(color: Color = .red) {
        self.color = color
    }

    var color: Color
    
    static var standard = Self()
}
```

However, init injection become complicated in more complex view hierarchies, where you may find yourself passing around values everywhere to get things to work.

Instead of init injection, SwiftUI provides a convenient way of injecting values into the view environment, after which views can use `@Environment` to access the injected value.

SwiftUI uses this convention extensively, for instance with the `.buttonStyle` view modifier, as well as many, many other styles and configurations that exist in the framework.

Environment injection is MUCH more flexible than injecting values into the initializer, since you can inject environment values into any part of the view hierarchy.

It's also nice to remove parameters from the initializer, since more complex views, as well as generic ones, can end up with complicated permutations if you have many parameters.


## How to define an environment value - the complicated way

To make it possible to inject a custom type into the view environment, you must extend the native `EnvironmentValues` type with a property that can get and set a value of that type.

To do this, you must also define a type that conforms to the `EnvironmentKey` protocol, and returns a `defaultValue` of your type.

Finally, it's nice to also provide a view modifier with the same name as the value you want to inject, much like `ButtonStyle` has a matching `.buttonStyle` modifier.

Apple provides boilerplate code for making this happen, here rewritten for `MyViewStyle`:

```swift
public extension MyStyle {
    
    static var standard = Self()
}

private extension MyStyle {

    struct Key: EnvironmentKey {
        static var defaultValue: MyStyle = .standard
    }
}

public extension EnvironmentValues {

    var myStyle: MyStyle {
        get { self[MyStyle.Key.self] }
        set { self[MyStyle.Key.self] = newValue }
    }
}

public extension View {

    func myStyle(_ style: MyStyle) -> some View {
        environment(\.myStyle, style)
    }
}
```

It's not much code, but imagine having many custom types, and for each you'd have to:

* Provide a default value.
* Define a `Key` type that returns the default value.
* Create an `EnvironmentValues` property, using the complicated subscript syntax.
* Provide a view extension.

I'd like this to be a little simpler, and have spent some time playing around with plain Swift and protocols to come up with a more streamlined approach. Let's take a look.


## How to define an environment value - the easy way

Instead of the boilerplate code above, I have created and published an open-source library called [EnvironmentKit]({{page.sdk}}), that lets you achieve the same result with this code:

```swift
struct MyStyle: EnvironmentValue {  
    
    static var keyPath: EnvironmentKeyPath { \.myStyle }    
}

extension EnvironmentValues {

    var myStyle: MyStyle {
        get { get() } set { set(newValue) }
    }
}

extension View {

    func myStyle(_ style: MyStyle) -> some View {
        environment(style)
    }
}
```

While you don't save that many lines of code, you avoid the repetitions of having to refer to the same type many times, the correct keypath, etc. All in all, I find it cleaner.

Everything is powever by having your type implementing the `EnvironmentValue` protocol, which provides EnvironmentKit with all it needs to resolve keys, keypaths, etc.

Let's see how it's implemented.


## Creating EnvironmentKit

I wanted a core protocol to power the library, and since SwiftUI has an `EnvironmentValues` type, I decided to call it `EnvironmentValue` (this naming will surely punish me in the future):

```swift
public protocol EnvironmentValue {}
```

To be able to resolve things automatically, the protocol will require its implementing types to provide a parameterless initializer, or default values for all properties:

```swift
public protocol EnvironmentValue {
    
    init()
}
```

With this initializer, the protocol can now provide a default value for all implementing types:

```swift
public extension EnvironmentValue {
    
    static var defaultValue: Self { .init() }
}
```

We can now automatically provide a key type for every value type, using the default value:

```swift
public protocol EnvironmentValue {
    
    init()
    
    typealias EnvironmentKey = EnvironmentValueKey<Self>
}

public struct EnvironmentValueKey<T: EnvironmentValue>: EnvironmentKey {
    
    public static var defaultValue: T { T.defaultValue }
}
```

We can now extend the native `EnvironmentValues` type with a getter and a setter that uses this key information to get and set values for any `EnvironmentValue`:

```swift
public extension EnvironmentValues {
    
    func get<T: EnvironmentValue>() -> T {
        self[T.EnvironmentKey.self]
    }
    
    mutating func set<T: EnvironmentValue>(_ newValue: T) {
        self[T.EnvironmentKey.self] = newValue
    }
}
```

This lets us avoid having to use the subscript syntax for every new value type. Instead, we can define custom environment value properties like this:

```swift
private extension EnvironmentValues {

    var myViewStyle: MyViewStyle {
        get { get() } set { set(newValue) }
    }
}
```

To avoid having to specify a keypath when using an environment value type, we can force each type to specify its keypath:

```swift
public protocol EnvironmentValue {
    
    init()
    
    static var keyPath: EnvironmentPath { get }

    typealias EnvironmentKey = EnvironmentValueKey<Self>
    typealias EnvironmentPath = WritableKeyPath<EnvironmentValues, Self>
}
```

This means that all a type has to do is to provide a default initializer or default values for each property, as well as a key path. This is how the `MyViewStyle` from above could look:

```swift
private struct MyViewStyle: EnvironmentValue {
    
    var color: Color = .blue
    
    static var keyPath: EnvironmentPath { \.myViewStyle }
}
```

EnvironmentKit has a custom `.environment` view modifier that just needs a value, and then uses the type information to figure out which key path to use:

```swift
public extension View {

    func environment<T: EnvironmentValue>(
        _ value: T
    ) -> some View {
        environment(T.keyPath, value)
    }
}
```

This means that we don't have to repeat the key path information when providing a custom `myViewStyle` modifier. Instead, we can just do this:

```swift
private extension View {

    func myViewStyle(_ style: MyViewStyle) -> some View {
        environment(style)
    }
}
```

We can now apply `.myViewStyle(...)` to any view, then use `@Environment(\.myViewStyle)` to access the injected value in any view. If no value is injected, a default value is returned.



## Future work

Although the end result lets you create custom environment types with less code, I hoped to be able to use the `EnvironmentValue` to do even more automatically.

My initial idea was for the `keyPath` to be automatically resolved by `EnvironmentValues`, by using a generic function that could be use instead of an explicit key path property.

However, Swift seems to require an actual property to be able to use it as a keypath in the `.environment` modifier. If we could work around this, we'd need even less code.

The dream would be for the type to just implement the `EnvironmentValue` protocol, and for EnvironmentKit to take care of the rest.



## Conclusion

The `EnvironmentValue` approach lets us define custom environment types with a lot less code. If you think this approach looks interesting, make sure to give [EnvironmentKit]({{page.sdk}}) a try.