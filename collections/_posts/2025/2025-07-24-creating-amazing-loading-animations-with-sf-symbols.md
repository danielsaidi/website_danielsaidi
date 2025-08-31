---
title:  Creating amazing loading animations with SF Symbols.
date:   2025-07-24 07:00:00 +0000
tags:   swiftui

assets: /assets/blog/25/0724/
image:  /assets/blog/25/0724/image.jpg
image-show: 0

bsky: https://bsky.app/profile/danielsaidi.bsky.social/post/3lupm3x6c522z
toot: https://mastodon.social/@danielsaidi/114908364082640185
---

{% include kankoda/data/open-source name="SwiftUIKit" %}
In this post, we'll take a look at how to use SF Symbols to easily create amazing loading animations.

<!--![Blog post header]({{image}})-->


## SF Symbols

SF Symbols has evolved a lot since its early days, and is now capable to apply powerful styling and value effects to many of its built-in symbols.

This makes SF Symbols a great alternative to many things that used to require custom code. Let's see how we can use it to replace a bunch of code for creating a loading animation.


## Old Loading Dot Animation

My [{{project.name}}]({{project.url}}) open-source project have the following code for generating animating loading dots:

```swift
public struct DotLoadingAnimation: View {

    public init(
        dotCount: Int = 3,
        interval: Double = 0.25
    ) {
        self.dotCount = dotCount
        self.timer = Timer.publish(every: interval, on: .main, in: .common)
            .autoconnect()
    }

    private let dotCount: Int
    private let timer: Publishers.Autoconnect<Timer.TimerPublisher>

    @State
    private var currentDotCount = 0

    public var body: some View {
        Text(dotText)
            .onReceive(timer) { _ in increaseDotCount() }
    }
}

private extension DotLoadingAnimation {

    var dotText: String {
        if currentDotCount == 0 { return "" }
        return (0..<currentDotCount)
            .map { _ in "." }
            .joined(separator: "")
    }
}

private extension DotLoadingAnimation {

    func increaseDotCount() {
        var newCount = currentDotCount + 1
        if newCount > dotCount {
            newCount = 0
        }
        currentDotCount = newCount
    }
}
```

It also had a `DotLoadingAnimationText` that could append the loading animation to any `Text` view:

```swift
public struct DotLoadingAnimationText: View {

    public init(
        text: LocalizedStringKey,
        bundle: Bundle? = nil,
        dotCount: Int = 3,
        interval: Double = 0.8
    ) {
        self.text = text
        self.bundle = bundle
        self.dotCount = dotCount
        self.interval = interval
    }

    private let text: LocalizedStringKey
    private let bundle: Bundle?
    private let dotCount: Int
    private let interval: Double

    public var body: some View {
        HStack(spacing: 0) {
            Text(text, bundle: bundle)
            Text(staticDotString)
        }
        .opacity(0)
        .overlay(titleAnimation, alignment: .leading)
    }
}

private extension DotLoadingAnimationText {

    var dotAnimation: some View {
        DotLoadingAnimation(
            dotCount: dotCount,
            interval: interval
        )
    }

    var staticDotString: String {
        (0..<dotCount)
            .map { _ in "." }
            .joined(separator: "")
    }

    var titleAnimation: some View {
        HStack(spacing: 0) {
            Text(text)
            dotAnimation
        }
    }
}
```

That's quite a lot of code for something that just renders a set of dots. Since this is plain text, it can be styled with standard SwiftUI modifiers:

```swift
DotLoadingAnimation(dotCount: 10)
    .foregroundStyle(.blue)
    .font(.largeTitle.bold())
```

This generates a customizable dot animation, where the dots can render together with other text:

![Dot animation]({{page.assets}}/dots-custom.gif)

This is easy to use, but having all that code for such a basic view is no longer needed. Let's see how we can use SF Symbols to create a similar effect with less code and more flexibility.


## SF Symbols-based loading animation

If we target iOS 17+, we can use SF Symbols to create a nice loading animation with very little code:

```swift
Image(systemName: "ellipsis")
    .foregroundStyle(.blue)
    .font(.largeTitle.bold())
    .symbolEffect(.variableColor)
```

This generates a smooth, ever-looping dot animation, that can be styled with fonts, text colors, etc:

![Dot animation]({{page.assets}}/dots-symbol.gif)

You can add modifiers to `.variableColor` to adjust the animation. For instance, `.hideInactiveLayers` makes inactive dots disappear, `.iterative` only highlights the current dot, etc.

These modifiers can be chained together to create complex, combinated effects. For instance, this:

```swift
Image(systemName: "ellipsis")
    .foregroundStyle(.blue)
    .font(.largeTitle.bold())
    .symbolEffect(
        .variableColor
        .iterative
        .hideInactiveLayers
    )
```

will apply a variable color effect that iteratively hides inactive layers. This results in a custom effect:

![Dot animation]({{page.assets}}/dots-symbol-styled.gif)

These composition capabilities make it easy to create amazing custom effects with almost no code. 

And the best part is that it works for all symbols that support variable colors, like the `wifi` symbol:

![Dot animation]({{page.assets}}/wifi.gif)

If you target iOS 18+ and aligned versions, you get access to even more symbol effects, like `.bounce`. Each effect has its own set of modifiers, to let you customize them to great effect.




## Conclusion

SF Symbols make it easy to create powerful, flexible animations and effects. If you target recent OS versions, I would even call it the preferred choice over writing a bunch of custom code.

Since SF Symbols is SO nice, I have decided to deprecate the old dot loading animation in [{{project.name}}]({{project.url}}).