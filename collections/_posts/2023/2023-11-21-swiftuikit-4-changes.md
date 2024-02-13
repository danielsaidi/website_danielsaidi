---
title:  SwiftUIKit 4.0 changes
date:   2023-11-21 06:00:00 +0000
tags:   swiftui open-source

icon:   swiftui
---

{% include kankoda/data/open-source.html name="SwiftUIKit" %}
I'm about to release a new major version of [{{project.name}}]({{project.url}}). This version will remove no longer needed things, bump the deployment targets and merge the library with SwiftKit.


## New deployment targets

[SwiftUIKit]({{project.url}}) previously targeted iOS 13, macOS 11, tvOS 13 & watchOS 7. With my goal of supporting two major platform versions, SwiftUI will now require iOS 15, macOS 13, tvOS 15 & watchOS 8.


## Removing no longer needed types

SwiftUI's goal has been to provide you with easier ways to do things, or to add ways to do newer things on older OS versions. 

With the deployment targets being bumped, some things will be removed, since there are now native ways of achieving the same thing.


## ActionToggle is removed

The `ActionToggle` was a toggle that triggered an "on" or "off" action as you toggled it. This was straightforward and easy to use, but the code for it was a bit much.

Returning to this code, it's now painfully clear that the same can be achieved like this:

```swift
struct MyView: View {

    @State
    private var isOn = false

    var body: some View {
        Toggle("Toggle me", isOn: $isOn)
            .onChange(of: isOn) {
                if $0 {
                    // Perform "on" action
                } else {
                    // Perform "off" action
                }
            }
    }
}
```

I created the toggle when I had many action-based toggles and all `onChange` code became repetitive. Still, I don't think that such a specific thing belongs to a library like SwiftUI.


## ScanCodeGenerator is replaced

The `ScanCodeGenerator` protocol and `StandardScanCodeGenerator` implementation offered a convenient way to generate scan codes, like QR codes, barcodes, Aztek etc.

My main reason for creating these types ealier, was for the utils to show up in DocC, since extension to native types didn't show up when you generated DocC in Xcode 14. 

Since Xcode will now include documentation for native types, I've converted these utils to plain `Image` and `UIImage/NSImage` extensions:

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

You can generate a scan code image for SwiftUI, UIKit and AppKit, using basic initializers:

```swift
Image(scanCode: "123456789", type: .qr, scale: 5)
```

It's easier to use these initializers, and since they show up in DocC, they are discoverable.


## FormActionButton is replaced with a style

SwiftUIKit has many different button and view types, which is hard to manage. Instead of having separate button views, button styles scale better.

As an example, the `FormActionButton` will be replaced with a `FormActionButtonStyle` that you can apply like this:

```swift
HStack {
    Button(...)
    Button(...)
    Button(...).buttonStyle(.customStyle)
}
.buttonStyle(.formGroup)
```

This style can be customized and you can modify the global default for an entire app. This reduces complexity and increases composability.


## Remove types that have native iOS 16 types

SwiftUIKit will remove custom types that have native versions in iOS 16 and earlier. One example is `FormTextEditor`, which can now be replaced with a multiline, native `TextField`.


## Conclusion

SwiftUIKit 4.0 cleans up a lot. It has some breaking changes and removes many types, but you can always go and grab the things you need from earlier versions.

I prepare the release in the [SwiftUIKit]({{project.url}}) `v4` branch. Feel free to give it a try and let me know what you think.