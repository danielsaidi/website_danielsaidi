---
title:  Bringing platform-specific types together in SwiftUI
date:   2022-04-25 07:00:00 +0100
tags:   swiftui swift

icon:   swiftui
assets: /assets/blog/2022/2022-04-25/
tweet:  https://twitter.com/danielsaidi/status/1518493789517717505?s=20&t=wF1kbk5Nxm27t6vxQ1OeLQ

swiftuikit: https://github.com/danielsaidi/SwiftUIKit
---

SwiftUI's amazing multi-platform support makes it easy to develop apps for iOS, macOS, tvOS and watchOS. But how do you handle types that differ between platforms? Let's take a look.

Consider a SwiftUI app that lists images in a stack or grid, where tapping an image should open up a share sheet that can be used to share the image. 

If list items over the network, the item type could look something like this:

```swift
struct ImageItem: Codable {

    let created: Date
    let title: String
    let imageData: Data
}
```

This is a platform-agnostic type, since there are no platform-specific parts. However, to do anything with the image, we need to convert it to `UIImage` for UIKit, `NSImage` for AppKit and `Image` for SwiftUI.

For convenience, lets add an `image` property that maps the `imageData` to an image. Since we may want to use it for more than just showing it in the app, let's not make it a SwiftUI `Image`.

For UIKit, we could do it like this:

```swift
extension ImageItem {

    var image: UIImage {
        UIImage(data: imageData)
    }
}
```

For AppKit, it would look almost identical, but using `NSImage` instead of `UIImage`:

```swift
extension ImageItem {

    var image: NSImage {
        NSImage(data: imageData)
    }
}
```

While we could wrap these extensions in `#if os(iOS)` and `#if os(macOS)`, we may have to work with images in more places and should therefore look for a more platform-agnostic way to do so. 

To handle these situations, I prefer to create a platform-agnostic typealias, for instance:

```swift
#if os(macOS)
import Cocoa

public typealias ImageResource = NSImage
#endif

#if os(iOS) || os(tvOS) || os(watchOS)
import UIKit

public typealias ImageResource = UIImage
#endif
```

Since both `UIImage` and `NSImage` has a `data` initializer, we can now rewrite the `image` property:

```swift
extension ImageItem {

    var image: ImageResource {
        ImageResource(data: imageData)
    }
}
```

We could also extend SwiftUI `Image` to make it easier to initialize it with this new type:

```swift
extension Image {
    
    init(_ imageResource: ImageResource) {
        #if os(iOS) || os(watchOS) || os(tvOS)
        self.init(uiImage: imageResource)
        #elseif os(macOS)
        self.init(nsImage: imageResource)
        #endif
    }
}
```

This lets us display the `image` of an `ImageItem` in SwiftUI like this:

```swift
Image(item.image)
```

We could extend `ImageItem` further and add an `imageView` extension, but I think you get the point.

This is all easy when the underlying types share the same api:s, but how about when they don't? For instance `UIImage` has a `jpegData(compressionQuality:)` function that `NSImage` lacks.

To fix this, we can just fill in the gaps by implementing the missing functionality that we need. We could for instance implement `jpegData` for `NSImage` by first defining a `cgImage` property:

```swift
extension NSImage {
    
    var cgImage: CGImage? {
        var rect = CGRect(origin: .zero, size: size)
        return cgImage(forProposedRect: &rect, context: nil, hints: nil)
    }
}
```

then use it to define a `jpegData` function:

```swift
extension NSImage {
 
    func jpegData(compressionQuality: CGFloat) -> Data? {
        guard let image = self.cgImage(forProposedRect: nil, context: nil, hints: nil) else { return nil }
        let bitmap = NSBitmapImageRep(cgImage: image)
        return bitmap.representation(using: .jpeg, properties: [.compressionFactor: compressionQuality])
    }
}
```

One drawback with this approach is that the UIImage and NSImage implementations will be defined at various places, where UIImage defines this within UIKit and you defined it in a custom extension. 

In this case, I think the `jpegData` extension is fine, since it's a convenience that is always true for an `NSImage`. For other cases, consider extending the `ImageResource` typealias instead, for instance:

```swift
extension ImageResource {

    func compressedForSharing() -> ImageResource? {
        jpegData(compressionQuality: 0.7)
    }
}
```

Since we defined `jpegData` for `NSImage` and `UIImage` already has an identical function in `UIKit`, `ImageResource` can use it without any `#if os(...)` switches.


## Conclusion

SwiftUI multi-platform apps work amazingly well, but you may have to put work into bridging underlying types. For more complex situations and larger systems, consider using protocols to define things further.

You can find the types and extensions in this post in the [SwiftUIKit]({{page.swiftuikit}}) library. Feel free to try them out and let me know what you think.