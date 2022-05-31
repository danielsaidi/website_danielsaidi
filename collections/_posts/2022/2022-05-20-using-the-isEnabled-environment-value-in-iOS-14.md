---
title:  Using the isEnabled environment value in iOS 14
date:   2022-05-20 10:00:00 +0000
tags:   swiftui

icon:   swiftui
assets: /assets/blog/2022/2022-05-20/

tweet:  https://twitter.com/danielsaidi/status/1527690267360378881?s=20&t=PiJnnQfR8Ta3V-NP2TU-sQ
---

SwiftUI is amazing, but has a history of seriously buggy behavior. Even if you follow the documentation and your code compiles, you must still verify that it actually works if you target older iOS versions. As an example, let's take a look at using the `isEnabled` environment value with custom button styles.

SwiftUI makes it easy to implement a custom `ButtonStyle` that changes appearance based on if the button or its view hierarchy is enabled or not. Just add an `@Environment` property that is bound to the `\.isEnabled` key path, to get whether or not a view or its view hierarchy is enabled.

A basic button style that uses this environment value could look something like this:

```swift
struct MyButtonStyle: ButtonStyle {

    @Environment(\.isEnabled) private var isEnabled

    func makeBody(configuration: Configuration) -> some View {
        configuration.label
            .padding()
            .background(backgroundColor)
            .clipShape(Capsule())
    }

    var backgroundColor: Color {
        isEnabled ? .green : .red
    }
}
```

Well, at least you would think that it's as simple, since it compiles. In iOS 15, this works great:

![iOS 15 renders enabled and disabled buttons correctly]({{page.assets}}ios15.png)

However, things doesn't look as nice in iOS 14.4, where the `isEnabled` state is always `true`:

![iOS 14.4 renders enabled and disabled buttons incorrectly]({{page.assets}}ios14-4.png)

Turns out that this environment value returns an incorrect value to button styles, which is yet another of these nasty SwiftUI inconsistencies that are easy to miss and that cause bugs in your apps.

To fix this bug, your button style must create a nested view, which can then use the correct `isEnabled` environment value to customize the button content.

The button style above could be adjusted to look something like this:

```swift
struct MyButtonStyle: ButtonStyle {

    private struct ContentView<Content: View>: View {

        var view: Content

        @Environment(\.isEnabled) private var isEnabled

        var body: some View {
            view
                .padding()
                .background(backgroundColor)
                .clipShape(Capsule())
        }

        var backgroundColor: Color {
            isEnabled ? .green : .red
        }
    }

    func makeBody(configuration: Configuration) -> some View {
        ContentView(view: configuration.label)
    }
}
```

This small adjustment makes the button style render a correct result, even in iOS 14.4:

![With a nested content view, iOS 14.4 renders enabled and disabled buttons correctly]({{page.assets}}ios14-4-2.png)

If you have many styles, this can however become quite tedious and repetitive. You can make it more managable by creating a content view that wraps any view and provides it with the correct `isEnabled`:

```swift
public struct ButtonStyleContent<Content: View>: View {

    public init(@ViewBuilder viewBuilder: @escaping ContentBuilder) {
        self.viewBuilder = viewBuilder
    }

    public typealias ContentBuilder = (_ isEnabled: Bool) -> Content

    private let viewBuilder: ContentBuilder

    @Environment(\.isEnabled)
    public var isEnabled: Bool

    public var body: some View {
        viewBuilder(isEnabled)
    }
}
```

You can then reduce the amount of code in the button style and use the same approach in all styles:


```swift
struct MyButtonStyle: ButtonStyle {

    func makeBody(configuration: Configuration) -> some View {
        ButtonStyleContent { isEnabled in
            configuration.label
                .padding()
                .background(backgroundColor(isEnabled: isEnabled))
                .clipShape(Capsule())
        }
    }

    func backgroundColor(isEnabled: Bool) -> Color {
        isEnabled ? .green : .red
    }
}
```

The content view provides the `isEnabled` state to the content view builder function. You can then use it as is or pass it on to any functions that are used to determine the button apperance.


## Conclusion

Although the workaround to this problem is easy to implement, it's unfortunate that Apple often misses things like this in SwiftUI. Every flaw like this help undermine the trust we developers must have in the technology, to feel confident enough to switch over from UIKit and AppKit.

Furthermore, it's also frustrating that we as developers have to discover these problems by pure chance, and that Apple don't communicate these flaws. This has surely not helped the adoption of SwiftUI, where developers constantly argue whether or not the technology is production ready.

But that's a discussion for another post :)