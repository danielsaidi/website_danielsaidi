---
title:  Backporting the SwiftUI 4 ImageRenderer to iOS 13
date:   2022-06-22 08:00:00 +0000
tags:   swiftui image-renderer

icon:   swiftui
assets: /assets/blog/2022/220622/
tweet:  https://twitter.com/danielsaidi/status/1539875509814935552?s=20&t=eoAA2uzGrsotG6V9s3-PFQ

article: http://danielsaidi.com/blog/2022/06/20/using-the-swiftui-imagerenderer
matt: https://twitter.com/mattie
matt-article: https://www.chimehq.com/blog/swift-and-old-sdks
---

SwiftUI 4 introduces a new `ImageRenderer` that can be used to render any SwiftUI view as an image in iOS 16, macOS 13, tvOS 16 and watchOS 9. Let's look at how to backport it to iOS 13.

{% include kankoda/data/open-source.html name="SwiftUIKit" %}


## Background

Before `ImageRenderer`, I used the following extension to render any SwiftUI `View` to an image:

```swift
#if os(iOS)
import SwiftUI

public extension View {
    
    func snapshot(origin: CGPoint = .zero, size: CGSize) -> UIImage {
        let window = UIWindow(frame: CGRect(origin: origin, size: size))
        let hosting = UIHostingController(rootView: self)
        hosting.view.frame = window.frame
        window.addSubview(hosting.view)
        window.makeKeyAndVisible()
        return hosting.view.renderedImage
    }
}

private extension UIView {
    
    var renderedImage: UIImage {
        UIGraphicsBeginImageContextWithOptions(bounds.size, false, 0.0)
        let context = UIGraphicsGetCurrentContext()!
        layer.render(in: context)
        let image = UIGraphicsGetImageFromCurrentImageContext()!
        UIGraphicsEndImageContext()
        return image
    }
}
#endif
```

The extension lets you to specify a custom `size`, then uses a `UIWindow`, a `UIHostingController` and a `UIView` to render the view in a window with the provided size.

While the new `ImageRenderer` supports all platforms, this extension only supports iOS. However, the extension supports iOS 13, while the renderer requires iOS 16.

This makes it possible for us to backport the `ImageRenderer` all the way back to iOS 13, in a way that provides almost the same APIs as the native renderer. Let's take a look at how.


## Backporting ImageRenderer

When backporting `ImageRenderer` to iOS 13, we first need to make sure that we only define our own renderer in iOS 15 and earlier. If an app targets iOS 16, it should get the native renderer instead.

However, there doesn't seem to be a convenient way to make a type unavailable for a certain upper os version. `if #unavailable(iOS 16)` check doesn't work, since it causes a `"Statements are not allowed at the top level"` warning for types, and there's no `@unavailable` attribute.

To work around this, [Matt Massicotte]({{page.matt}}) has written a great [post]({{page.matt-article}}), in which he suggests using a `compiler` check instead, which lets us check the version of the Swift compiler. 

Since the native `ImageRenderer` is available in SwiftUI 4 and Xcode 14, we can make our own renderer unavailable in Swift 5.7 and later.

```swift
#if os(iOS) && compiler(<5.7)
import SwiftUI

public class ImageRenderer<Content: View> {
}
#endif
```

The `#if` check makes sure that the renderer is only available for iOS 15 and before, although it would be great to have an explicit `unavailable` check instead.

We can now define an initializer and properties for the renderer:

```swift
public class ImageRenderer<Content: View> {

    @MainActor
    public init(
        content: Content,
        size: CGSize,
        scale: CGFloat? = nil
    ) {
        self.content = content
        self.size = size
        self.scale = scale ?? UIScreen.main.scale
    }

    private let content: Content
    private let size: CGSize

    @MainActor
    public var scale: CGFloat
}
```

This initializer is a bit different than the native renderer initializer. Since we'll use the extension that we saw earlier, we have to provide an explicit size when we create the renderer. 

We also add an optional scale parameter for convenience, to avoid having to specify it. Unlike the native renderer, we'll use the screen resolution instead of 1.

We can now copy the code from the extension to implement a `uiImage` property:

```swift
public class ImageRenderer<Content: View> {

    ...

    @MainActor
    public var uiImage: UIImage {
        let window = UIWindow(frame: CGRect(origin: .zero, size: size))
        let hosting = UIHostingController(rootView: content)
        hosting.view.frame = window.frame
        window.addSubview(hosting.view)
        window.makeKeyAndVisible()
        return hosting.view.renderedImage
    }
}

private extension UIView {

    var renderedImage: UIImage {
        UIGraphicsBeginImageContextWithOptions(bounds.size, false, 0.0)
        let context = UIGraphicsGetCurrentContext()!
        layer.render(in: context)
        let image = UIGraphicsGetImageFromCurrentImageContext()!
        UIGraphicsEndImageContext()
        return image
    }
}
```

That's it! We can now use this backported renderer in the app that we created in [Monday's article]({{page.article}}) (about using the native `ImageRenderer`) instead of the native one:

```swift
extension ContentView {
    
    func generateSnapshot() {
        Task {
            let renderer = await ImageRenderer(
                content: viewToSnapshot("ImageRenderer"),
                size: CGSize(width: 150, height: 50))
            if let image = await renderer.uiImage {
                self.snapshot = image
            }
        }
    }
}
```

The generated snapshot looks great, since it uses the screen resolution by default:

![A screenshot that shows an original SwiftUI view and a snapshot generated by our backported image renderer]({{page.assets}}result.png)

However, note that the backported renderer is only available in iOS. It's not available in macOS, tvOS or watchOS, since it uses iOS-specific functionality to generate the snapshot.


## Conclusion

The SwiftUI 4 `ImageRenderer` does a great job of rendering snapshots, but since it's only available for iOS 16, macOS 13, tvOS 16 and watchOS 9, it may take some time before you can start using it.

To provide a similar solution for older versions, we can backport a renderer that behaves almost as the native renderer. This lets us use a backported version for now and replace it with a native one later.

You can find a backported renderer in [SwiftUIKit]({{project.url}}). Feel free to try it out and let me know what you think.