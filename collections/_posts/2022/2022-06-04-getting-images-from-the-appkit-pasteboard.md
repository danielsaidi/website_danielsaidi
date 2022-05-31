---
title:  Getting images from the AppKit pasteboard
date:   2022-06-04 10:00:00 +0000
tags:   swift appkit

icon:   swift
---

In this post, we'll take a quick look at how we can fetch images from `NSPasteboard`, which lacks a lot of functionality compared to `UIPasteboard`.

Unlike `UIPasteboard`, the `NSPasteboard` in AppKit has no convenient way to fetch any images that the user has copied into the pasteboard. You can make it behave more like `UIPasteboard` by adding these two extensions to it:

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