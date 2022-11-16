---
title:  Using complex gestures in a ScrollView
date:   2022-11-16 08:00:00 +0000
tags:   swiftui

icon:   swiftui

tweet: https://twitter.com/danielsaidi/status/1592988560642428928

konstantin: https://twitter.com/kzyryanov
keyboardkit: https://getkeyboardkit.com
---

Using long press and drag gestures in SwiftUI `ScrollView`s is complicated, since they steal the touch events and cause scrolling to stop working. I've been trying to fix this, and have found a way that seems promising, that involves using a button style to handle the scroll blocking gestures.


## The problem

Let's say you have a horizontal `ScrollView` with a `LazyHStack`, to which you add a bunch of views:

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

If run this code, you'll get a horizontal list with red squares. As expected, you can swipe horizontally to scroll throught the items in the list.

Let's update the code above to add an `onTapGesture` modifier to each list item:

```swift
listItem
    .onTapGesture { print("Tap") }
```

Run the code again, and you'll see that things will still work. You can tap the items to trigger the action, and scroll through the items as before.

Let's now update the code to use an `onLongPressGesture` modifier instead:

```swift
listItem
    .onLongPressGesture { print("Long press") }
```

If you run this code, you can long press the items to trigger the long press action. However, if you try to scroll in the list, you'll notice that scrolling no longer works.

It doesn't matter if you use a `gesture` with a `LongPressGesture` instead, scrolling is still broken:

```swift
listItem
    .gesture(
        LongPressGesture()
            .onChanged { _ in print("Long press changed") }
            .onEnded { _ in print("Long press ended") }
    )
```

With this code, the `onChange` and `onEnded` functions will trigger as expected, but the list will still not scroll. The same will happen if you use a `DragGesture` instead of a `LongPressGesture`.


## Why does this happen?

Scrolling stops working because the long press and drag gesture modifiers steals the gestures from the scroll view, in a way that doesn't happen when you apply a tap gesture modifier.

I don't know why tap gestures work while long presses and drags don't, but I guess it has something to do with that taps just have to detect a press and release, while the others need to detect gestures over time, in a way that maybe conflicts with the scroll gestures.


## Some non-working solutions

If you search for this problem online, you will find suggestions that you can fix this by adding an empty `onTapGesture` before the long press and drag gestures, for instance:

```swift
listItem
    .onTapGesture { print("Tap") }
    .gesture(
        LongPressGesture()
            .onEnded { _ in print("Long press") }
    )
```

If you try this, it will actually work in a way that is good enough for long press gestures. However, if you replace the `gesture` with a `simultaneousGesture`, things stop working again:

```swift
listItem
    .onTapGesture { print("Tap") }
    .simultaneousGesture(
        LongPressGesture()
            .onEnded { _ in print("Long press") }
    )
```

The reason why `gesture` works and `simultaneousGesture` doesn't, is because `gesture` schedules itself after any prior gestures, while `simultaneousGesture` schedules itself together with them.

In other words, `gesture` triggers *after* `onTapGesture`, which means that it doesn't interfere with the scrolling, which is why scrolling still works. However, `simultaneousGesture` triggers immediately, and is therefore interfering with the scrolling, which is why scrolling stops working.

This means that the `onTapGesture` approach requires a delay. If we want to use immediate gestures, such as detecting drags and presses, this approach is therefore not viable.

You may also find other delay-based solutions, some of which are pretty complicated. These also won't work if we want the gestures to be immediately detected.


## Finding a workaround

While `UIKit` has very granular gesture detection, gesture detection in `SwiftUI` is more limited. You can still do much of the same things, but with fewer tools. For instance, we often use a `DragGesture` with a 0 distance to detect `press` gestures.

While this approach is ok (albeit a bit conter-intuitive) in most cases, it won't work in a scroll view, since it causes scrolling to stop working. The challenge is thus to find a way to detect some of these gestures in a way that doesn't mess with the scrolling.

After pondering this for a while and trying many non-working solutions, me and [Konstantin Zyryanov]({{page.konstantin}}) did realize that we have a way to detect that a view is being pressed - using a `ButtonStyle`.

For those of you who are unfamiliar with SwiftUI button styles, they let you change the style of a button depending on its `role` and whether or not it's pressed. For instance, this style changes the opacity of its `label` based on if the button is pressed or not:

```swift
struct MyButtonStyle: ButtonStyle {

    func makeBody(configuration: Configuration) -> some View {
        configuration.label
            .opacity(configuration.isPressed ? 0.5 : 1)
    }
}
```

As most of you who have worked with SwiftUI probably know, button styles don't interfere with scrolling. That would have made the entire style approach unusable (much like not being able to use gestures in a scroll view). Perhaps this is the hack we've been looking for? Perhaps we can use a button style to work around the scroll view limitations and detect presses and releases without having to use a drag gesture?


## Building a gesture button

When creating this button style-based approach, we want it to be able to detect the following gestures, to make it as versatile as possible:

* Presses
* Releases
* Cancelled gestures
* Long presses
* Double taps

Most will be handled by the style, while some must be handled by the button. Let's start with the style.


### Defining a button style

Let's call the style `GestureButtonStyle` and define all the functionality that it should help us handle:

```swift
struct GestureButtonStyle: ButtonStyle {

    init(
        doubleTapTimeout: TimeInterval,
        longPressDelay: TimeInterval,
        pressAction: @escaping () -> Void,
        endAction: @escaping () -> Void,
        longPressAction: @escaping () -> Void,
        doubleTapAction: @escaping () -> Void
    ) {
        self.doubleTapTimeout = doubleTapTimeout
        self.longPressDelay = longPressDelay
        self.pressAction = pressAction
        self.endAction = endAction
        self.longPressAction = longPressAction
        self.doubleTapAction = doubleTapAction
    }

    private var doubleTapTimeout: TimeInterval
    private var longPressDelay: TimeInterval

    private var pressAction: () -> Void
    private var endAction: () -> Void
    private var longPressAction: () -> Void
    private var doubleTapAction: () -> Void

    func makeBody(configuration: Configuration) -> some View {
        configuration.label
            // Magic code here
    }
}
```

Besides the gesture actions, we also add configurations to let us define the max time between taps for a double tap, and how long a long press must be.

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

In the code above, we subscribe to the pressed state and trigger a function every time the state changes. We then trigger the 
`pressAction` if the configuration is pressed and the `endAction` if it's not.

If you wonder why the actions aren't named `pressAction` and `releaseAction`, let me spoil a future finding. If we apply this to a button, then start scrolling while pressing the button, the `endAction` will be triggered as the gesture is cancelled, even if we still keep our finger pressed. On other words, it's *not* a release event, but rather an event for when the gesture ends. We need to handle release in another way.


## How to handle double taps

To handle double taps, we just have to detect how fast two press events are triggered. To implement this for our button style, first add this state:

```swift
@State
var doubleTapDate = Date()
```

then add the following function:

```swift
private extension GestureButtonStyle {

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

When the view is pressed, we check if there's an earlier registered press that should cause the new press to be handled as a double tap. If two presses happen within the `doubleTapTimeout`, we trigger a double tap, otherwise we set the `doubleTapDate` to the distant past to avoid a subsequent double tap.

Now, this is technically not a double tap gesture, rather a double press. However, rewriting it to behave as a double tap is a bit more complicated, so let's go with this for now.


## How to handle long presses

To handle long presses, we just have to detect how long a press event is being active. To implement this for our button style, first add this state:

```swift
@State
var longPressDate = Date()
```

then add the following function:

```swift
private extension GestureButtonStyle {

    func tryTriggerLongPressAfterDelay(triggered date: Date) {
        DispatchQueue.main.asyncAfter(deadline: .now() + longPressDelay) {
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

We first set the `longPressDate` to the current date, then trigger an async operation to be performed after a `longPressDelay`. If the date is still the same when it triggers, we trigger the `longPressAction`.

Our button style is now complete. All in all, it looks like this:

```swift
struct GestureButtonStyle: ButtonStyle {

    init(
        doubleTapTimeout: TimeInterval,
        longPressDelay: TimeInterval,
        pressAction: @escaping () -> Void,
        endAction: @escaping () -> Void,
        longPressAction: @escaping () -> Void,
        doubleTapAction: @escaping () -> Void
    ) {
        self.doubleTapTimeout = doubleTapTimeout
        self.longPressDelay = longPressDelay
        self.pressAction = pressAction
        self.endAction = endAction
        self.longPressAction = longPressAction
        self.doubleTapAction = doubleTapAction
    }

    private var doubleTapTimeout: TimeInterval
    private var longPressDelay: TimeInterval

    private var pressAction: () -> Void
    private var endAction: () -> Void
    private var longPressAction: () -> Void
    private var doubleTapAction: () -> Void

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

private extension GestureButtonStyle {

    func tryTriggerDoubleTap() -> Bool {
        let interval = Date().timeIntervalSince(doubleTapDate)
        guard interval < doubleTapTimeout else { return false }
        doubleTapAction()
        return true
    }

    func tryTriggerLongPressAfterDelay(triggered date: Date) {
        DispatchQueue.main.asyncAfter(deadline: .now() + longPressDelay) {
            guard date == longPressDate else { return }
            longPressAction()
        }
    }
}
```

We are however missing some functionality, such as detecting when a button is released. Let's define a button that will apply the style and fill out these missing parts.


## Defining a button

To implement the `release` gesture, lets create a button that uses a `releaseAction` as its tap action and applies the button style that we just defined:

```swift
struct GestureButton<Label: View>: View {

    init(
        doubleTapTimeout: TimeInterval = 0.5,
        longPressDelay: TimeInterval = 1,
        pressAction: @escaping () -> Void = {},
        releaseAction: @escaping () -> Void = {},
        endAction: @escaping () -> Void = {},
        longPressAction: @escaping () -> Void = {},
        doubleTapAction: @escaping () -> Void = {},
        label: @escaping () -> Label
    ) {
        self.style = GestureButtonStyle(
            doubleTapTimeout: doubleTapTimeout,
            longPressDelay: longPressDelay,
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

If you try this out, you'll see that it actually works. You can press, relase, double tap, long press etc. and scrolling still works. All made possible by the fact that button styles detect presses without blocking the scroll view scrolling.


## Conclusion & future work

While the above works well and is probably enough for most needs, it's actually not enough if you need to detect drag gestures. For instance, I'm trying to find a better way to handle gestures in a library of mine called [KeyboardKit]({{page.keyboardkit}}), where buttons must be able to handle a wide variety of gestures and transition to dragging when a button presents a callout with secondary actions.

I will therefore try to improve the solution above further to also handle drag operations. I will update this post if I manage to solve it. Until then, I'd love to hear what you think about the solution above.