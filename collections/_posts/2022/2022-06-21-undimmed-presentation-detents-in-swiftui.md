---
title:  Undimmed presentation detents in SwiftUI
date:   2022-06-21 08:00:00 +0000
tags:   swiftui sheet presentation-detents

icon:   swiftui
assets: /assets/blog/2022/2022-06-21/
tweet:  https://twitter.com/danielsaidi/status/1539343541155028993?s=20&t=mIWJ4rucrEUZSxr9gkm6MA

article:    https://danielsaidi.com/blog/2022/06/15/swiftui-4-custom-sheet-sizes
swiftuikit: https://github.com/danielsaidi/SwiftUIKit

edward:     https://twitter.com/edwardsainsbury 
ericlewis:  https://twitter.com/ericlewis
kzyryanov:  https://twitter.com/kzyryanov
tgrapperon: https://twitter.com/tgrapperon
---

SwiftUI 4 adds a bunch of amazing features, such as custom sized sheets. However, the current sheets will always dim the underlying view when they are presented, even when they use a smaller size. Let's look at how to fix this.

> Update 2022-11-01: I have updated this post with a new way to handle the largest undimmed presentation detents, that doesn't require always including `.large` in the provided detent collection.

> Update 2022-09-04: I noticed that the approach in this post now only works if `.large` is in the provided set of detents. Furthermore, SwiftUI previews started crashing when they used the undimmed modifier. The post has been updated with a setup that doesn't cause a crash and the modifier now always adds `.large` to the provided set of detents, to ensure that undimming works.


## Background

I [recently wrote]({{page.article}}) about how you can use the new `presentationDetents` view modifier to setup custom sheet sizes in SwiftUI 4.

Even though this API is nice and easy to use, it doesn't let you do all you can in UIKit. For instance, you can't keep the underlying view undimmed when a sheet is presented. This means that you can't build apps like Apple Maps, where a small sheet is always presented over an always interactable map.

This is how a SwiftUI map app would behave if we were to present a small sheet over a fullscreen map:

![A SwiftUI map app without and with a small sheet overlay]({{page.assets}}/maps.jpg){:style="width:650px"}

Even if it's hard to see in the images above, the underlying map becomes disabled when the sheet is presented. This won't do if we want the map to be interactable while the sheet is presented.


## Undimming the underlying view in UIKit

In UIKit, custom sheet sizes were introduced in iOS 15, with a `largestUndimmedDetentIdentifier` property that lets you specify for which detents the underlying view should be undimmed and enabled.

For instance, if you want the underlying view to be enabled up to and including a `.medium` sheet size, you can add this code to your sheet presentation controller:

```swift
sheetPresentationController?.largestUndimmedDetentIdentifier = .medium
```

This feature is not available in SwiftUI at the moment, which means that SwiftUI sheets will always dim the underlying view. We can however add support for undimming to SwiftUI with a tiny fix, which will let us affect the sheet presentation controller from SwiftUI.


## Undimming the underlying view in SwiftUI

When I went to Twitter to cry about these missing capabilities, I quickly got a response from [tgrapperon]({{page.tgrapperon}}) who suggested using a `UIHostingController` to affect the sheet presentation controller.

So, I did just that. I want the workaround to be as close to the current APIs as possible, to make it easy to replace when the feature is added in a future version of SwiftUI.

The native SwiftUI extension that is used to set custom sheet sizes is called `presentationDetents` and is used like this:

```swift
myView.presentationDetents([.medium, .large])
```

I therefore decided to call the undim supporting extension `presentationDetents` as well, but instead of having an unnamed detents parameters, I called it `undimmed`:

```swift
extension View {

    func presentationDetents(
        undimmed detents: Set<PresentationDetent>
    ) -> some View {
        self.presentationDetents(detents)
        // Now what???
    }

    func presentationDetents(
        undimmed detents: Set<PresentationDetent>, 
        selection: Binding<PresentationDetent>
    ) -> some View {
        self.presentationDetents(detents, selection: selection)
        // Now what???
    }
}
```

Before we continue, I want to pause and address an Xcode 14 beta bug that caused undimming to stop working unless you provided a largest undimmed detent size, which can only be defined with UIKit.

Since we therefore will need to use both the SwiftUI `PresentationDetent` as well as the UIKit detents identifier, and there is no clean way to bridge the two, I decided to add a new enum:

```swift
enum UndimmedPresentationDetent {

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

The `uiKitIdentifier` property also needs new identifier extensions for `.fraction` and `.height`:

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

Let's also add an extension to any collection that contains `UndimmedPresentationDetent`, to make it easy to create a `PresentationDetent` set:

```swift
extension Collection where Element == UndimmedPresentationDetent {

    var swiftUISet: Set<PresentationDetent> {
        Set(map { $0.swiftUIDetent })
    }
}
```

We can now create a `UIViewControllerRepresentable` that can wrap a `UIViewController` that can manipulate the sheet presentation controller.

Let's start with the controller:

```swift
private class UndimmedDetentController: UIViewController {

    var largestUndimmed: UndimmedPresentationDetent?

    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
        avoidDimmingParent()
        avoidDisablingControls()
    }

    func avoidDimmingParent() {
        let id = largestUndimmed?.uiKitIdentifier
        sheetPresentationController?.largestUndimmedDetentIdentifier = id
    }

    func avoidDisablingControls() {
        presentingViewController?.view.tintAdjustmentMode = .normal
    }
}
```

We can provide this controller with a `largestUndimmedDetent` that it then used to configure the shet presentation controller. We also need to tweak the tint to avoid that undimmed sheets still look dimmed.

Let's now define a `UIViewControllerRepresentable` that we can use in a SwiftUI view extension:

```swift
private struct UndimmedDetentView: UIViewControllerRepresentable {

    var largestUndimmed: UndimmedPresentationDetent?

    func makeUIViewController(context: Context) -> UIViewController {
        let result = UndimmedDetentController()
        result.largestUndimmedDetent = largestUndimmed
        return result
    }

    func updateUIViewController(_ uiViewController: UIViewController, context: Context) {
    }
}
```

The only thing this does is to return our custom controller, which affects the sheet presentation controller.

We can now redefine the view extension that we defined earlier, to support both undimmed detents and the largest undimmed detent:

```swift
public extension View {

    func presentationDetents(
        undimmed detents: [UndimmedPresentationDetent],
        largestUndimmed: UndimmedPresentationDetent? = nil
    ) -> some View {
        self.background(UndimmedDetentView(largestUndimmed: largestUndimmed ?? detents.last))
            .presentationDetents(detents.swiftUISet)
    }

    func presentationDetents(
        undimmed detents: [UndimmedPresentationDetent],
        largestUndimmed: UndimmedPresentationDetent? = nil,
        selection: Binding<PresentationDetent>
    ) -> some View {
        self.background(UndimmedDetentView(largestUndimmed: largestUndimmed ?? detents.last))
            .presentationDetents(
                Set(detents.swiftUISet),
                selection: selection
            )
    }
}
```

These extensions let us specify a set of `undimmed` detents, as well as a `largestUndimmed` detent. If no largest detent is provided, the last of the `undimmed` detents is used.

Now, guess what? This actually works! If we use `.presentationDetents(undimmed:)` instead of the native `.presentationDetents()`, the underlying view will not be dimmed nor disabled.

This post used to have a separate section about the `tintAdjustmentMode` fix, but I put it all together to make it more compact. it was however provided by [ericlewis]({{page.ericlewis}}), so all cred to him for making this work!

We can now use the `.presentationDetents(undimmed:)` instead of `.presentationDetents()` until Apple updates SwiftUI to support this natively. Hopefully, it won't take too long.


## Conclusion

SwiftUI 4 custom sized sheets are amazing, but unfortunately some things are still missing. If you want to keep the underlying views undimmed as sheets are presented, I hope that this article helped you out.

Big, big thanks to [kzyryanov]({{page.kzyryanov}}) for notifying me about this and to [tgrapperon]({{page.tgrapperon}}) and [ericlewis]({{page.ericlewis}}) for your amazing help! You are what makes this Internet thing still being great!

I have added this extension to [SwiftUIKit]({{page.swiftuikit}}). Feel free to try it out and let me know what you think, and just let me know if you find any more things that need fixing.
