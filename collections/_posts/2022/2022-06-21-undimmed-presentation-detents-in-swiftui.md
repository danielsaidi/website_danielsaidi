---
title:  Undimmed presentation detents in SwiftUI
date:   2022-06-21 08:00:00 +0000
tags:   article swiftui sheet

icon:   swiftui
assets: /assets/blog/2022/2022-06-21/
tweet:  https://twitter.com/danielsaidi/status/1539343541155028993?s=20&t=mIWJ4rucrEUZSxr9gkm6MA

article:    https://danielsaidi.com/blog/2022/06/15/swiftui-4-custom-sheet-sizes
swiftuikit: https://github.com/danielsaidi/SwiftUIKit

ericlewis:  https://twitter.com/ericlewis
kzyryanov:  https://twitter.com/kzyryanov
tgrapperon: https://twitter.com/tgrapperon
---

SwiftUI 4 adds a bunch of amazing features, such as custom sized sheets. However, the current sheets will always dim the underlying view when they are presented, even when they use a smaller size. Let's look at how to fix this.


## Background

I recently wrote about custom sized sheets in SwiftUI in [this article]({{page.article}}). Check it out for some background information and how to use the new `presentationDetents` view modifier to specify sheet sizes.

Even though this API is easy to use, it currently doesn't let you do all the things you can in UIKit. For instance, you can't specify a custom corner radius, which is very much possible in UIKit.

However, while custom corner radius is a minor feature, a huge feature (I would even call it mandatory) that's currently missing is the option to keep the underlying view undimmed when a sheet is presented.

Sadly, SwiftUI sheets will always dim their underlying views, even when they are presented as overlays. This means that you can't build apps like Apple Maps, where a small sheet is always presented over an always interactable map.

This is how a SwiftUI map app would behave if we were to present a small sheet over a fullscreen map:

![A SwiftUI map app without and with a small sheet overlay]({{page.assets}}/maps.jpg){:style="width:650px"}

As you can see, the underlying view becomes disabled when the sheet is presented. This won't do if we want to build an app where the underlying view should be interactable while the sheet is presented.


## Undimming the underlying view in UIKit

In UIKit, custom sheet sizes were introduced in iOS 15. There, the sheet presentation controller also has a property called `largestUndimmedDetentIdentifier` that lets you specify for which largest detent the underlying view should still be undimmed and enabled.

For instance, if you want the underlying view to be enabled up to and including a `.medium` sheet size, you can add this code to your sheet's view controller:

```swift
sheetPresentationController?.largestUndimmedDetentIdentifier = .medium
```

This works great and will let you build apps where the sheet is presented over a still enabled underlying view. However, this feature is sadly not available in SwiftUI at the moment, which I must say is a huge missed opportunity. I guess the team ran out of time, but it unfortunately cripples the custom sheet size feature quite a bit and makes a bunch of use-cases unsupported.

We can however add this feature to SwiftUI with a tiny, tiny fix, that lets us affect the sheet presentation controller from within the sheet. Let's take a look at how.


## Undimming the underlying view in SwiftUI

When I noticed this earlier today, I went on Twitter and cried for help. I quickly got a nice response from [tgrapperon]({{page.tgrapperon}}) who suggested using a `UIHostingController` to affect the sheet presentation controller.

So, I did just that. I want the fix to be as simple as revertable as possible, in case the feature is added in future versions or future iOS 16 beta versions. I therefore want the API to be as close to the current APIs as possible, which means that it should be implemented as a view extension.

I decided to call the extension `presentationDetents`, just like the existing APIs. However, instead of having unnamed detents parameters, I instead give this parameter the public name `undimmed`:

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

We will now create a `UIViewControllerRepresentable` that wraps a `UIHostingController` that in turn will manipulate the sheet presentation controller. Let's start with the hosting controller:

```swift
class UndimmedDetentController<Content: View>: UIHostingController<Content> {

    override func viewWillAppear(_ animated: Bool) {
        super.viewWillAppear(animated)
        sheetPresentationController?.largestUndimmedDetentIdentifier = .large
    }
}
```

The controller changes the largest undimmed detent identifier of the sheet presentation controller. Since the SwiftUI `.fraction` and `.height` detents aren't represented in UIKit, let's just go with `.large`.

Let's now define the `UIViewControllerRepresentable` that we will use in our view extension:

```swift
struct UndimmedDetentView: UIViewControllerRepresentable {

    var largestUndimmedDetent: PresentationDetent?

    func makeUIViewController(context: Context) -> UIViewController {
        UndimmedDetentController(rootView: Color.clear)
    }

    func updateUIViewController(_ uiViewController: UIViewController, context: Context) {
    }
}
```

The only thing this view controller representable does, is to return our custom controller, which means that it will affect the sheetp presentation controller when it's presented.

Let's do this by updating the view extension that we defined earlier:

```swift
extension View {

    func presentationDetents(
        undimmed detents: Set<PresentationDetent>
    ) -> some View {
        self.presentationDetents(detents)
            .background(UndimmedDetentView())
    }

    func presentationDetents(
        undimmed detents: Set<PresentationDetent>, 
        selection: Binding<PresentationDetent>
    ) -> some View {
        self.presentationDetents(detents, selection: selection)
            .background(UndimmedDetentView())
    }
}
```

Turns out that this fix actually works! If we now use `.presentationDetents(undimmed:)` instead of `.presentationDetents()` on our view, the underlying view will not be dimmed nor disabled.

However, there is still one thing that we have to fix. Although the underlying view no longer gets dimmed nor disabled, it still *looks* disabled. The buttons are still greyed out, even though they can be tapped.

After some more Twitter crying, I got an amazing bunch of information from [ericlewis]({{page.ericlewis}}) who pointed out that I needed to adjust the tint adjustmenet mode to stop the view from being rendered as disabled:

```swift
override func viewWillAppear(_ animated: Bool) {
    super.viewWillAppear(animated)
    sheetPresentationController?.largestUndimmedDetentIdentifier = .large
    parent?.presentingViewController?.view.tintAdjustmentMode = .normal
}
```

If we add this and run our app again, everything now works great. The underlying map is still interactable and looks enabled, even when we present a sheet over it:

![A SwiftUI map app without and with a small, non-dimming sheet]({{page.assets}}/maps-working.jpg){:style="width:650px"}

We can now use the `.presentationDetents(undimmed:)` instead of `.presentationDetents()` until Apple updates SwiftUI to support this natively. Hopefully, it won't take too long.


## Conclusion

SwiftUI 4's custom sized sheets are amazing, but unfortunately some critical things are still missing. If you want to use non-dimming sheets in your apps, I hope that this article helped you out.

Big, big thanks to [kzyryanov]({{page.kzyryanov}}) for notifying me about this limitation and to [tgrapperon]({{page.tgrapperon}}) and [ericlewis]({{page.ericlewis}}) for your amazing help! You are what makes this Internet thing still being great!

I have added this extension to [SwiftUIKit]({{page.swiftuikit}}). Feel free to try it out and let me know what you think, and please let me know if you find any more things that need fixing.
