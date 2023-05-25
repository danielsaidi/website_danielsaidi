---
title:  Undimmed presentation detents in SwiftUI
date:   2022-06-21 08:00:00 +0000
tags:   swiftui sheet presentation-detents

icon:   swiftui
assets: /assets/blog/2022/2022-06-21/
tweet:  https://twitter.com/danielsaidi/status/1539343541155028993?s=20&t=mIWJ4rucrEUZSxr9gkm6MA

article:    https://danielsaidi.com/blog/2022/06/15/swiftui-4-custom-sheet-sizes
swiftuikit: https://github.com/danielsaidi/SwiftUIKit
extension:  https://github.com/danielsaidi/SwiftUIKit/blob/master/Sources/SwiftUIKit/Presentation/Detents/PresentationDetentsViewModifier.swift

edward:     https://twitter.com/edwardsainsbury 
ericlewis:  https://twitter.com/ericlewis
kzyryanov:  https://twitter.com/kzyryanov
tgrapperon: https://twitter.com/tgrapperon
---

SwiftUI 4 adds a bunch of great features, such as custom sized sheets. However, these sheets will always dim the underlying view, even when they use a smaller size. Let's look at how to fix this.

> Update 2023-05-25: After some discussions and GitHub issues, I have cleaned up the code quite a bit. This post now has the latest version. You can always visit [SwiftUIKit]({{page.swiftuikit}}) for the latest version of the code.


## Background

I [recently wrote]({{page.article}}) about how you can use the new `presentationDetents` view modifier to set up sheets with custom sizes in SwiftUI 4.

Even though this is great, it always dims the underlying when a sheet is presented. This means that we can't build an apps like Apple Maps, where a sheet is always presented over an always interactable map:

![A SwiftUI map app without and with a small sheet overlay]({{page.assets}}/maps.jpg){:style="width:650px"}

In the images above, the map becomes disabled when the sheet is presented with this new modifier, and will hide the sheet when you tap on it. We thus need a way to keep the underlying view undimmed.


## Undimming the underlying view in UIKit

In UIKit, custom sheet sizes were introduced in iOS 15, with a `largestUndimmedDetentIdentifier` property that lets you specify for which detents the underlying view should be undimmed and enabled.

For instance, if you want the underlying view to be enabled up to and including a `.medium` sheet size, you can add this code to your sheet presentation controller:

```swift
sheetPresentationController?.largestUndimmedDetentIdentifier = .medium
```

This feature is not available in SwiftUI at the moment, but we can add support for it with a tiny fix, which will let us affect the sheet presentation controller from SwiftUI.


## Undimming the underlying view in SwiftUI

When I went to Twitter to cry about these missing capabilities, I quickly got a response from [tgrapperon]({{page.tgrapperon}}) who suggested using a `UIHostingController` to affect the sheet presentation controller.

So, I did just that. I want the workaround to be as close to the current APIs as possible, to make it easy to replace when the feature is added in a future version of SwiftUI.

The native SwiftUI extension that is used to set custom sheet sizes is called `presentationDetents`:

```swift
myView.presentationDetents([.medium, .large])
```

I decided to call my view modifier `presentationDetents` as well, but also added a `largestUndimmed` parameter to support undimming and an optional `selection` binding:

```swift
extension View {

    func presentationDetents(
        _ detents: [PresentationDetent],
        largestUndimmed: PresentationDetent,
        selection: Binding<PresentationDetent>? = nil
    ) -> some View {
        // Insert magic here
    }
}
```

Unfortunately, things will not be this easy. Since we have to use UIKit to make undimming work, we need to find a way to bridge the SwiftUI `PresentationDetent` with the UIKit one.


## Bridging UIKit and SwiftUI

Since there is no clean way to bridge the two detent types, I decided to add a new enum that does this:

```swift
public enum PresentationDetentReference: Hashable {

    case large

    case medium

    case fraction(_ value: CGFloat)

    case height(_ value: CGFloat)

    var swiftUIDetent: PresentationDetent {
        switch self {
        case .large: return .large
        case .medium: return .medium
        case .fraction(let value): return .fraction(value)
        case .height(let value): return .height(value)
        }
    }

    var uiKitIdentifier: UISheetPresentationController.Detent.Identifier {
        switch self {
        case .large: return .large
        case .medium: return .medium
        case .fraction(let value): return .fraction(value)
        case .height(let value): return .height(value)
        }
    }
}
```

where the `uiKitIdentifier` property needs these extensions for `.fraction` and `.height`:

```swift
extension UISheetPresentationController.Detent.Identifier {

    static func fraction(_ value: CGFloat) -> Self {
        .init("Fraction:\(String(format: "%.1f", value))")
    }

    static func height(_ value: CGFloat) -> Self {
        .init("Height:\(value)")
    }
}
```

Let's also add a reference extension that makes it easy to create a SwiftUI `PresentationDetent` set:

```swift
extension Collection where Element == PresentationDetentReference {

    var swiftUISet: Set<PresentationDetent> {
        Set(map { $0.swiftUIDetent })
    }
}
```

With this in place, we can now handle presentation detents in UIKit and SwiftUI with just a single type.


## Controlling SwiftUI undimming with UIKit

To add undimming support to SwiftUI, we can create a `UIViewControllerRepresentable` that wraps a `UIViewController` that we can use to manipulate the underlying views.

Let's first create a view controller to set the largest undimmed detent of the shet presentation controller and tweak the tint adjustment mode, to avoid a bug where undimmed sheets still look dimmed:

```swift
private class UndimmedDetentViewController: UIViewController {

    var largestUndimmedDetent: PresentationDetentReference?

    override func viewDidLayoutSubviews() {
        super.viewDidLayoutSubviews()
        avoidDimmingParent()
        avoidDisablingControls()
    }

    func avoidDimmingParent() {
        let id = largestUndimmedDetent?.uiKitIdentifier
        sheetPresentationController?.largestUndimmedDetentIdentifier = id
    }

    func avoidDisablingControls() {
        presentingViewController?.view.tintAdjustmentMode = .normal
    }
}
```

We can now wrap the controller in a `UIViewControllerRepresentable` to make it available to SwiftUI:

```swift
private struct UndimmedDetentView: UIViewControllerRepresentable {

    var largestUndimmed: PresentationDetentReference?

    func makeUIViewController(
        context: Context
    ) -> UndimmedDetentViewController {
        let result = UndimmedDetentViewController()
        result.largestUndimmedDetent = largestUndimmed
        return result
    }

    func updateUIViewController(
        _ controller: UndimmedDetentViewController, 
        context: Context
    ) {
        controller.largestUndimmedDetent = largestUndimmed
    }
}
```

If we now add this view to a SwiftUI view hierarchy, it will adjust the underlying UIKit views to play well with any undimming that we want to apply to our custom sized sheets.


## Undimming the underlying view in SwiftUI

With all these new things in place, we can now create a view modifier that lets us provide presentation detents together with a largest undimmed detent value:

```swift
public struct PresentationDetentsViewModifier: ViewModifier {

    public init(
        presentationDetents: [PresentationDetentReference],
        largestUndimmed: PresentationDetentReference,
        selection: Binding<PresentationDetent>? = nil
    ) {
        self.presentationDetents = presentationDetents + [largestUndimmed]
        self.largestUndimmed = largestUndimmed
        self.selection = selection
    }

    private let presentationDetents: [PresentationDetentReference]
    private let largestUndimmed: PresentationDetentReference
    private let selection: Binding<PresentationDetent>?

    public func body(content: Content) -> some View {
        if let selection = selection {
            content
                .background(background)
                .presentationDetents(
                    Set(presentationDetents.swiftUISet),
                    selection: selection)
        } else {
            content
                .background(background)
                .presentationDetents(Set(presentationDetents.swiftUISet))
        }
    }
}

private extension PresentationDetentsViewModifier {

    var background: some View {
        UndimmedDetentView(
            largestUndimmed: largestUndimmed
        )
    }
}
```

The view modifier adds the `UndimmedDetentView` we defined earlier as a background, which makes the underlying UIKit view perform the required modifications.

We also have to add the `largestUndimmed` detent to the `presentationDetents` collection, to ensure that it's in that collection. If not, the undimming will not work.

We can now create a `presentationDetents` view extension as a shorthand to this new view modifier:

```swift
public extension View {

    func presentationDetents(
        _ detents: [PresentationDetentReference],
        largestUndimmed: PresentationDetentReference,
        selection: Binding<PresentationDetent>? = nil
    ) -> some View {
        self.modifier(
            PresentationDetentsViewModifier(
                presentationDetents: detents + [largestUndimmed],
                largestUndimmed: largestUndimmed,
                selection: selection
            )
        )
    }
}
```

This modifier looks just like the already existing, native view extensions, which means that it will sit nicely next to the native extensions and be easy to discover.

We can now use `.presentationDetents(:largestUndimmed:selectin:)` to get undimming support in our iOS 16 apps. 


## Conclusion

SwiftUI 4 custom sized sheets are greatamazing, but will unfortunately not let you keep the underlying view undimmed. This post presents a workaround until Apple adds this as a native feature.

Big thanks to [kzyryanov]({{page.kzyryanov}}) for notifying me about this, and to [tgrapperon]({{page.tgrapperon}}) and [ericlewis]({{page.ericlewis}}) for your amazing help! You are what makes this Internet thing still being great!

I have added [this extension]({{page.extension}}) to my [SwiftUIKit]({{page.swiftuikit}}) library, which I use for misc SwiftUI utilities. Feel free to try it out and let me know what you think, and if you find anything that needs fixing.
