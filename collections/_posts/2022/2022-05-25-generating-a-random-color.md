---
title:  Generating a random color in SwiftUI
date:   2022-05-25 10:00:00 +0000
tags:   swiftui

icon:   swiftui

swiftuikit: https://github.com/danielsaidi/SwiftUIKit
---

In this post, we'll take a quick look at how to generate a random color in SwiftUI, using the nice random api:s provided in Swift.

Back in the good ol' Objective-C days, random values involved a lot of `arc4random_uniform`, which although not hard in itself isn't really nice to read. You could then use these global functions to generate random values, colors etc., which often involved quite a lot of code.

As Swift later introduced nice `random` api:s for various numeric values, these things became way less complicated to implement. However, Apple leaves it up to you to add random support to certain types. One such example is the SwiftUI `Color` struct, which doesn't have built-in random support.

We can however add this support quite easily, by using the `Double` random capabilities and the rgb `Color` initializer that was added in iOS 14.

```swift
public extension Color {

    static func random(randomOpacity: Bool = false) -> Color {
        Color(
            red: .random(in: 0...1),
            green: .random(in: 0...1),
            blue: .random(in: 0...1),
            opacity: randomOpacity ? .random(in: 0...1) : 1
        )
    }
}

```

This lets you generate a random color and also lets you specify whether you want a random opacity or not. You can use random colors for various features, as a background color to make view debugging easier etc. since it makes it easy to see which views redraw.


## Conclusion

This was a short post, but I hope you found it helpful. You can find the source code in my [SwiftUIKit]({{page.swiftuikit}}) library. Feel free to try it out and let me know what you think.