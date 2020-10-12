---
title: An easier way to manage alerts in SwiftUI
date:  2020-06-07 10:00:00 +0100
tags:  swift swiftui
icon:  swiftuikit

lib:    https://github.com/danielsaidi/SwiftUIKit
source: https://github.com/danielsaidi/SwiftUIKit/tree/master/Sources/SwiftUIKit/Alerts
---

In this post, we'll look at an easier way to manage alerts in `SwiftUI`, that lets us reuse functionality, reduce state management and present many different alerts in the same way.


## The basics

To present alerts in a `SwiftUI` app, you would normally use an `alert` modifier that takes an `isPresented` binding and an alert-producing `content` function:

```swift
struct MyView: View {
    
    @State private var isAlertActive = false
    private let alert = Alert(title: Text("Hello, world!"))
    
    var body: some View {
        Button("Show alert", action: showAlert)
            .alert(isPresented: $isAlertActive, content: { alert })
    }

    func showAlert() {
        isAlertActive = true
    }
}
```

This is simple, sure, but I think it becomes tricky to manage alerts as soon as you want to present multiple alerts from the same screen or reuse alerts across an app. You may end up duplicating `isAlertActive` logic as well as the alert builder logic.

I therefore tried to find a way to work with alerts in a more reusable way, that requires less code and less state while still being flexible to support both global and screen-specific alerts.


## AlertContext

After pondering this problem for a while, I think I have come up with a solution that simplies working with `SwiftUI` alerts. It all starts with an observable class called `AlertContext`:

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

As you can see, `AlertContext` basically only contains code for presenting an `AlertProvider`. We'll come back to this concepts shortly.

You may also notice that it inherits something called `PresentationContext`. Let's take a closer look at this base class.


## PresentationContext

Since I find that the alert problem also is true for sheets, context menus etc., I have created a `PresentationContext` on which I base other similar solutions to the same kind of problem.

`PresentationContext` is an `ObservableObject` base class that handles state and views for presentable things, like sheets, alerts, toasts etc. It's a pretty simple little thing:

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

In fact, this means that besides the `present(_ provider: AlertProvider)` function, `AlertContext` also gets a `present(_ alert: Alert)` function from the generic `present(_ content: Content)` function.


## AlertProvider

As we saw earlier, `AlertContext` can present an `Alert` and an `AlertProvider`, where `AlertProvider` is a protocol for anything that can provide an alert:

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
        case .warning: return Alert(title: Text("Don't eat yellow snow!"))
        }
    }
}
```


## New alert modifier

Since `AlertContext` handles all state for us, we can now implement a new `alert` modifier for presenting alerts:

```swift
public extension View {
    
    func alert(context: AlertContext) -> some View {
        alert(isPresented: context.isActiveBinding, content: context.content)
    }
}
```

The new modifier just provides the standard `alert` modifier with the context's state, which makes things easier for you.


## Presenting an alert

With these new tools at our disposal, we can present alerts in a much easier way.

First, create a `@State` property in any view that should be able to present alerts:

```swift
@State private var alertContext = AlertContext()
```

then add an `AlertContext` specific view modifier to the view:

```swift
.alert(context: alertContext)
```

You can now present any view and `AlertProvider` as a alert, for instance the `AppAlert`:

```swift
alertContext.present(AppAlert.warning)
```

You can also present any custom alerts in the same way, using the same context.


## Conclusion

As you can see, `AlertContext` can be used to manage all different kind of alerts. It manages all state for you and lets you use a more convenient alert modifier. All you have to do is provide it with the alerts you want to present.


## Source code

I have added these components to my [SwiftUIKit]({{page.lib}}) library. You can find the source code [here]({{page.source}}).