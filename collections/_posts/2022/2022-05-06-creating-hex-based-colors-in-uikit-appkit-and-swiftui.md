---
title:  Creating hex-based colors in UIKit, AppKit and SwiftUI
date:   2022-05-06 12:00:00 +0000
tags:   quick-tip swift swiftui

icon:   swift

swiftuikit:  https://github.com/danielsaidi/SwiftUIKit
---

In this post, let's take a quick look at how to we can extend the UIKit, AppKit and SwiftUI colors with hex-based initializers that accept strings (e.g. `"#abcdef"`) and numeric values (e.g. `0xabcdef`).

Although you can use asset catalogs to define colors for your apps and frameworks, there may come a time when you have to create colors from hex codes, for instance when fetching colors from a web api.

You may find it strange that neither UIKit, AppKit nor SwiftUI has initializers for creating colors with hex codes, and you'd be right to think so. Since Apple provides initializers that let you define red, green, blue and alpha values separately, it's a bit strange.

I first thought that they may want to encourage using asset catalogs, since asset colors can be extended to support dark and high contrast mode, but in that case, they should discourage developers from using the rgba initializers as well.

Regardless, let's take a look at how you can add your own extensions to provide a way to use hex codes to create colors. It's pretty straightforward.


## Multi-platform bridging

To simplify working with colors on multiple platforms, let's first define a convenience typealias that we can use on all platforms, to make the rest of the code cleaner:

```swift
#if os(macOS)
import AppKit.NSColor

typealias ColorRepresentable = NSColor
#endif

#if os(iOS) || os(tvOS) || os(watchOS)
import UIKit.UIColor

typealias ColorRepresentable = UIColor
#endif
```

`ColorRepresentable` resolves to `NSColor` on macOS, while it resolves to `UIColor` on all other platforms. The rest of our code doesn't have to know which type we really use.


## Adding hex support to ColorRepresentable

With the new multi-platform `ColorRepresentable` in place, we can now extend it with a few initializers that let us create platform-specific colors with hex-based strings and integers.

Let's start with the integer-based initializer:

```swift
extension ColorRepresentable {

    convenience init(hex: UInt64) {
        let r, g, b, a: CGFloat
        r = CGFloat((hex & 0xff000000) >> 24) / 255
        g = CGFloat((hex & 0x00ff0000) >> 16) / 255
        b = CGFloat((hex & 0x0000ff00) >> 8) / 255
        a = CGFloat(hex & 0x000000ff) / 255
        self.init(red: r, green: g, blue: b, alpha: a)
    }
}
```

This initializer lets us provide both numeric values like `0xabcdef` (which lacks an alpha component) and `0xabcdef55` (which provides one with the last two digits). Omitting the alpha component makes the color opaque instead of transparent.

For the string-based initializer, we first need a few string extensions, which we can keep private:

```swift
private extension String {

    func cleanedForHex() -> String {
        if hasPrefix("0x") {
            return String(dropFirst(2))
        }
        if hasPrefix("#") {
            return String(dropFirst(1))
        }
        return self
    }

    func conforms(to pattern: String) -> Bool {
        let pattern = NSPredicate(format:"SELF MATCHES %@", pattern)
        return pattern.evaluate(with: self)
    }
}
```

`cleanedForHex()` removes any hex-specific prefixes, like `#` and `0x` while `conforms(to:)` can be used to evaluate if a string conforms to a certain regular expression.

We can now implement the string-based initializer:

```swift
public extension ColorRepresentable {

    convenience init?(hex: String) {
        let hex = hex.cleanedForHex()
        guard hex.conforms(to: "[a-fA-F0-9]+") else { return nil }
        let scanner = Scanner(string: hex)
        var hexNumber: UInt64 = 0
        guard scanner.scanHexInt64(&hexNumber) else { return nil }
        self.init(hex: hexNumber)
    }
}
```

This initializer cleans the string, then ensures that it only contains the correct characters. It then uses a `Scanner` to generate a `UInt64` which it passes to the integer-based initializer that we created earlier. 

With this, we now have a set of initializers that apply to both `NSColor` and `UIColor`, which means that they can be used in both AppKit and `UIKit`. 

Let's move on to SwiftUI.


## Adding hex support to Color

Implementing the same functionality for the SwiftUI `Color` type is super-simple, since we can just build upon what we've already implemented. 

We basically just have to add these two initializers:

```swift
public extension Color {

    init?(hex: String) {
        guard let color = ColorRepresentable(hex: hex) else { return nil }
        self.init(color)
    }

    init(hex: UInt64) {
        let color = ColorRepresentable(hex: hex)
        self.init(color)
    }
}
```

The initializers just creates a `ColorRepresentable` value, then return a `Color` that wraps that value. Everything else is defined in the initializers that we created earlier.


## Conclusion

Being able to create colors from hex codes is a nice thing to have in place, for instance if you persist or receive raw hex strings or integers. Just keep in mind that supporting features like light and dark mode with programatically created colors can be messy. In many cases, color assets are more convenient.

If you're interested in the source code, you can find it in the [SwiftUIKit]({{page.swiftuikit}}) library. Don't hesitate to comment or reach out with any thoughts you may have. I'd love to hear your thoughts on this.