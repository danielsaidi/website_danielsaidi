---
title:  Removing the iOS home indicator in SwiftUI
date:   2022-08-01 06:00:00 +0000
tags:   swiftui

icon:   swiftui
assets: /assets/blog/2022/220801/

tweet:  https://twitter.com/danielsaidi/status/1554022675873398784?s=20&t=Qlh0zpBnm2otxJslq1R_yQ
---

In SwiftUI 4 and iOS 16, you will finally be able to hide the home indicator on iPhone and iPad devices that don't have a home button, without resorting to UIKit hacks. Let's see how.

In SwiftUI, we have so far not been able to hide the bottom home indicator that appears on iPhone and iPad devices that lack a home button:

![A screenshot of an iOS home indicator]({{page.assets}}home-indicator.png){:class="plain" width="450px"}

You *could* get it to work with UIKit-based hacks, at least earlier, but it was harder to apply the more SwiftUI evolved and the less it relied on UIKit.

In SwiftUI 4, you will finally be able to hide the home indicator in a clean way, using the brand new `persistentSystemOverlays` view modifier.

As an example, consider the following SwiftUI view:

```swift
struct ContentView: View {

    var body: some View {
        Color.red
            .ignoresSafeArea(.all)
    }
}
```

This view will take up the entire screen and show a home indicator on devices that lack a home button:

![A screenshot of an iOS app with a home indicator]({{page.assets}}home-indicator-original.png){:class="plain" width="250px"}

In SwiftUI 4, when you target iOS 16, macOS 13, tvOS 16 or watchOS 9, you can now remove the home indicator by just adding `.persistentSystemOverlays(.hidden)` to the view, like this:

```swift
struct ContentView: View {

    var body: some View {
        Color.red
            .ignoresSafeArea(.all)
            .persistentSystemOverlays(.hidden)
    }
}
```

When you run this again, the view will no longer show the home indicator...or more precise: the indicator will be shown briefly, then fade out:

![A screenshot of an iOS app without a home indicator]({{page.assets}}home-indicator-removed.png){:class="plain" width="250px"}

I just love how clean and easy these things are with SwiftUI, where UIKit instead requires you to override properties in your view controller.

Note that while this modifier can be used to hide the home indicator in iPhone and iPad, this effect is not its communicated intent, which rather is to affect "persistent system overlays". This means that the effect can change depending on context, such as the device, view, platform etc. I'd prefer to have an exclusive home indicator view modifier, but since this is all we have, make sure that applying it doesn't cause any unwanted side-effects to your app.

Also, do note that you should only use this view modifier in very specific cases, since the home indicator should most probably be used in most cases. I use it to hide the home indicator in a video player, which makes sense. Full-screen games is another example of when the indicator maybe should be hidden. All in all, make sure to consider your use-case before hiding the indicator, and you'll be fine.


## Targeting older OS versions

Since the new `persistentSystemOverlays` view modifier is only available for iOS 16, macOS 13, tvOS 16 and watchOS 9, I have created a view extension that only applies the modifier if it's available:

```swift
extension View {

    func prefersPersistentSystemOverlaysHidden() -> some View {
        if #available(iOS 16.0, macOS 13.0, tvOS 16.0, watchOS 9.0, *) {
            return self.persistentSystemOverlays(.hidden)
        } else {
            return self
        }
    }
}
```

Since my app targets iOS 14, this extension lets me apply the view modifier without having to care if it has any effect or not. This is fine, since hiding the home indicator is just a nice to have feature.

Note that this code will only compile if you use the new Xcode 14 beta. If you have to add it to a project that still needs to build for Xcode 13, you can add an `#if compiler(>=5.7)` check: 


```swift
extension View {

    func prefersPersistentSystemOverlaysHidden() -> some View {
        #if compiler(>=5.7)
        if #available(iOS 16.0, macOS 13.0, tvOS 16.0, watchOS 9.0, *) {
            return self.persistentSystemOverlays(.hidden)
        } else {
            return self
        }
        #else
        return self
        #endif
    }
}
```

This will make Xcode return the original view when the code is built using Xcode 13. Once Xcode 14 is released, you can remove this compiler check.