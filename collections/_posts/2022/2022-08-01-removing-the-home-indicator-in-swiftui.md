---
title:  Removing the iOS home indicator in SwiftUI
date:   2022-08-01 06:00:00 +0000
tags:   swiftui

icon:   swiftui
assets: /assets/blog/22/0801/

tweet:  https://twitter.com/danielsaidi/status/1554022675873398784?s=20&t=Qlh0zpBnm2otxJslq1R_yQ
---

In SwiftUI 4 & iOS 16, you will finally be able to hide the home indicator on iPhone & iPad devices that don't have a home button, without resorting to UIKit-based hacks.

In SwiftUI, we have so far not been able to hide the bottom home indicator that appears on iPhone & iPad devices that lack a home button:

![A screenshot of an iOS home indicator]({{page.assets}}home-indicator.png){:class="plain" width="450px"}

You *could* get it to work with UIKit-based hacks, at least earlier, but it was harder to apply the more SwiftUI evolved and the less it relied on UIKit.

In SwiftUI 4, you are now finally able to hide the home indicator in a clean way, using the new `persistentSystemOverlays` view modifier.

As an example, consider the following SwiftUI view:

```swift
struct ContentView: View {

    var body: some View {
        Color.red
            .ignoresSafeArea(.all)
    }
}
```

This will take up the entire screen and show the home indicator on FaceID-based devices:

![A screenshot of an iOS app with a home indicator]({{page.assets}}home-indicator-original.png){:class="plain" width="250px"}

In SwiftUI 4, you can now remove the indicator with `.persistentSystemOverlays(.hidden)`:

```swift
struct ContentView: View {

    var body: some View {
        Color.red
            .ignoresSafeArea(.all)
            .persistentSystemOverlays(.hidden)
    }
}
```

This will no longer show the home indicator. Or rather, it will show briefly, then fade out:

![A screenshot of an iOS app without a home indicator]({{page.assets}}home-indicator-removed.png){:class="plain" width="250px"}

I just love how clean and easy these things are with SwiftUI, where UIKit instead requires you to override properties in your view controller.

Note that while this hides the home indicator on iPhone & iPad, the term "persistent system overlays" may mean different things depending on the context, such as the device. I would have preferred to have an exclusive home indicator modifier.

Also, note that the home indicator should be shown in most cases, so only hide it where it makes sense. For instance, I hide it in a full-screen video player. 

Full-screen games is another example of when the indicator maybe should be hidden. All in all, make sure to consider your use-case before hiding the indicator.


## Targeting older OS versions

Since the new `persistentSystemOverlays` modifier is only available for iOS 16, macOS 13, tvOS 16 & watchOS 9, I have created a view extension that only applies it if it's available:

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

Since my app targets iOS 14, this lets me apply the view modifier without having to care if it has any effect or not. This is fine, since hiding the home indicator is just nice to have.

Note that this will only compile if you use the new Xcode 14 beta. If you have to add it to a project that still needs to build for Xcode 13, you can add an `#if compiler(>=5.7)` check: 


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