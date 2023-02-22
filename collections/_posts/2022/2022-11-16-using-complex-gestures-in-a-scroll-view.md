---
title:  Using complex gestures in a SwiftUI ScrollView
date:   2022-11-16 08:00:00 +0000
tags:   swiftui open-source gestures

icon:   swiftui

tweet: https://twitter.com/danielsaidi/status/1592988560642428928

konstantin: https://twitter.com/kzyryanov

keyboardkit: https://keyboardkit.com
github: https://github.com/danielsaidi/SwiftUIKit
source: https://github.com/danielsaidi/SwiftUIKit/blob/master/Sources/SwiftUIKit/Gestures/ScrollViewGestureButton.swift
---

Using complex gestures in a SwiftUI `ScrollView` is complicated, since they block scroll view gestures in a way that causes scrolling to stop working. I've looked into this, and found a way to use a button style to handle gestures in a way that doesn't block the scrolling.


## Post updates

**2022-11-20** I have added a "Going further" section that describes how to add support for drag gestures, and also added a link to a component that supports detecting and handling presses, releases (inside & outside), long presses, double taps, repeats (press & hold), drag gestures, as well as when gestures end.


## The problem

The reason for me to tackle this problem, was that I ran into problems in a project where I need complex gestures on views that are nested in a `ScrollView`. The gestures conflict with the scroll view gestures, which cause scrolling to stop working and the gestures not being detected.

To explain, let's say you have a `ScrollView` with a `LazyHStack`, to which you add a bunch of views:

```swift
struct MyView: View {

    var body: some View {
        VStack {
            Text("\(tapCount) taps")
            ScrollView(.horizontal) {
                LazyHStack {
                    ForEach(0...100, id: \.self) { _ in
                        listItem
                    }
                }
            }
        }
    }

    var listItem: some View {
        Color.red
            .frame(width: 100, height: 100)
    }
}
```

If run this code, you'll get a horizontal list with red squares, which can swipe to scroll through the items.

Let's update the code above to add an `onTapGesture` modifier to each list item:

```swift
listItem
    .onTapGesture { print("Tap") }
```

Run the code again, and you'll see that things will still work. You can tap the items to trigger the action, while still scrolling through the items as before.

Let's now update the code to use an `onLongPressGesture` modifier instead of the `onTapGesture`:

```swift
listItem
    .onLongPressGesture { print("Long press") }
```

If you run this code, you can now long press the items to trigger the long press action. However, if you try to scroll in the list, you'll notice that scrolling no longer works.

It doesn't matter if you use a `gesture` with a `LongPressGesture` instead, scrolling is still broken:

```swift
listItem
    .gesture(
        LongPressGesture()
            .onChanged { _ in print("Long press changed") }
            .onEnded { _ in print("Long press ended") }
    )
```

With this code, the `onChange` and `onEnded` functions will trigger as expected, but the list will not scroll. The same will happen if you use a `DragGesture` instead of a `LongPressGesture`.


## Why does this happen?

Scrolling stops working because the long press and drag gesture modifiers steals the gestures from the scroll view, in a way that doesn't happen when you apply a tap gesture modifier.

I don't know why tap gestures work while long presses and drags don't, but I guess it has something to do with that taps just have to detect a press and release, while the others need to detect gestures over time, in a way that maybe conflicts with the scroll gestures.


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

The reason why `gesture` works and `simultaneousGesture` doesn't, is because `gesture` schedules itself after any prior gestures, while `simultaneousGesture` schedules itself together with them.

In other words, `gesture` triggers *after* `onTapGesture`, which means that it doesn't interfere with the scrolling. This is why scrolling still works. However, since `simultaneousGesture` triggers immediately, it's interfering with the scrolling. This is why scrolling stops working.

This means that the `onTapGesture` approach requires a delay. If we want to use immediate gestures, such as detecting drags and presses, this approach is not viable.

You may find other delay-based solutions, some of which are pretty complicated. Since these are also based on delays, they won't work if we want the gestures to be immediately detected.


## Finding a workaround

While `UIKit` has very granular gesture detection, `SwiftUI` is more limited. We can still do much of the same things, but with fewer tools. For instance, you can use a `DragGesture` with a `0` distance to detect `press` gestures. To detect `releases`, we can listen for the drag gesture to end.

However, since long presses and drag gestures don't work in a `ScrollView`, the challenge is to find a way to detect some of these gestures in a way that doesn't mess with the scrolling.

After pondering this for a while and trying many non-working solutions, me and [Konstantin Zyryanov]({{page.konstantin}}) did realize that we have a way to detect that a view is being pressed - using a `ButtonStyle`.

For those of you who are unfamiliar with SwiftUI button styles, they let you change the style of a button depending on its `role` and `isPressed` state. For instance, this style changes the opacity of its `label`:

```swift
struct MyButtonStyle: ButtonStyle {

    func makeBody(configuration: Configuration) -> some View {
        configuration.label
            .opacity(configuration.isPressed ? 0.5 : 1)
    }
}
```

As most of you who have worked with SwiftUI probably know, button styles don't interfere with scrolling. That would have made the entire style approach unusable (much like not being able to use gestures in a scroll view). Perhaps this is the hack we've been looking for? 

Perhaps we can use a button style to work around the scroll view limitations and use it to detect presses and releases, without having to use a drag gesture? Let's find out!


## Building a scroll view gesture button

When creating this button style-based approach, I want it to be able to detect the following gestures:

* Presses
* Releases (inside and outside)
* Long presses
* Hold presses
* Double taps
* Gesture ended

Most will be handled by the style, while some must be handled by the button. Let's start with the style.


### Defining a button style

Let's create a `ScrollViewGestureButtonStyle` and define the functionality that it will help us handle:

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

Besides the gesture actions, we also add configurations to let us define the max time between two taps to count as a double tap, as well as how long time that is required for a long press.

With this foundation in place, we can start handling the pressed state within `makeBody`, which we detect by using the `configuration.isPressed` value:

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

In the code above, we subscribe to the pressed state and trigger a function every time the state changes. We then trigger the `pressAction` if the configuration is pressed and the `endAction` if it's not.

If you wonder why `endAction` isn't called `releaseAction`, let me spoil a future finding. If we apply this style to a button in a scroll view, then start scrolling when the button is pressed, the `endAction` will be triggered as the gesture is cancelled, even if we still press the button. In other words, this is *not* a release action. We must handle releases in another way.


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

When the view is pressed, we check if there's an earlier registered press that should cause the new press to be handled as a double tap. If two presses happen within the `doubleTapTimeout` time, we trigger a double tap, otherwise we set the `doubleTapDate` to the distant past to avoid a subsequent double tap.

To be clear, this is technically not a double tap gesture, but rather a double press. However, rewriting it to behave as a double tap is a bit more complicated, so let's go with this for now.


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

We first set the `longPressDate` to the current date, then trigger an async operation to be performed after a `longPressTime`. If the date is still the same when it triggers, we trigger the `longPressAction`.


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

We are however still missing some functionality, such as detecting when a button is released. This can't be done within the style, since the style gesture may be cancelled, so let's define a button that will apply the style and fill out these missing parts.


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

That's it! The button just has to wrap the provided label, trigger the provided `releaseAction` and apply the newly created style to take care of the remaining gestures.


## Conclusion

If you try this out, you'll see that it actually works. You can press, relase, double tap, long press etc. and scrolling still works. All made possible by the fact that button styles can detect presses without blocking the scroll view gestures.


## Going further

While the above works well and is probably enough for most needs, it's actually not enough if you need to detect drag gestures. For instance, my [KeyboardKit]({{page.keyboardkit}}) library needs buttons to be able to handle a wide variety of gestures and transition to dragging when a button presents a callout with secondary actions.

I therefore decided to improve the solution above to also handle drag gestures. This turned out to be way more compolicated than I could first expect. For instance, we can't apply the drag gesture directly to the `Button`, but must instead apply it to the button content view:

```swift
Button(action: releaseAction) {
    buttonContentView
        .gesture(DragGesture(...))  // Must go here
}
.buttonStyle(style)
.gesture(DragGesture(...))  // This will not work!
```

However, adding a `DragGesture` to the view means that it will start to conflict with the button style. For instance, quickly tapping the button will only trigger the button action and not the style. This means that we must handle double taps in both the button and the style. Also, turns out that the drag gesture will once again block scrolling, as we discussed earlier. We must therefore add a tap gesture before it to force a delay onto the drag gesture as we discussed earlier, but this introduces even more complexities since we now have a tap gesture, a drag gesture and a button style that must all play together.

Adding drag gestures turned out to open a can of worms.

Since the different parts of the code must handle the same functionality in different cases, I also had to make the code more complex to avoid code duplication. The simple solution above became a lot more complicated when adding drag gestures to the mix.


## Conclusion

`ScrollViewGestureButton` lets you handle multiple gestures with a single button. You can detect presses, relases outside and inside, long presses, double taps, trigger repeating actions etc. all with a single `DragGesture`.

I have added `ScrollViewGestureButton` to my [SwiftUIKit]({{page.github}}) library. You can find the source code [here]({{page.source}}). If you decide to give it a try, I'd be very interested in hearing what you think.

Happy button mashing!