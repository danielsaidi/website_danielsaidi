---
title:  A better way to extend types
date:   2022-06-06 10:00:00 +0000
tags:   swift pasteboard multi-platform uikit appkit

icon:   swift

tweet:  https://twitter.com/danielsaidi/status/1534121018716999681?s=20&t=HuAkOPV9JSNSw7eWgtmaVQ
---

In this post, we'll take a quick look at a better way to extend types in Swift, to make the extensions more versatile and discoverable.


## Background

Extensions are convenient and common ways to extend types in Swift. In Swift, extensions are even the core of various coding styles, for instance to separate public and private members, encapsulate protocol implementations etc.

However, while extensions are convenient, they also risk bloating types with too much functionality and provide functionality that should be defined somewhere else, specified by a protocol, implemented in an abstract manner etc. Furthermore, extensions are also not included in generated DocC documentation, which may cause large part of an open-source library to not show up in the generated documentation.

There's no hard line when using an extension is "correct" or "wrong". Just keep an eye on your code and be aware of if you base too much of your logic in plain type extensions.


## Defining extensions with protocols

One way to make extensions more versatile and increase their discoverability, is to define a protocol that defines the functionality, then make suitable types implement the protocol with an extension. This makes it possible for more types to implement the same protocol and get access to the extension, and will also cause the extension to show up in the DocC documentation, since it is defined by the protocol instead of as a plain extension.

As an example, consider a situation where we want to get images from the pasteboard in both UIKit and AppKit. While `UIPasteboard` has properties for `image` and `images`, `NSPasteboard` has no such properties. If we want both types to have the same image properties, we could extend `NSPasteboard`:

```swift
public extension NSPasteboard {

    var image: ImageRepresentable? {
        images?.first
    }

    var images: [ImageRepresentable]? {
        readObjects(forClasses: [NSImage.self]) as? [NSImage]
    }
}
```

However, if we now put this extension in a library that uses DocC to generate documentation, this nice extension wouldn't show up, since DocC omits extensions to native types. Our dear developers would only get to know about its existence by typing in Xcode and hope that the autocomplete gods pick it up.

We can improve this by adding a protocol that defines the same functionality:

```swift
public protocol PasteboardImageReader {

    var image: ImageRepresentable? { get }

    var images: [ImageRepresentable]? { get }
}
```

We can then make both `UIPasteboard` and `NSPasteboard` conform to this protocol:

```swift
#if os(iOS)
import UIKit

extension UIPasteboard: PasteboardImageReader {}
#endif

#if os(macOS)
import AppKit

extension NSPasteboard: PasteboardImageReader {}
#endif
```

Unlike the extension, the protocol *will* show up in the DocC documentation, which will show that there is a way to get images from a pasteboard. However, it would not show that both pasteboards implement the protocol, since they are native types and not defined in the library. You should therefore mention this in the protocol documentation, for instance:

```swift
/**
 This protocol can be implemented any types that can provide
 images from the pasteboard.

 The protocol is implemented by the UIKit `UIPasteboard`, as
 well as the AppKit `NSPasteboard`.
 */
public protocol PasteboardImageReader {

    ...
}
```

This improves discoverability a whole lot, since developers can now browse the documentation to find out which types that implement this protocol, as well as what the protocol can do.

Since we now have a protocol that defines the functionality, we can extend it further with more functions, for instance to get whether or not the pasteboard has any images.

```swift
public extension PasteboardImageReader {

    var hasImages: Bool {
        guard let images = images else { return false }
        return !images.isEmpty
    }
}
```

Another great benefit with this approach, is that any type can implement this protocol and get access to the additional functionality. In this case, it's not much, but when you work with multi-platform codebases, this can be a great way to reduce the amount of code you have to write for each type.

But that's a discussion for another post.


## Conclusion

Extensions are convenient, but you should pay attention to how you use extensions in your code, and if you would benefit from defining extensions through protocols instead.