---
title:  Multi-platform image resizing
date:   2022-06-29 09:00:00 +0000
tags:   multi-platform images uikit appkit

icon:   swift
tweet:  https://twitter.com/danielsaidi/status/1542274485616787456?s=20&t=PkyvVlGvMZnUvLmKC_TMwA
---

In this post, let's take a quick look at how to resize images in UIKit and AppKit. The result will work on iOS, macOS, tvOS and watchOS and lets us resize images with the same APIs regardless of platform.

{% include kankoda/data/open-source.html name="SwiftUIKit" %}

Image resizing is pretty straightforward in Swift, so let's look at how to implement it in UIKit and AppKit, then add more functionality that applies to all platforms.

In UIKit, we can add a `UIImage` extension that resizes images using the image context:

```swift
#if canImport(UIKit)
import UIKit

public extension UIImage {

    func resized(to size: CGSize) -> UIImage? {
        UIGraphicsBeginImageContextWithOptions(size, false, scale)
        draw(in: CGRect(origin: CGPoint.zero, size: size))
        let result = UIGraphicsGetImageFromCurrentImageContext()
        UIGraphicsEndImageContext()
        return result
    }
}
#endif
```

In AppKit, we can add an `NSImage` extension that resizes images in a different way:

```swift
#if canImport(AppKit)
import AppKit

public extension NSImage {

    func resized(to newSize: CGSize) -> NSImage? {
        let newImage = NSImage(size: newSize)
        newImage.lockFocus()
        let sourceRect = NSMakeRect(0, 0, size.width, size.height)
        let destRect = NSMakeRect(0, 0, newSize.width, newSize.height)
        draw(in: destRect, from: sourceRect, operation: .sourceOver, fraction: CGFloat(1))
        newImage.unlockFocus()
        return newImage
    }
}
#endif
```

Since both `UIImage` and `NSImage` now have the same API for resizing images to a certain size, we can extend both types with more resizing functionality that use this underlying functionality.

Let's first define a platform agnostic image typealias, which you may have seen in previous posts:

```swift
#if canImport(AppKit)
import class AppKit.NSImage

public typealias ImageRepresentable = NSImage
#endif

#if canImport(UIKit)
import class UIKit.UIImage

public typealias ImageRepresentable = UIImage
#endif
```

This let's us use `ImageRepresentable` to handle both `UIImage` and `NSImage`. Let's use this to add ways to resize images to a certain width and height, using the resizing function we defined earlier:

```swift
public extension ImageRepresentable {

    func resized(toHeight points: CGFloat) -> ImageRepresentable? {
        let ratio = points / size.height
        let width = size.width * ratio
        let newSize = CGSize(width: width, height: points)
        return resized(to: newSize)
    }

    func resized(toWidth points: CGFloat) -> ImageRepresentable? {
        let ratio = points / size.width
        let height = size.height * ratio
        let newSize = CGSize(width: points, height: height)
        return resized(to: newSize)
    }
}
```

That's it - you can now use `resized(to:)`, `resized(toWidth:)` and `resized(toHeight:)` with `UIImage` and `NSImage`, to resize images in iOS, macOS, tvOS and watchOS. 

I have added these extension to [SwiftUIKit]({{project.url}}). Feel free to try them out and let me know what you think.
