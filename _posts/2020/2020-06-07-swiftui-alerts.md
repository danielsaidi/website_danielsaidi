---
title:  "An easier way to manage alerts in SwiftUI"
date:   2020-06-07 10:00:00 +0100
tags:   swift swiftui
icon:   swiftuikit

lib:    https://github.com/danielsaidi/SwiftUIKit
source: https://github.com/danielsaidi/SwiftUIKit/tree/master/Sources/SwiftUIKit/Alerts
tests:  https://github.com/danielsaidi/SwiftUIKit/tree/master/Tests/SwiftUIKitTests/Alerts
---

In this post, we'll look at how to manage SwiftUI alerts in a more maintainable and flexible way. This will allow us to present different alerts in the same way and reduce state management.


## The basics

To present alerts in `SwiftUI`, you use the `alert` modifier. It takes an `isPresented` binding and an alert-producing `content` function:

```swift
struct MyView: View {
    
    @State private var isAlertActive = false
    private let alert = Alert(title: Text("Hello, world!"))
    
    var body: some View {
        Button("Show sheet", action: showSheet)
            .alert(isPresented: $isAlertActive, content: { alert })
        }
    }
}
```

Easy enough, right? Well, this basic example is, but I think it becomes tricky to manage as soon as you want to present multiple alerts from the same screen or reuse alerts across your app.

One problem is that you keep duplicating `isAlertActive` logic everywhere. You also have to duplicate the alert producing logic whenever you present the same alert from multiple views.

I solved this particular problem for modal sheets in yesterday's sheet-specific post, and will use the same approach here. It is a more reusable way to manage alerts that requires less code and provides more flexible support for both global and screen-specific alerts.


## AlertContext to the rescue!

After experimenting some with this, I came up with a way to let us reuse a bunch of this alert-specific logic by gathering it in an `AlertContext` class:

```swift
public class AlertContext: ObservableObject {
    
    public init() {}
    
    @Published public var isActive = false
    
    public private(set) var alertView: Alert? {
        didSet { isActive = alertView != nil }
    }
    
    public func alert() -> Alert {
        alertView ?? Alert(title: Text(""))
    }
    
    public func present(_ alert: Alert) {
        alertView = alert
    }
    
    public func present(_ alert: AlertPresentable) {
        alertView = alert.alert
    }
}
```

The context can be used to present any `Alert` and anything that implements `AlertPresentable`, which can be implemented by anything that can provide an `alert`:

```swift
public protocol AlertPresentable {
    
    var alert: Alert { get }
}
```

With this in place, you can now implement custom alerts in many different ways and present all of them the same way, using this new context.

To use this context within your views, just create a context instance and call any of its `present` functions. To bind it to a view, just use the `alert` modifier as you normally do:
 
 ```swift
 .alert(isPresented: $alertContext.isActive, content: alertContext.alert)
 ```

You can define various `AlertPresentable` types in your app. For instance, if you have a set of alerts that should be presented from multiple views, you could create an `AppAlert` enum:

```swift
enum AppAlert: AlertPresentable {
    
    case warning
    
    var alert: Alert {
        switch self {
        case .warning: return Alert(title: Text("Don't eat yellow snow!"))
        }
    }
}
```

Then present it as such:

```swift
alertContext.present(AppSheet.warning)
```

You can also present any custom alert with the same context:

```swift
alertContext.present(Alert(title: Text("Hello! I'm a custom alert.")))
```

If the settings screen has a bunch of alerts that should only be presented from settings, you could create a separate `SettingsAlert` enum and use it in the exact same way.

This means that `AlertContext` can be used to manage all different kind of alerts. It manages your state, while you just have to provide it with sheets you want to present.


## Source code

I have added these services to my [SwiftKit]({{page.lib}}) library. You can find the source code [here]({{page.source}}) and the unit tests [here]({{page.tests}}).