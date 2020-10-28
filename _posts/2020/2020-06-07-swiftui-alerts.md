---
title: An easier way to manage alerts in SwiftUI
date:  2020-06-07 10:00:00 +0100
tags:  swift swiftui
icon:  swiftuikit

lib:    https://github.com/danielsaidi/SwiftUIKit
source: https://github.com/danielsaidi/SwiftUIKit/tree/master/Sources/SwiftUIKit/Alerts
---

In this post, we'll look at an easier way to manage alerts in `SwiftUI`, that lets us reuse functionality, reduce state management and present many different alerts in the same way.


## TLDR;

If you find this post too long, I have added this to my [SwiftUIKit]({{page.lib}}) library. You can find the source code [here]({{page.source}}) and checkout the demo app for a fully working example.


## The basics

To present alerts in a `SwiftUI` app, you use the `alert` modifier that takes an `isPresented` binding and a `content` function:

```swift
struct MyView: View {
    
    @State private var isAlertActive = false
    
    var body: some View {
        Button("Show alert", action: showAlert)
            .alert(isPresented: $isAlertActive, content: alert)
    }

    func alert() -> Alert {
        Alert(title: Text("Hello, world!"))
    }

    func showAlert() {
        isAlertActive = true
    }
}
```

This can becomee tricky when you have to present multiple alerts from the same screen or reuse alerts across an app. You may end up duplicating state and view builder logic and having to write the same code many times.

I therefore tried to find a way to work with alerts in a more reusable way, that requires less code and less state while still being flexible to support both global and screen-specific alerts.

It all begins with a very simple state manager that I call `AlertContext`.


## AlertContext

Instead of managing state in every view that should present alerts, I use a `AlertContext`:

```swift
public class AlertContext: PresentationContext<Alert> {
    
    public override func content() -> Alert {
        contentView ?? Alert(title: Text(""))
    }
    
    public func present(_ provider: AlertProvider) {
        contentView = provider.alert
    }
}
```

As you can see, it basically only contains code for presenting an `AlertProvider`. We'll come back to the provider shortly.

You may also notice that it inherits something called `PresentationContext`. Let's take a closer look at this base class.


## PresentationContext

Since I find that the alert presentation problem also is true for sheets, toasts etc., I have a `PresentationContext`, which is a pretty simple `ObservableObject` base class:

```swift
public class PresentationContext<Content>: ObservableObject {
    
    public init() {}
    
    @Published public var isActive = false
    
    public var isActiveBinding: Binding<Bool> {
        .init(get: { self.isActive },
              set: { self.isActive = $0 }
        )
    }
    
    open func content() -> Content { contentView! }
    
    public internal(set) var contentView: Content? {
        didSet { isActive = contentView != nil }
    }
    
    public func dismiss() {
        isActive = false
    }
    
    public func present(_ content: Content) {
        contentView = content
    }
}
```

By calling the more specific functions in `AlertContext`, the `PresentationContext` state is properly updated.

In fact, this means that besides the `present(_ provider: AlertProvider)` function, `AlertContext` also gets an implicit `present(_ alert: Alert)` function from the generic `present(_ content: Content)` function.


## AlertProvider

As we saw earlier, `AlertContext` can present an `Alert` and an `AlertProvider`. `Alert` is just a standard SwiftUI alert, while `AlertProvider` is a protocol for anything that can provide an alert:

```swift
public protocol AlertProvider {
    
    var alert: Alert { get }
}
```

With this in place, you can now implement custom alerts in many different ways and present all of them the same way, using this new context.

For instance, you can have an enum that represents the various alerts your app supports:

```swift
enum AppAlert: AlertProvider {
    
    case warning
    
    var alert: Alert {
        switch self {
        case .warning: return Alert(title: Text("Something went wrong!"))
        }
    }
}
```

This makes it possible to create app and view specific enums that contain your app's alerts, which can all be presented in the same way.


## New alert modifier

In `SwiftUI`, you present alerts by adding a modifier to the presenting view. With the new `AlertContext` managing our state, we can create a new `alert` modifier:

```swift
public extension View {
    
    func alert(context: AlertContext) -> some View {
        alert(isPresented: context.isActiveBinding, content: context.content)
    }
}
```

The new modifier just provides the standard `alert` modifier with the context's state, which makes things easier for you.


## Presenting an alert

With these new tools at our disposal, we can present alerts in a much easier way. First, create a context property:

```swift
@StateObject private var alertContext = AlertContext()
```

then add an `alert` modifier to the view:

```swift
.alert(context: alertContext)
```

You can now present any `AlertProvider` as a alert, for instance `AppAlert`:

```swift
alertContext.present(AppAlert.warning)
```

You can also present any custom alerts in the same way, using the same context.

That's it, your view don't need multiple `@State` properties for different alerts or to switch over an enum to determine which alert to show.


## @StateObject vs @ObservedObject

Use `@StateObject` for your contexts whenever possible. However, if you target `iOS 13` or if the context is created and managed by another part of your app, use `@ObservedObject`.


## Conclusion

As you can see, `AlertContext` can be used to manage all different kind of alerts. It manages all state for you and lets you use a more convenient modifier. All you have to do is provide it with the alerts you want to present.


## Source code

I have added these components to my [SwiftUIKit]({{page.lib}}) library. You can find the source code [here]({{page.source}}).