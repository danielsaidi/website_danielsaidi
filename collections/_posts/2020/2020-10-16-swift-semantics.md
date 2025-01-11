---
title: Swift Semantics
date:  2020-10-16 12:00:00 +0100
tags:  swift
icon:  swift

control-flow: https://docs.swift.org/swift-book/LanguageGuide/ControlFlow.html
---

In this post, we'll look at various ways to improve readability of Swift code, by introducing a couple of extensions to Swift's native types.

{% include kankoda/data/open-source name="SwiftUIKit" %}{% assign swiftuikit = project %}


## Background

There will always be parts of any programming language that you may think are lacking or don't fit your personal coding style. This is also true for me with Swift & SwiftUI.

For instance, why does SwiftUI has a `disabled` view modifier, but not an inverted `enabled` one, why do you have to type `if !list.isEmpty` instead of `if list.hasContent`, etc.

Since Swift & SwiftUI has gaps that I think should be there, I have started adding reusable extensions to make my code more straightforward. I will go through some of them below.

If you have more suggestions, don't hesitate to discuss in the comments or submit a PR.


## Inverse semantics

I think using `!` in [control flow]({{page.control-flow}}) makes code harder to read, since it adds logic operators to code that otherwise pretty much reads like English.

In many cases, you can replace a `guard` with an `if` and vice versa to reverse the check. For instance, instead of the double negation nature of this `guard`:

```swift
guard !view.isHidden else { return }
```

I think this reads better:

```swift
if view.isHidden { return }
```

Sometimes you can't do this, though. For instance, here you can't replace `if` with `guard`, since guards have to return:

```swift
if !view.isHidden { /* do something with the view */ }
```

In these cases, inverse properties can help. The inverse of `isHidden` would be `isVisible`:

```swift
if view.isVisible { /* do something with the view */ }
```

I find that it's easier to read code that expresses intent instead of inversed intent, so I try to reduce negations as much as possible.


## Syntax hiding semantics

For an overall readable language like Swift, I think that `==` and `!=` reduces readability (not always true, though).

For instance, consider if you want to check that an optional has a value:

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

However, I think the syntax above reduces readability in some cases, e.g. when comparing numeric values. I don't think `if 5.isGreater(than: 4)` is more readable than `if 5 > 4`.


## Chaining semantics

In many cases, chaining operators can improve readability by removing `guard` or `if let` when using optional values. 

For instance, consider this case, where we cast an optional SwiftUI `View` to `AnyView`:

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

In the code above, you must perform an `if let` to get the login form before you can cast it. To simplify things, you could add the following extension:

```swift
public extension View {
    
    func anyView() -> AnyView {
        AnyView(self)
    }
}
```

then make the code more compact by not having to `if let` the login form:

```swift
var content: AnyView {
    loginForm?.anyView() ?? SomeLoggedInView()
}
```

The same can be used to simplify code for converting optional numeric values. Instead of:

```swift
var myInt: Int?
...
guard let value = myInt else { return nil }
return Double(myInt)
```

you can add an `Int` extension:

```swift
extension Int {
    
    func toDouble() -> Double { Double(self) }
}
```

that lets you chain the code and make it more readable:

```swift
return myInt?.toDouble()
```

I think chaining is as great here as when e.g. chaining maps, reduce, filters, modifiers, etc.


## Conclusion

This article discusses things I think improves the readability of Swift code. One could argue that you just have to become familiar with the syntax, but I'd argue against that.

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


## Source Code

I have added most of the extensions in this post to [SwiftUIKit]({{swiftuikit.url}}). Feel free to try it out and let me know what you think.