---
title:  Using complex gestures in a SwiftUI ScrollView
date:   2022-11-16 08:00:00 +0000
tags:   swiftui open-source gestures

icon:   swiftui

tweet: https://twitter.com/danielsaidi/status/1592988560642428928

konstantin: https://twitter.com/kzyryanov
source: /blob/master/Sources/SwiftUIKit/Gestures/ScrollViewGestureButton.swift
---

SwiftUI gestures are complicated, since they can block the `ScrollView` gestures. Let's look at a way to implement rich view gestures in a way that doesn't block the scrolling.

{% include kankoda/data/open-source.html name="GestureButton" %}


## Update 2024-09-02

The `ScrollViewGestureButton` below stopped working in iOS 18, where the button gestures started blocking the scroll view gestures.

iOS 18 however doesn't need these complex gestures anymore. We only need to change the `GestureButton` to use a simultaneous gesture, and it works right away.

The new [{{project.name}}]({{project.url}}) open-source project contains these improvements, so check it out for code samples and source-code that is adjusted for the future.


## The problem

I ran into this problem in a project where I need to apply gestures to views in a `ScrollView`. These gestures conflict with the scroll gestures, which causes scrolling to stop working.

To explain, let's say you have a horizontal `ScrollView`, to which you add a bunch of views:

```swift
struct MyView: View {

    var body: some View {
        VStack {
            Text("\(tapCount) taps")
            ScrollView(.horizontal) {
                LazyHStack {
                    ForEach(0...100, id: \.self) { _ in
                        Color.red
                            .frame(width: 100, height: 100)
                    }
                }
            }
        }
    }
}
```

This creates a horizontal list of squares that can we can swipe to scroll through the items.

Let's update the code above to add an `onTapGesture` modifier to each list item:

```swift
Color.red
    .onTapGesture { print("Tap") }
```

Run the code again, and you'll see that things will still work. You can tap items to trigger an action, while still scrolling through the items as before.

Let's now update the code to use an `onLongPressGesture` instead of the `onTapGesture`:

```swift
Color.red
    .onLongPressGesture { print("Long press") }
```

If you run this code, you can long press the items to trigger the long press action. However, if you try to scroll in the list, you'll notice that scrolling no longer works.

It doesn't matter if you use a `gesture` with a `LongPressGesture`, scrolling is still broken:

```swift
Color.red
    .gesture(
        LongPressGesture()
            .onChanged { _ in print("Long press changed") }
            .onEnded { _ in print("Long press ended") }
    )
```

With this code, the `onChange` and `onEnded` functions will trigger as expected, but the list will not scroll. The same will happen if you use a `DragGesture` instead of a `LongPressGesture`.


## Why does this happen?

Scrolling stops working because the long press and drag gesture modifiers block the scroll view in a way that doesn't happen when you apply a tap gesture modifier.

I guess it has something to do with that taps just have to detect a press and release, while others need to track gestures over time, in a way that may conflict with the scroll gestures.


## Some non-working solutions

If you search for this problem online, you will find suggestions that you can fix this by adding an empty `onTapGesture` before the long press and drag gestures:

```swift
listItem
    .onTapGesture { print("Tap") }
    .gesture(
        LongPressGesture()
            .onEnded { _ in print("Long press") }
    )
```

This will actually work. The long press gesture will trigger and you will still be able to scroll through the list. However, if you replace `gesture` with a `simultaneousGesture`, scrolling will stop working again:

```swift
listItem
    .onTapGesture { print("Tap") }
    .simultaneousGesture(
        LongPressGesture()
            .onEnded { _ in print("Long press") }
    )
```

The reason why `gesture` works and `simultaneousGesture` doesn't, is because the `gesture` schedules itself after any prior gestures, while `simultaneousGesture` schedules itself to run simultaneously with them. Hence the name, heh.

In other words. Since the `gesture` triggers *after* `onTapGesture`, it doesn't interfere with the scroll view. And since the `simultaneousGesture` triggers immediately, it does.

This means that the `onTapGesture` approach requires a delay. If we want to use immediate gestures, such as detecting drags and presses, this approach is not viable.

You may find other delay-based solutions to this problem, some pretty complicated. If they are based on delays, they won't work if we want the gestures to be immediately detected.


## Finding a workaround

While `UIKit` has very granular gesture detection, `SwiftUI` is more limited. We can still do much of the same things, but with much fewer tools. 

For instance, you can use a `DragGesture` with `0` distance to detect a `press` gesture, then listen for the drag gesture to end to detect a `release`.

However, since long presses and drag gestures don't work in `ScrollView`, we must find a way to detect some of these gestures in a way that doesn't mess with the scrolling.

After pondering this for a while, me and [Konstantin Zyryanov]({{page.konstantin}}) realized that we have another way to detect that a view is being pressed - using a `ButtonStyle`.

For those of you who are unfamiliar with SwiftUI `ButtonStyle`, it lets you change the style of a button depending on its `role`, `isPressed` state, etc. 

For instance, this style changes the opacity of its `label` based on the `isPressed` state:

```swift
struct MyButtonStyle: ButtonStyle {

    func makeBody(configuration: Configuration) -> some View {
        configuration.label
            .opacity(configuration.isPressed ? 0.5 : 1)
    }
}
```

Since button styles don't interfere with scrolling (that would have made the entire approach unusable, since it's a universal tool), perhaps this is the hack we've been looking for? 

Perhaps we can use a `ButtonStyle` to work around the scroll view limitations and use it to detect presses and releases, without having to use a drag gesture?

Let's find out!


## Building a scroll view gesture button

When creating a style-based approach, I want it to be able to detect the following gestures:

* Presses
* Releases (inside and outside)
* Long presses
* Hold presses
* Double taps
* Gesture ended

Most of these gestures can be handled by the button style, while some must be handled by the button itself. Let's begin with the style.


### Defining a button style

Let's create a `ScrollViewGestureButtonStyle` and define the gestures it will let us handle:

```swift
struct ScrollViewGestureButtonStyle: ButtonStyle {

    init(
        pressAction: @escaping () -> Void,
        longPressTime: TimeInterval,
        longPressAction: @escaping () -> Void,
        doubleTapTimeout: TimeInterval,
        doubleTapAction: @escaping () -> Void,
        endAction: @escaping () -> Void
    ) {
        self.pressAction = pressAction
        self.longPressTime = longPressTime
        self.longPressAction = longPressAction
        self.doubleTapTimeout = doubleTapTimeout
        self.doubleTapAction = doubleTapAction
        self.endAction = endAction
    }

    private var doubleTapTimeout: TimeInterval
    private var longPressTime: TimeInterval

    private var pressAction: () -> Void
    private var longPressAction: () -> Void
    private var doubleTapAction: () -> Void
    private var endAction: () -> Void

    func makeBody(configuration: Configuration) -> some View {
        // Insert magic here
    }
}
```

Besides the actions, we also add configurations to let us define the max time between two taps to count as a double tap, as well as how long time that is required for a long press.

With this in place, we can start handling the pressed state within `makeBody`, which we can detect by using the `configuration.isPressed` value:

```swift
func makeBody(configuration: Configuration) -> some View {
    configuration.label
        .onChange(of: configuration.isPressed) { isPressed in
            if isPressed {
                pressAction()
            } else {
                endAction()
            }
        }
}
```

Here, we subscribe to the pressed state and trigger a function every time it changes. We then trigger the `pressAction` if the configuration is pressed and the `endAction` if it's not.

If you wonder why `endAction` is called and not `releaseAction`, let me spoil a future finding. If we apply this style to a button in a scroll view, then start scrolling when it's is pressed, the `endAction` will be triggered as the gesture is cancelled, even if we still press the button.

In other words, this is *not* a release action. We must handle the release in another way.


### How to handle double taps

To handle double taps, we just have to detect how fast two press events are triggered. To implement this for our button style, first add this state to the style:

```swift
@State
var doubleTapDate = Date()
```

then add the following function:

```swift
private extension ScrollViewGestureButtonStyle {

    func tryTriggerDoubleTap() -> Bool {
        let interval = Date().timeIntervalSince(doubleTapDate)
        guard interval < doubleTapTimeout else { return false }
        doubleTapAction()
        return true
    }
}
```

then finally add the following to the `isPressed` handling:

```swift
if isPressed {
    pressAction()
    doubleTapDate = tryTriggerDoubleTap() ? .distantPast : .now
} else {
    endAction()
}
```

Here, we check if there's an earlier press that should cause the new press to be handled as a double tap. If two presses happen within `doubleTapTimeout`, we trigger a double tap, otherwise we set `doubleTapDate` to the distant past to avoid future incorrect double taps.

To be clear, this is not a double tap, but a double press. However, it serves our purpose.


### How to handle long presses

To handle long presses, we just have to detect how long a press event is being active. To implement this for our button style, first add this state to the style:

```swift
@State
var longPressDate = Date()
```

then add the following function:

```swift
private extension ScrollViewGestureButtonStyle {

    func tryTriggerLongPressAfterDelay(triggered date: Date) {
        DispatchQueue.main.asyncAfter(deadline: .now() + longPressTime) {
            guard date == longPressDate else { return }
            longPressAction()
        }
    }
}
```

then finally add the following to the `isPressed` handling:

```swift
longPressDate = Date()
if isPressed {
    pressAction()
    doubleTapDate = tryTriggerDoubleTap() ? .distantPast : .now
    tryTriggerLongPressAfterDelay(triggered: longPressDate)
} else {
    endAction()
}
```

We first set `longPressDate`, then trigger an async operation after the `longPressTime`. If the date is still the same when it triggers, we trigger the `longPressAction`.


### Wrapping up our style

Our button style is now complete. All in all, it looks like this:

```swift
struct ScrollViewGestureButtonStyle: ButtonStyle {

    init(
        pressAction: @escaping () -> Void,
        doubleTapTimeoutout: TimeInterval,
        doubleTapAction: @escaping () -> Void,
        longPressTime: TimeInterval,
        longPressAction: @escaping () -> Void,
        endAction: @escaping () -> Void
    ) {
        self.pressAction = pressAction
        self.doubleTapTimeoutout = doubleTapTimeoutout
        self.doubleTapAction = doubleTapAction
        self.longPressTime = longPressTime
        self.longPressAction = longPressAction
        self.endAction = endAction
    }

    private var doubleTapTimeoutout: TimeInterval
    private var longPressTime: TimeInterval

    private var pressAction: () -> Void
    private var longPressAction: () -> Void
    private var doubleTapAction: () -> Void
    private var endAction: () -> Void

    @State
    var doubleTapDate = Date()

    @State
    var longPressDate = Date()

    func makeBody(configuration: Configuration) -> some View {
        configuration.label
            .onChange(of: configuration.isPressed) { isPressed in
                longPressDate = Date()
                if isPressed {
                    pressAction()
                    doubleTapDate = tryTriggerDoubleTap() ? .distantPast : .now
                    tryTriggerLongPressAfterDelay(triggered: longPressDate)
                } else {
                    endAction()
                }
            }
    }
}

private extension ScrollViewGestureButtonStyle {

    func tryTriggerDoubleTap() -> Bool {
        let interval = Date().timeIntervalSince(doubleTapDate)
        guard interval < doubleTapTimeoutout else { return false }
        doubleTapAction()
        return true
    }

    func tryTriggerLongPressAfterDelay(triggered date: Date) {
        DispatchQueue.main.asyncAfter(deadline: .now() + longPressTime) {
            guard date == longPressDate else { return }
            longPressAction()
        }
    }
}
```

We still miss some functionality, such as detecting when a button is released. This can't be done within the style, since the style gesture may be cancelled, so let's define a button that will apply the style and fill out these missing parts.


### How to handle gesture releases

To implement the `release` gesture, lets create a button that uses a `releaseAction` as its tap action and applies the button style that we just defined:

```swift
struct ScrollViewGestureButton<Label: View>: View {

    init(
        doubleTapTimeoutout: TimeInterval = 0.5,
        longPressTime: TimeInterval = 1,
        pressAction: @escaping () -> Void = {},
        releaseAction: @escaping () -> Void = {},
        endAction: @escaping () -> Void = {},
        longPressAction: @escaping () -> Void = {},
        doubleTapAction: @escaping () -> Void = {},
        label: @escaping () -> Label
    ) {
        self.style = ScrollViewGestureButtonStyle(
            doubleTapTimeoutout: doubleTapTimeoutout,
            longPressTime: longPressTime,
            pressAction: pressAction,
            endAction: endAction,
            longPressAction: longPressAction,
            doubleTapAction: doubleTapAction)
        self.releaseAction = releaseAction
        self.label = label
    }

    var label: () -> Label
    var style: GestureButtonStyle
    var releaseAction: () -> Void

    var body: some View {
        Button(action: releaseAction, label: label)
            .buttonStyle(style)
    }
}
```

That's it! The button just wraps the provided label, triggers the provided `releaseAction` and then lets the newly created style take care of the remaining gestures.

With this approach, you can press, relase, double tap, long press, etc. without messing up the scrolling. All made possible by the fact that the button style can detect presses without blocking the scroll view.


## Going further

While the above works well, it's can't detect drag gestures yet. I have therefore improved it to handle drag gestures as well, which was a bit more complicated than I first expected. 

First, we can't apply the gesture to a `Button`, but must apply it to the button content view:

```swift
Button(action: releaseAction) {
    buttonContentView
        .gesture(DragGesture(...))  // Must go here
}
.buttonStyle(style)
.gesture(DragGesture(...))  // <-- This will not work!!!
```

However, adding a `DragGesture` to the view means that it will conflict with the button style. For instance, quickly tapping the button will only trigger the button action and not the style. 

This means that we must handle double taps for both the button and the style. Also, since this new drag gesture will once again block scrolling, we must add a tap gesture before it to force a delay. 
This adds even more complexities since a tap gesture, a drag gesture and a button style must now work together.

Adding drag gestures turned out to open a can of worms. Since the different parts of the code must handle the same functionality in different cases, I had to make the code more complex to avoid duplication. It works great, but became more complex than I expected.


## Conclusion

`ScrollViewGestureButton` lets you apply multiple gesture actions to a button with a single `DragGesture`, that works in a `ScrollView`.

I have added a `ScrollViewGestureButton` to my [SwiftUIKit]({{project.url}}) library. You can find the source code [here]({{project.url}}{{page.source}}). If you decide to give it a try, I'd be very interested in hearing what you think.