---
title:  How to bridge platform-specific types in Swift & SwiftUI
date:   2022-04-25 07:00:00 +0100
tags:   swiftui swift multi-platform

image:  /assets/blog/22/0425/title.jpg

tweet:  https://x.com/danielsaidi/status/1760314704335876445?s=20
toot:   https://mastodon.social/@danielsaidi/111970012516609641
---

SwiftUI's multi-platform support makes it easy to develop apps for all platforms, but how do you handle types that differ between platforms? Let's take a look.

{% include kankoda/data/open-source name="SwiftUIKit" %}


## Problem description

Lets say that we fetch the following list item over a REST-based API, with the intention to list it in a multi-platform SwiftUI app:

```swift
struct ListItem: Codable {

    let title: String
    let created: Date
    let imageData: Data
}
```

The type itself is platform-agnostic, but to do anything with the image, we need to convert it to `UIImage` for UIKit, `NSImage` for AppKit and `Image` for SwiftUI.

Let's take a look at how we can provide a displayable image in a platform-agnostic way.


## UIKit & AppKit

To support UIKit and AppKit, we can add an `image` extension to `ListItem` that maps the `imageData` value to either a `UIImage` or an `NSImage`:

```swift
import SwiftUI

#if canImport(UIKit)
extension ListItem {

    var image: UIImage? {
        .init(data: imageData)
    }
}
#elseif canImport(AppKit)
extension ListItem {

    var image: NSImage? {
        .init(data: imageData)
    }
}
#endif
```

To avoid having to use `#if` checks everywhere in the code, I actually first prefer to define a platform-agnostic image typealias like this:

```swift
import SwiftUI

#if canImport(UIKit)
public typealias ImageRepresentable = UIImage
#elseif canImport(AppKit)
public typealias ImageRepresentable = NSImage
#endif
```

Since both `UIImage` and `NSImage` have a `data`-based initializer, this `ImageRepresentable` typealias now lets us rewrite the `image` property like this:

```swift
extension ListItem {

    var nativeImage: ImageRepresentable {
        ImageRepresentable(data: imageData)
    }
}
```

We can then add any new capabilities that we need to `UIImage` and `NSImage`, to have a platform-agnostic image type that works in the same way across all platforms.


## SwiftUI

To support SwiftUI, we can extend `Image` to make it easier to initialize it with this new type:

```swift
import SwiftUI

extension Image {
    
    init(_ image: ImageRepresentable) {
        #if canImport(UIKit)
        self.init(uiImage: image)
        #elseif canImport(Cocoa)
        self.init(nsImage: image)
        #endif
    }
}
```

We can now extend `ListItem` with a SwiftUI `image` without having to do any `#if` checks:

```swift
extension ListItem {

    var image: Image { 
        .init(nativeImage) 
    }
}
```


## Extending the platform-agnostic image type

This was easy to achieve, since `UIImage` and `NSImage` both had a `Data`-based initializer, but how about when they don't share the same APIs?

For instance, consider how `UIImage` has a `jpegData(compressionQuality:)` function that `NSImage` lacks. We can then fill in the gaps by implementing missing functionality. 

We can implement `jpegData` for `NSImage` by first defining a `cgImage` property:

```swift
#if canImport(Cocoa)
extension NSImage {
    
    var cgImage: CGImage? {
        cgImage(forProposedRect: nil, context: nil, hints: nil)
    }
}
#endif
```

We can then use this function to define a `jpegData` function:

```swift
#if canImport(Cocoa)
extension NSImage {
 
    func jpegData(compressionQuality: CGFloat) -> Data? {
        guard let image = cgImage else { return nil }
        let bitmap = NSBitmapImageRep(cgImage: image)
        return bitmap.representation(using: .jpeg, properties: [.compressionFactor: compressionQuality])
    }
}
#endif
```

Since both `UIImage` and `NSImage` now have a `jpegData` function with the same signature, you can extend `ImageRepresentable` by building upon the shared functionality:

```swift
extension ImageRepresentable {

    func compressedForSharing() -> Self? {
        jpegData(compressionQuality: 0.7)
    }
}
```

Since both types define the same API, we don't need to add any `#if` checks. This keeps the rest of our source code clean and less error-prone.