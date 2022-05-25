---
title:  Creating a custom environment key for safe area insets
date:   2022-05-23 10:00:00 +0000
tags:   swiftui swift

icon:   swiftui
tweet:  https://twitter.com/danielsaidi/status/1528773060123303937?s=20&t=PiJnnQfR8Ta3V-NP2TU-sQ

hackingwithswift: https://www.hackingwithswift.com/quick-start/swiftui/how-to-inset-the-safe-area-with-custom-content
swiftuikit: https://github.com/danielsaidi/SwiftUIKit
---

In this post, we'll take a look at how to create a custom SwiftUI environment key that lets us get the safe area insets for the current scene.

SwiftUI has many great environment values, with more being added with every new release. However, there are still gaps, where important information isn't available in the convenient way that we have come to expect from SwiftUI.

One example is safe area insets, where SwiftUI offers different ways to get the insets of a certain view. One way is to use the `.safeAreaInset` view modifier, which was added in iOS 15 and is described in great detail [here]({{page.hackingwithswift}}). You can also use a `GeometryReader`, which provides these insets through its proxy, but at the same time affects the view hierarchy in ways that you may not want.

However, sometimes you may need the insets for the screen, rather than the view itself. For instance, if you ignore the safe area insets in the root view, then want to apply a bottom margin depending on if the device has a notch or not, you may face more problems than you'd expect.

I'd love for SwiftUI to provide various environments to get the safe area insets of the e.g. view itself, the scene, the screen etc. but until Apple adds this to SwiftUI (and when they do, it will be for the latest os versions only), we can implement this ourselves.

Let's start with defining a few extensions that we'll need. For UIKit, we need the key window, which we can get like this:

```swift
#if os(iOS) || os(tvOS)
private extension UIApplication {

    var keyWindow: UIWindow? {
        connectedScenes
            .filter { $0.activationState == .foregroundActive }
            .compactMap { $0 as? UIWindowScene }
            .flatMap { $0.windows }
            .filter { $0.isKeyWindow }
            .first
    }
}
#endif
```

Note that `UIWindow` is only available in iOS and tvOS, which is why we wrap the extension in an `#if`.

We must also be able to convert the windows UIKit-specific insets to SwiftUI, which we can do like this:

```swift
#if canImport(UIKit)
private extension UIEdgeInsets {
    
    var edgeInsets: EdgeInsets {
        EdgeInsets(top: top, leading: left, bottom: bottom, trailing: right)
    }
}
#endif
```

We can now define a custom environment key. Let's call it `SafeAreaInsetsKey`:

```swift
private struct SafeAreaInsetsKey: EnvironmentKey {
    
    static var defaultValue: EdgeInsets {
        #if os(iOS) || os(tvOS)
        let keyWindow = UIApplication.shared.keyWindow
        return keyWindow?.safeAreaInsets.edgeInsets ?? EdgeInsets()
        #else
        EdgeInsets()
        #endif
    }
}
```

For iOS and tvOS, we return the safe area insets of the key window, if any, while other platforms like watchOS and macOS just get a zero-valued result.

To use this new key, we can extend `EnvironmentValues` with a new `safeAreaInsets` value:

```swift
public extension EnvironmentValues {
    
    var safeAreaInsets: EdgeInsets {
        self[SafeAreaInsetsKey.self]
    }
}
```

You can now get the safe area insets of the current scene like this:

```swift
struct MyView: View {

    @Environment(\.safeAreaInsets)
    private var insets: EdgeInsets

    var body: some View {
        Text("\(insets.bottom)")
    }
}
```

This would render different texts if you run the code on an iPhone 13 or an iPhone SE, regardless if you ignore safe area insets anywhere in your view hierarchy.


## Conclusion

Although the name of the new environment key that we added in this post may be confusing (a better name would perhaps be `sceneInsets` or something like that), I hope you liked this way of adding custom environment values. I think it's really convenient and will try to use it more often.

You can find this code in my [SwiftUIKit]({{page.swiftuikit}}) library. Feel free to try it out and let me know what you think.