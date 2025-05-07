---
title: Numeric string representations in Swift
date:  2020-06-03 16:00:00 +0100
tags:  swift extensions
icon:  swift

lib:    https://github.com/danielsaidi/SwiftUIKit
source: https://github.com/danielsaidi/SwiftUIKit/tree/master/Sources/SwiftUIKit/Extensions
---

In this post, we'll create string representations of numeric Swift types and extend them with convenience functionality to make them easier to use. 


## The basics

You can use `String(format:_:)` to create strings from serializable types. Different formats apply to different value types.

For instance, you can create a two decimal string from a `Double` value like this:

```swift
let value = 1.2345
let result = String(format: "%0.2f", value)    // => "1.23"
```

While this is easy, I find it hard to remember formats. I also think it's nasty to scatter magic formatting strings all over the code base.


## Extending numeric types

For instance, say that we want to add an extension to make it easier to serialize a decimal value with any number of decimals easier.

We could create extensions for the numeric types that we want to support:

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

However, this duplicates the same code over and over. We can do a lot better than this.


## Creating a shared extension

If we look at `String(format:,)`, we can see that it takes a list of `CVarArg` arguments. This is a protocol that is implemented by all the numeric types above.

We could thus make the extension above more general by applying it to `CVarArg` instead:

```swift
public extension CVarArg {
    
    func string(withDecimals decimals: Int) -> String {
        String(format: "%0.\(decimals)f", self)
    }
}
```

This is however not a good idea. `CVarArg` is implemented by some types where "decimals" don't make sense. For instance, with the extension above, we could do this:

```swift
let string = "Hello, world!"
let result = string.string(withDecimals: 2)
```

While this is exciting (right?), it just doesn't make sense. We need to restrict this somehow and can do this by introducing a new protocol:

```swift
public protocol NumericStringRepresentable: CVarArg {}
```

We can then let the numeric types we want to support implement this protocol:

```swift
extension CGFloat: NumericStringRepresentable {}
extension Double: NumericStringRepresentable {}
extension Float: NumericStringRepresentable {}
```

We can then apply the extension to this protocol instead of `CVarArg`:

```swift
public extension NumericStringRepresentable {
    
    func string(withDecimals decimals: Int) -> String {
        String(format: "%0.\(decimals)f", self)
    }
}
```

With this, we have constrained the extension to pnly `NumericStringRepresentable` types.



## Source Code

I have added these extensions to my [SwiftUIKit]({{page.lib}}) library. You can find the source code [here]({{page.source}}). Feel free to try it out and let me know what you think!