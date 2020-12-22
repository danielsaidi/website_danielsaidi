---
title: Swift Semantics
date:  2020-10-16 12:00:00 +0100
tags:  swift swiftui uikit
icon:  swift

swiftkit:    https://github.com/danielsaidi/SwiftKit
swiftuikit:  https://github.com/danielsaidi/SwiftUIKit

control-flow: https://docs.swift.org/swift-book/LanguageGuide/ControlFlow.html
---

In this post, we'll look at various ways to improve readability and writeability of Swift code by introducing extensions to Swift's native types.


## TLDR;

If you find this post too long, I have added most of the extensions in this post to my [SwiftKit]({{page.swiftkit}}) and [SwiftUIKit]({{page.swiftuikit}}) libraries. I have chosen to exclude UIKit-specific code, since I have shifted focus to SwiftUI and don't have any UIKit code in these libraries.


## Background

There will always be parts of a language that you may think are missing or doesn't fit your personal coding style. Some things are just strange (e.g. why does UIKit only have an `isHidden` view property, but no `isVisible`) while some may be specific to your own needs.

Swift intentionally tries to be very focused, which means that it will intentionally leave out parts that you may think should be there, which may explain missing properties like `isVisible`. You can easily create such properties yourself with extensions, but doing so can be repetetive and tiresome. I have therefore started gathering reusable extensions in the libraries mentioned above, which makes my code more straightforward.

I will split this post into different sections for specific focus areas. I will update the post as I add more related extensions to my various libraries. If you have any suggestions or comments, don't hesitate to discuss in the comment section or send me PRs.


## Inverse semantics

I personally think that using `!` in [control flow]({{page.control-flow}}) makes code harder to read, since it adds logic operators to code that otherwise pretty much reads like English. 

In many cases, you can just replace a `guard` with an `if` and vice versa to make the code easier to read. For instance, instead of the double negation nature of this `guard`:

```swift
guard !view.isHidden else { return }
```

I think that the code below is easier to read:

```swift
if view.isHidden { return }
```

Sometimes you can't do this, though, since a `guard` has to return. For instance, here:

```swift
if !view.isHidden { /* do something with the view */ }
```

you can't replace the `if` with a `guard`. Here, I think that `inverse` properties can help:

```swift
if view.isVisible { /* do something with the view */ }
```

I find that it's easier to read code that expresses intent instead of inversed intent, so I try to reduce negations as much as possible.


## Syntax hiding semantics

For an overall readable language like Swift, I think that `==` and `!=` reduces readability (not always true, though, read on).

Consider if you want to check that an optional has a value:

```swift
if myOptional != nil { ... }
if myOptional == nil { ... }
```

I think the following is easier to read:

```swift
if myOptional.isSet { ... }
if myOptional.isNil { ... }
```

The same goes for comparisons. Consider comparing two dates:

```swift
if date1 > date2 { ... } 
if date1 < date2 { ... } 
if date1 == date2 { ... } 
```

I think the following is easier to read:

```swift
if date1.isAfter(date2) { ... }
if date1.isBefore(date2) { ... }
if date1.isSame(as: date2) { ... }
```

This is a common feature of test libraries that strive for readable test code, with comparison functions like `5.isGreaterThan(4)`. I think this is a great practice in other cases as well.

However, I think the syntax above may also reduce readability in some cases. I think you should stick to operators when you are working with raw numeric values. For instance, I don't think that `if 5.isGreater(than: 4)` is much more readable than `if 5 > 4`.


## Chaining semantics

In many cases, chaining operators can improve readability by removing the use of `guard`s or `if let`s, for instance when you have to cast optional values. Consider this case, where you have to cast an optional SwiftUI `View` to `AnyView`:

```swift
var body: some View {
    content
}

var content: AnyView {
    if let form = loginForm {
        return AnyView(form)
    } else {
        // Logged in content
    }
}

var loginForm: LoginForm? {
    isLoggedIn ? nil : LoginForm()
}
```

In the code above, you have to perform an `if let` to get the login form before you can cast it. To simplify things, you could add the following extension:

```swift
public extension View {
    
    func any() -> AnyView {
        AnyView(self)
    }
}
```

then make the code more compact by not having to `if let` the login form:

```swift
var content: AnyView {
    loginForm?.any() ?? // Logged in content
}
```

The same can be used to simplify code for converting optional numeric values. Instead of:

```swift
var myInt: Int?
// To set or not to set, that is the question
guard let value = myInt else { return nil }
return Double(myInt)
```

You can add an `Int` `toDouble` extension:

```swift
public extension Int {
    
    func toDouble() -> Double { Double(self) }
}
```

that lets you chain the code and make it more readable:

```swift
return myInt?.toDouble()
```

Chaining operators are as great here as they are when e.g. chaining maps, filters, SwiftUI modifiers etc.


## Conclusion

This post discusses things that I think improves the overall readability of the code. One could argue that it's just a matter of becoming familiar with the syntax of the language you write, but I would argue that readability trumps keeping to the basics of the language.

Below is a list of semantics that I use in my projects:

* `Collection` `hasContent` instead of `!isEmpty`
* `Date` `isAfter`, `isBefore` and `isSame` instead of `>/`, `<` and `==`
* `Decimal` `doubleValue` instead of `Double(truncating...)`
* `Numeric` `toX()` e.g. `Int` `toDouble()` instead of `Double(self)`
* `Optional` `isNil` instead of `== nil`
* `Optional` `isSet` instead of `!= nil`
* `String` `hasContent` instead of `!isEmpty`
* `View` `any` instead of `AnyView(self)`
* `View` `hidden(if:)` instead of `if x { view.hidden() } else { view }`
* `View` `visible(if:)` instead of `if !view.hidden(if:)`


## Source code

I have added most of the extensions in this post to my [SwiftKit]({{page.swiftkit}}) and [SwiftUIKit]({{page.swiftuikit}}) libraries. Feel free to send me your own semantic improving extensions as PRs.