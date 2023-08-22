---
title:  Generating a random color in SwiftUI
date:   2022-05-25 10:00:00 +0000
tags:   swiftui colors

icon:   swiftui
---

In this post, we'll take a quick look at how to generate a random color in SwiftUI, using the nice random api:s provided in Swift.

{% include kankoda/data/open-source.html name="SwiftUIKit" %}

In Objective-C, random values involved a lot of `arc4random_uniform` code, which doesn't read well. You could use these global functions to generate random values, colors etc., often with a lot of code.

As Swift introduced `random` APIs for various numeric values, these things became way less complicated. However, some types like `Color`, still don't have built-in random support.

We can add this support to `Color` quite easily, by using the `Double` random capabilities and the rgb `Color` initializer that was added in iOS 14.

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

This lets you generate a random color and lets you specify whether you want a random opacity or not. 

You can use random colors for various features, as a background color to make view debugging easier etc. since it makes it easy to see which views redraw.


## Conclusion

This was a short post, but I hope you found it helpful. You can find the source code in my [SwiftUIKit]({{project.url}}) library. Feel free to try it out and let me know what you think.