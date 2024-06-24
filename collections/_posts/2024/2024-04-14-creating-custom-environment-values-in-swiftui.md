---
title:  Creating custom environment values in SwiftUI
date:   2024-04-14 04:00:00 +0000
tags:   swift swiftui open-source

assets: /assets/blog/24/0414/
image:  /assets/blog/24/0414.jpg
image-show: 0

redirect_from: /blog/2024/04/14/create-custom-environment-types-in-swiftui-with-less-code

entry:  https://developer.apple.com/documentation/SwiftUI/Entry()

tweet:  https://x.com/danielsaidi/status/1779625421656535282
toot:   https://mastodon.social/@danielsaidi/112271734083721514

sdk:    https://github.com/danielsaidi/EnvironmentKit
---

In this post, I'll describe how to create custom environment values in SwiftUI, and how I've abused Swift to let us do it with a lot less code than what is otherwise required.


## Update: 2024-06-14

At WWDC24, Apple presented a new [Entry]({{page.entry}}) type that will let us define custom environment, transaction, container, and focused values with very little code.

The `Entry` type will make the code and the linked [SDK]({{page.sdk}}) in this post obsolete. I will however keep the information here as a reference, for anyone who not yet targets iOS 18.


## Background

Environment values can be used to make it easy to inject things like styles, configurations, etc. into the view hierarchy, instead of having to apply them with a view initializer.

For instance, consider that you have a `MyView` view, that can be styled with a `MyViewStyle`:

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

Init injection however becomes complicated in more complex view hierarchies, where you may have to pass in values deep into the view hierarchy.

Instead of init injection, SwiftUI provides a convenient way of injecting environment values into the view environment, then using `@Environment` to access the injected values.

SwiftUI uses this convention extensively, for instance with the `.buttonStyle` view modifier, as well as many, many other styles and configurations that exist in the framework.

Environment injection is MUCH more flexible than init injection, since you can inject values into any part of the view hierarchy.

It's also nice to remove parameters from view initializers, since complex and generic views can end up with complicated init permutations.

Let's take a look at how to define custom environment values in the standard, complicated SwiftUI way, then how we can simplify it a bit using the Swift type system. 


## How to define an environment value - the standard way

To define a custom environment value, you must first extend the native `EnvironmentValues` type with a property that can get and set a value of that type.

For this to work, you must also define a custom `EnvironmentKey` type that returns a default value for your type, then make the `EnvironmentValues` property use that type.

Finally, it's nice to provide a view modifier with the same name as your value type, like how the `ButtonStyle` has a matching `.buttonStyle` view modifier.

For `MyViewStyle`, the resulting code could look something like this:

```swift
extension MyStyle {
    
    static var standard = Self()
}

extension MyStyle {

    struct Key: EnvironmentKey {
        static var defaultValue: MyStyle = .standard
    }
}

extension EnvironmentValues {

    var myStyle: MyStyle {
        get { self[MyStyle.Key.self] }
        set { self[MyStyle.Key.self] = newValue }
    }
}

extension View {

    func myStyle(_ style: MyStyle) -> some View {
        environment(\.myStyle, style)
    }
}
```

The `MyView` view could then be rewritten like this:

```swift
struct MyView {

    @Environment(\.myViewStyle)
    var style: MyViewStyle

    var body: some View {
        style.color
    } 
}
```

This is not much code, but imagine having many custom types, and for each you'd have to repeat yourself over and over.

I'd like this to require less code, and have spent some time playing around with plain Swift and protocols to come up with a more streamlined approach. Let's take a look.


## How to define an environment value - an easier way

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

While you don't save that many lines of code, you avoid the repetitions of having to refer to the same type many times, defining a keypath, etc. All in all, I find it much cleaner.

Everything is powered by having your type implementing the `EnvironmentValue` protocol, which defines all it needs to resolve keys, keypaths, etc. Let's see how it's implemented.


## Creating EnvironmentKit

I wanted a core protocol to power the library, and since SwiftUI has an `EnvironmentValues` type, I decided to call it `EnvironmentValue` (this will surely punish me in the future):

```swift
protocol EnvironmentValue {}
```

To be able to resolve things automatically, the protocol will require its implementing types to provide a parameterless initializer, or default values for all properties:

```swift
protocol EnvironmentValue {
    
    init()
}
```

With this initializer, the protocol can now provide a default value for all implementing types:

```swift
extension EnvironmentValue {
    
    static var defaultValue: Self { .init() }
}
```

We can now automatically provide a key type for every value type, using the default value:

```swift
protocol EnvironmentValue {
    
    init()
    
    typealias EnvironmentKey = EnvironmentValueKey<Self>
}

struct EnvironmentValueKey<T: EnvironmentValue>: EnvironmentKey {
    
    static var defaultValue: T { T.defaultValue }
}
```

We can now extend the native `EnvironmentValues` type with a getter and a setter that uses this key information to get and set values for any `EnvironmentValue`:

```swift
extension EnvironmentValues {
    
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
extension EnvironmentValues {

    var myViewStyle: MyViewStyle {
        get { get() } 
        set { set(newValue) }
    }
}
```

To avoid having to specify a keypath, we can force each type to specify its own keypath:

```swift
protocol EnvironmentValue {
    
    init()
    
    static var keyPath: EnvironmentPath { get }

    typealias EnvironmentKey = EnvironmentValueKey<Self>
    typealias EnvironmentPath = WritableKeyPath<EnvironmentValues, Self>
}
```

This means that all a type has to do is to provide a default initializer or a default value for each property, as well as a key path. 

This is how the `MyViewStyle` from above could look:

```swift
struct MyViewStyle: EnvironmentValue {
    
    var color: Color = .blue
    
    static var keyPath: EnvironmentPath { \.myViewStyle }
}
```

We can now add an `environment` view modifier that takes an `EnvironmentValue` and uses its type information to figure out which key path to use:

```swift
extension View {

    func environment<T: EnvironmentValue>(
        _ value: T
    ) -> some View {
        environment(T.keyPath, value)
    }
}
```

This means that we don't have to repeat the key path information when providing a custom `myViewStyle` modifier. Instead, we can just do this:

```swift
extension View {

    func myViewStyle(_ style: MyViewStyle) -> some View {
        environment(style)
    }
}
```

We can now apply `.myViewStyle(...)` to any view, then use `@Environment(\.myViewStyle)` to access the injected value in any view. If no value is injected, a default value is returned.



## Future work

Although the end result lets you create custom environment types with less code, I hoped to be able to use the `EnvironmentValue` to do even more automatically.

My initial idea was for the `keyPath` to be automatically resolved by `EnvironmentValues`, by using a generic function that could be use instead of an explicit key path property.

However, Swift seems to require an actual property to be able to use it as a keypath in the `.environment` modifier. If we could work around this, we'd need to write even less code.



## Conclusion

The `EnvironmentValue` approach lets us define custom environment values with a lot less code. If you think this approach looks interesting, make sure to give [EnvironmentKit]({{page.sdk}}) a try.

However, with the addition of [Entry]({{page.entry}}) in iOS 18, this approach will no longer be required, so only use it if you target older OS versions.