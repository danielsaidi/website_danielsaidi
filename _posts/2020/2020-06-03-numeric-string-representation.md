---
title:  "Numeric string representations"
date:   2020-06-03 16:00:00 +0100
tags:   swift
icon:   swift

lib:    https://github.com/danielsaidi/SwiftKit
source: https://github.com/danielsaidi/SwiftKit/tree/master/Sources/SwiftKit/Extensions
tests:  https://github.com/danielsaidi/SwiftKit/tree/master/Tests/SwiftKitTests/Extensions
---

In this post, we'll look at how to create string representations of numeric types in Swift and extend these types with functionality to make this easier. 


## The basics

When you create a string from a serializable type, you can use `String(format:,)` to provide rules for how you want the string to be formatted. Different formats apply to different value types.

For instance, to create a string of a `Double` and use two decimals, you would have to write something like:

```swift
let value = 1.2345
let result = String(format: "%0.2f")    // => 1.23
```

While this is easy, it's pretty hard to remember certain rules. In my opinion, it's also nasty to scatter magic formatting strings all over the code base.


## Extending numeric types

To make this specific task of serializing a decimal value with two decimals easier, we could create extensions for the numeric types that we want to support:

```swift
public extension CGFloat {
    
    func string(withDecimals decimals: Int) -> String {
        String(format: .decimals(decimals), self)
    }
}

public extension Double {
    
    func string(withDecimals decimals: Int) -> String {
        String(format: .decimals(decimals), self)
    }
}

public extension Float {
    
    func string(withDecimals decimals: Int) -> String {
        String(format: .decimals(decimals), self)
    }
}

private extension String {
    
    static func decimals(_ decimals: Int) -> String { "%0.\(decimals)f" }
}

```

While this works, it's repeating the same code over and over. We can do better.


## Creating a shared extension

If we look at `String(format:,)`, we can see that it takes a list of `CVarArg` arguments. It turns out that this is a protocol that is implemented by all the types above.

We could thus make the extension above more general:

```swift
public extension CVarArg {
    
    func string(withDecimals decimals: Int) -> String {
        String(format: "%0.\(decimals)f", self)
    }
}
```

This extension will apply to all the types above, and thus only require us to have a single extension for all types. Nice...

...or not that nice, because `CVarArg` is implemented by a bunch of types, where "decimals" doesn't make sense. For instance, with the exstension above, we could do this:

```swift
let string = "Hello, world!"
let result = string.string(withDecimals: 2)
```

While this is exciting and wild, it just doesn't make sense. We need to restrict this somehow.

We can do this by introducing a new protocol

```swift
public protocol NumericStringRepresentable: CVarArg {}
```

then let the numeric types we want to support implement this protocol

```swift
extension CGFloat: NumericStringRepresentable {}
extension Double: NumericStringRepresentable {}
extension Float: NumericStringRepresentable {}
```

and then apply the extension to this protocol instead of `CVarArg`

```swift
public extension NumericStringRepresentable {
    
    func string(withDecimals decimals: Int) -> String {
        String(format: "%0.\(decimals)f", self)
    }
}
```

We have now isolated the extension to the types that explictly implement `NumericStringRepresentable`.



## Source code

I have added this extension to my [SwiftKit]({{page.lib}}) library. You can find the source code [here]({{page.source}}) and the unit tests [here]({{page.tests}}).