---
title:  Creating a custom environment key for safe area insets
date:   2022-05-23 10:00:00 +0000
tags:   swiftui environment-values

icon:   swiftui
tweet:  https://twitter.com/danielsaidi/status/1528773060123303937?s=20&t=PiJnnQfR8Ta3V-NP2TU-sQ

hackingwithswift: https://www.hackingwithswift.com/quick-start/swiftui/how-to-inset-the-safe-area-with-custom-content
---

In this post, we'll take a look at how to create a custom SwiftUI environment key that lets us get the safe area insets for the current scene.

{% include kankoda/data/open-source.html name="SwiftUIKit" %}

SwiftUI has many great environment values, with more being added over time. However, there are still gaps, where important information isn't available in the convenient way that we have come to expect.

One example is safe area insets, where SwiftUI offers different ways to get the insets of a certain view. 

One way is to use the `.safeAreaInset` view modifier, which was added in iOS 15 and is described in great detail [here]({{page.hackingwithswift}}). You can also use a `GeometryReader`, which provides these insets through its proxy.

However, sometimes you may need the insets for the screen, rather than the view itself. For instance, if you ignore the safe area insets in the root view, you may face problems in nested views.

I'd love for SwiftUI to provide various environments to get the safe area insets of the view, the scene, the screen etc. but until Apple adds this to SwiftUI, we can implement it ourselves.

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

Although the name of the new environment key may be confusing (a better name may be `sceneInsets` or something like that), I hope you liked this way of adding custom environment values.

You can find this code in the [SwiftUIKit]({{project.url}}) library. Feel free to try it out and let me know what you think.