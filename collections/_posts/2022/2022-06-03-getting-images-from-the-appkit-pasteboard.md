---
title:  Getting images from the AppKit pasteboard
date:   2022-06-03 8:00:00 +0000
tags:   swift uikit appkit

icon:   swift

tweet:  https://twitter.com/danielsaidi/status/1532695001959911427?s=20&t=oawr9-x9iAsexoKgphA6OA
---

In this post, we'll take a quick look at how we can fetch images from the AppKit `NSPasteboard`, which lacks a lot of functionality compared to the UIKit `UIPasteboard`.

Unlike `UIPasteboard`, `NSPasteboard` has no convenient way to fetch images. Instead, you have to use `readObjects(forClasses:)` and cast the result to an `NSImage` array, which isn't ideal. 

You can make `NSPasteboard` behave more like `UIPasteboard` by adding these two properties to it:

```swift
import AppKit

extension NSPasteboard {

    var image: ImageRepresentable? {
        images?.first
    }
    
    var images: [ImageRepresentable]? {
        readObjects(forClasses: [NSImage.self]) as? [NSImage]
    }
}
```

This lets you use `images` to get all images in the pasteboard, and `image` to fetch the first. If the pasteboard doesn't contain any images, these properties will return `nil`.


## Conclusion

This was a short post, but I hope you found it helpful. Feel free to try it out and tell us what you think.