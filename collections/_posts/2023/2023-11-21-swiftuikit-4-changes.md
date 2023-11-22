---
title:  SwiftUIKit 4.0 changes
date:   2023-11-21 06:00:00 +0000
tags:   swiftui open-source

icon:   swiftui
---

I am about to create a new major version of SwiftUIKit. This version will remove no longer needed things, bump the deployment targets and merge the library with SwiftKit.

{% include kankoda/data/open-source.html name="SwiftUIKit" %}


## New deployment targets

[SwiftUIKit]({{project.url}}) previously targeted iOS 13, macOS 11, tvOS 13 and watchOS 7. With my goal of supporting two major versions back, this new major version means that SwiftUI drops support for older OS versions and will now require iOS 15, macOS 13, tvOS 15 and watchOS 8.


## Removing no longer needed types

SwiftUI's goal has been to provide you with easier ways to do things, or to add ways to do newer things on older OS versions. 

With the deployment targets being bumped, some things will therefore be removed, since there are now other ways of achieving the same thing.

And sometimes, I've added things that can be done in an easier way with the native tool given to us. One such example is the `ActionToggle`, which will be removed.


## ActionToggle is removed

The `ActionToggle` was a toggle that triggered either an "on" action or an "off" action as you toggled it. It was pretty straightforward and easy to use, but the code for it was a bit much.

Being removed from this code for a while, it's now painfully clear that the same can be achieved like this:

```swift
struct MyView: View {

    @State
    private var isOn = false

    var body: some View {
        Toggle("Toggle me", isOn: $isOn)
            .onChange(of: isOn) {
                if isOn {
                    // Perform "on" action
                } else {
                    // Perform "off" action
                }
            }
    }
}
```

The reason why I made the toggle, was that we had many action-trigger toggles that all `onChange` code became repetitive. Still, I now think that such a specific thing doesn't belong in a library like SwiftUI.


## ScanCodeGenerator is replaced

The `ScanCodeGenerator` protocol and `StandardScanCodeGenerator` class offered a convenient way to generate scan codes, like QR codes, barcodes, Aztek etc.

My main reason for creating these types ealier, was for the utils to show up in DocC, since extension to native types didn't show up when you generated DocC documentation in Xcode 14 and earlier. 

Since Xcode will now include documentation for native types, I've converted these utils to plain `Image` and `UIImage/NSImage` extensions instead:

```swift
public extension Image {
    
    init?(
        scanCode value: String,
        type: ScanCodeType,
        scale: CGFloat = 1
    ) {
        let image = ImageRepresentable(
            scanCode: value,
            type: type,
            scale: scale
        )
        guard let image else { return nil }
        self.init(image: image)
    }
}

public extension ImageRepresentable {
    
    convenience init?(
        scanCode value: String,
        type: ScanCodeType,
        scale: CGFloat = 1
    ) {
        let image = Self.generateCode(
            value: value,
            type: type,
            scale: scale
        )
        guard let image else { return nil }
        self.init(cgImage: image)
    }
}

private extension ImageRepresentable {
    
    static func generateCode(
        value: String,
        type: ScanCodeType,
        scale: CGFloat
    ) -> CGImage? {
        let ciContext = CIContext()
        let data = value.data(using: .utf8)
        let transform = CGAffineTransform(scaleX: scale, y: scale)
        guard let filter = CIFilter(name: type.ciFilterName) else { return nil }
        filter.setValue(data, forKey: "inputMessage")
        guard let ciImage = filter.outputImage?.transformed(by: transform) else { return nil }
        return ciContext.createCGImage(ciImage, from: ciImage.extent)
    }
}

#if os(macOS)
private extension ImageRepresentable {
    
    convenience init(cgImage: CGImage) {
        self.init(cgImage: cgImage, size: .zero)
    }
}
#endif
```

You can then generate a scan code image for SwiftUI, UIKit and AppKit, using very basic initializers:

```swift
Image(scanCode: "123456789", type: .qr, scale: 5)
```

It will be easier to use these types without having to first create a generator, and since the extensions will show up in DocC, they will still be discoverable.


## FormActionButton is replaced with a style

SwiftUIKit currently has many specific button and view types, which is a bit hard to manage as the number of variations grow. Instead of separate button view types, having button styles scale better.

As an example, the `FormActionButton` will be replaced with just having a `FormActionButtonStyle` that you can apply like this:

```swift
HStack {
    Button(...)
    Button(...)
    Button(...).buttonStyle(.customStyle)
}
.buttonStyle(.formGroup)
```

This style can be customized to great extent. You can also replace the default style to affect the global default style for your entire app. This reduces complexity and increases composability a great deal.


## Remove types that have native iOS 16 types

SwiftUIKit will also remove custom types that have native versions in iOS 16 and earlier. One example is `FormTextEditor`, which can now be replaced with a multiline, native `TextField`.


## Conclusion

SwiftUIKit 4.0 will clean up many things in the library. It has some breaking changes and remove quite a few types, but you can always go and grab the things you need from earlier versions.

I prepare the release in the [SwiftUIKit]({{project.url}}) `v4` branch. Feel free to try it out and let me know what you think.