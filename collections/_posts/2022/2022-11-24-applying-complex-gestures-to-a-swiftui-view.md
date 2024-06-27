---
title:  Applying complex gestures to a SwiftUI view
date:   2022-11-24 08:00:00 +0000
tags:   swiftui open-source gestures

tweet:  https://twitter.com/danielsaidi/status/1596179247336681473
toot:   https://mastodon.social/@danielsaidi/109405390884591414

post:   /blog/2022/11/16/using-complex-gestures-in-a-scroll-view

github: https://github.com/danielsaidi/SwiftUIKit
timer: https://github.com/danielsaidi/SwiftUIKit/blob/master/Sources/SwiftUIKit/Gestures/RepeatGestureTimer.swift
button: https://github.com/danielsaidi/SwiftUIKit/blob/master/Sources/SwiftUIKit/Gestures/GestureButton.swift
---

As we saw in [last week's post]({{page.post}}), gestures in a `ScrollView` are complicated, since they can block the scrolling. Without a scroll view, things become a lot easier. Let's take a look.


## Less complexity

If we don't have to consider the scroll view complexities, we can focus on handling estures in the cleanest way possible. For my needs, I need to handle the following gestures:

* Presses
* Releases (inside and outside)
* Long presses
* Hold presses
* Double taps
* Drag started
* Drag changed
* Drag ended
* Gesture ended

As you will see, we will be able to detect all these gestures by using a single drag gesture. Let's start with creating a view to which we can apply all these gestures.


## Creating a ScrollView-incompatible gesture button

Let's start with creating a `GestureButton` that we will use to implement all these gestures:

```swift
public struct GestureButton<Label: View>: View {

    init(
        isPressed: Binding<Bool>? = nil,
        pressAction: Action? = nil,
        releaseInsideAction: Action? = nil,
        releaseOutsideAction: Action? = nil,
        longPressDelay: TimeInterval = GestureButtonDefaults.longPressDelay,
        longPressAction: Action? = nil,
        doubleTapTimeout: TimeInterval = GestureButtonDefaults.doubleTapTimeout,
        doubleTapAction: Action? = nil,
        repeatDelay: TimeInterval = GestureButtonDefaults.repeatDelay,
        repeatTimer: RepeatGestureTimer = .shared,
        repeatAction: Action? = nil,
        dragStartAction: DragAction? = nil,
        dragAction: DragAction? = nil,
        dragEndAction: DragAction? = nil,
        endAction: Action? = nil,
        label: @escaping LabelBuilder
    ) {
        self.isPressedBinding = isPressed ?? .constant(false)
        self.pressAction = pressAction
        self.releaseInsideAction = releaseInsideAction
        self.releaseOutsideAction = releaseOutsideAction
        self.longPressDelay = longPressDelay
        self.longPressAction = longPressAction
        self.doubleTapTimeout = doubleTapTimeout
        self.doubleTapAction = doubleTapAction
        self.repeatDelay = repeatDelay
        self.repeatTimer = repeatTimer
        self.repeatAction = repeatAction
        self.dragStartAction = dragStartAction
        self.dragAction = dragAction
        self.dragEndAction = dragEndAction
        self.endAction = endAction
        self.label = label
    }

    public typealias Action = () -> Void
    public typealias DragAction = (DragGesture.Value) -> Void
    public typealias LabelBuilder = (_ isPressed: Bool) -> Label

    var isPressedBinding: Binding<Bool>

    let pressAction: Action?
    let releaseInsideAction: Action?
    let releaseOutsideAction: Action?
    let longPressDelay: TimeInterval
    let longPressAction: Action?
    let doubleTapTimeout: TimeInterval
    let doubleTapAction: Action?
    let repeatDelay: TimeInterval
    let repeatTimer: RepeatGestureTimer
    let repeatAction: Action?
    let dragStartAction: DragAction?
    let dragAction: DragAction?
    let dragEndAction: DragAction?
    let endAction: Action?
    let label: LabelBuilder

    @State
    private var isPressed = false

    @State
    private var longPressDate = Date()

    @State
    private var releaseDate = Date()

    @State
    private var repeatDate = Date()

    public var body: some View {
        label(isPressed)
            .onChange(of: isPressed) { isPressedBinding.wrappedValue = $0 }
            .accessibilityAddTraits(.isButton)
    }
}
```

Wow, that's a pretty huge initializer. Feel free to group the parameters if you see fit, but I've chosen to keep it like this for simplicity. Let's take a look at the parameters.

The initializer lets us provide an `isPressed` binding to observe the pressed state. Note that we set it to an `isPressedBinding` property and have a second `isPressed` state that we use to avoid problems if a `.constant` binding is provided.

We can provide a bunch of actions and configurations to handle `press`, `release inside`, `release outside`, `long press`, `double tap`, `repeats` (press and hold), `drag start`, `drag`, `drag end` and `gesture end` in a clean way.

In the body, we display the `label` builder result. It takes `isPressed` as input, then makes it sync `isPressed` changes to `isPressedBinding`. We also add accessibility traits to tell the system that this is a button.


## Adding a drag gesture to the button

To detect whether a press is released inside or outside of the button's bounds, we use a `GeometryReader` that will wrap the view to which the gestures are applied.

However, applying the `GeometryReader` to the button label would make the button greedy, which would cause it to float within a view that takes up all the available space. 

To avoid this, we can keep the button view as is, and instead add the geometry reader as an `overlay`, then apply the drag gesture to a view within the reader.

Let's start with defining this `gestureView`:

```swift
private extension GestureButton {

    var gestureView: some View {
        GeometryReader { geo in
            EmptyView() // We'll add the correct view soon
        }
    }
}
```

then add the view as an `overlay` to the button label:

```swift
var body: some View {
    label(isPressed)
        .overlay(gestureView)
        .onChange(of: isPressed) { isPressedBinding.wrappedValue = $0 }
        .accessibilityAddTraits(.isButton)
}
```

We can now replace the empty view with a view with gestures. Let's use `Color.clear` and specify a `.contentShape` to it to make it detect taps, then add a `DragGesture` to it:

```swift
var gestureView: some View {
    Color.clear
        .contentShape(Rectangle())
        .gesture(
            DragGesture(minimumDistance: 0)
                .onChanged { _ in /* Let's add code here */ }
                .onEnded { _ in /* ...and here */ }
        )
}
```

With the gesture in place, we can now start to implement the various actions that we want to be able to trigger with this single gesture.


## Implementing gesture actions

The drag gesture has an `onChanged` and an `onEnded` event, but no `onStarted`. This means that we have to use `onChanged` to handle both when a gesture starts and when it changes.

Let's define two functions for trying to handle presses and releases:

```swift
private extension GestureButton {

    func tryHandlePress(_ value: DragGesture.Value) {
        /* Let's add code here */
    }

    func tryHandleRelease(_ value: DragGesture.Value, in geo: GeometryProxy) {
        /* ...and here */
    }
}
```

We can now call these functions from the drag gesture above:

```swift
var gestureView: some View {
    Color.clear
        .contentShape(Rectangle())
        .gesture(
            DragGesture(minimumDistance: 0)
                .onChanged { value in
                    tryHandlePress(value)
                    dragAction?(value)
                }
                .onEnded { value in 
                    tryHandleRelease(value, in: geo)
                }
        )
}
```

`onChanged` will try to handle a press whenever the drag gesture starts, but it should only be triggered once and also call the `dragAction`. `onEnded` will only try to handle the release.


## Handling presses

Trying to handle presses involves the following operations:

```swift
func tryHandlePress(_ value: DragGesture.Value) {
    if isPressed { return }
    isPressed = true
    pressAction?()
    dragStartAction?(value)
    tryTriggerLongPressAfterDelay()
    tryTriggerRepeatAfterDelay()
}
```

Since we should only handle presses once, we abort if `isPressed` is `true`. If not, we set it to `true`, call `pressAction` & `dragStart`, after triggering the long press and repeat actions.

We now call `pressAction`, `dragStartAction` & `dragAction` at the correct places. Let's look at how to trigger a long press.


## How to trigger a long press

Trying to trigger a long press involves the following operations:

```swift
func tryTriggerLongPressAfterDelay() {
    guard let action = longPressAction else { return }
    let date = Date()
    longPressDate = date
    let delay = longPressDelay
    DispatchQueue.main.asyncAfter(deadline: .now() + delay) {
        guard self.longPressDate == date else { return }
        action()
    }
}
```

We first check that we have a `longPressAction`, otherwise we abort the operation. If we have one, we take the current date and set the `longPressDate` to it. 

We then use `longPressDelay` to trigger an async operation that check if `longPressDate` is still the same. If so, the gesture is still active, and should trigger the `longPressAction`.


## How to trigger a repeating action

With the long press taken care of, let's look at how to handle repeats, which are the actions that trigger on a regular basis for as long as you keep the button pressed.

To handle the repeats, we will use a [RepatGestureTimer]({{page.timer}}), which is a simple class that starts calling an action with a certain interval when it's started, then stops when it's stopped.

Trying to trigger a repeating action involve the following operations:

```swift
func tryTriggerRepeatAfterDelay() {
    guard let action = repeatAction else { return }
    let date = Date()
    repeatDate = date
    let delay = repeatDelay
    DispatchQueue.main.asyncAfter(deadline: .now() + delay) {
        guard self.repeatDate == date else { return }
        repeatTimer.start(action: action)
    }
}
```

Just like with long presses, we check that we have a `repeatAction`, otherwise we abort the operation. If we have one, we trigger a delay and start the `repeatTimer` if the gesture is still active after the delay.

That's all we need to do when we press the button. Let's now look how to handle when the drag gesture eventually ends.


## Handling releases

Trying to handle releases involves the following operations:

```swift
func tryHandleRelease(_ value: DragGesture.Value, in geo: GeometryProxy) {
    if !isPressed { return }
    isPressed = false
    longPressDate = Date()
    repeatDate = Date()
    repeatTimer.stop()
    releaseDate = tryTriggerDoubleTap() ? .distantPast : Date()
    if geo.contains(value.location) {
        releaseInsideAction?()
    } else {
        releaseOutsideAction?()
    }
    dragEndAction?(value)
    endAction?()
}
```

Since we should only handle releases once, we abort if `isPressed` is `false`. If not, we set it to `false`, then reset the `longPressDate` and `repeatDate` and stop the `repeatTimer`. 

We then update `releaseDate` according to if the release counts as a double tap or not, call the proper release action based on the end location then call `dragEndAction` & `endAction`.


## How to trigger a double tap

Trying to trigger a double tap involves the following operations:

```swift
func tryTriggerDoubleTap() -> Bool {
    let interval = Date().timeIntervalSince(releaseDate)
    let isDoubleTap = interval < doubleTapTimeout
    if isDoubleTap { doubleTapAction?() }
    return isDoubleTap
}
```

We use the `releaseDate` that we update in `tryHandleRelease` to check how long time that has passed since the last release. 

The release counts as a double tap if the time is less than the `doubleTapTimeout`. If so, we call the `doubleTapAction` and return the result, which updates the `releaseDate` accordingly.


## How to trigger the correct release action

Triggering the correct release action involves using the `GeometryProxy` to check if a gesture was released inside or outside of the view's bounds. To do this, we can use this extension:

```swift
private extension GeometryProxy {

    func contains(_ dragEndLocation: CGPoint) -> Bool {
        let x = dragEndLocation.x
        let y = dragEndLocation.y
        guard x > 0, y > 0 else { return false }
        guard x < size.width, y < size.height else { return false }
        return true
    }
}
```

And that's it! If the release is done within the view, we call `releaseInsideAction` otherwise we call `releaseOutsideAction`. With this in place, our gesture button is done.


## Conclusion

`GestureButton` lets you handle multiple gestures with a single button. You can detect many different type of gestures, using a single `DragGesture`.

I have added `GestureButton` to my [SwiftUIKit]({{page.github}}) library. You can find the source code [here]({{page.button}}). If you decide to give it a try, I'd be very interested in hearing what you think.