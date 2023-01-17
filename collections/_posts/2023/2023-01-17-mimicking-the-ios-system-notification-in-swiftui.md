---
title:  Mimicking the iOS system notification in SwiftUI
date:   2023-01-17 08:00:00 +0000
tags:   swiftui open-source

image:  /assets/headers/systemnotification.png
assets: /assets/blog/2023/2023-01-17/

tweet:  https://twitter.com/danielsaidi/status/1615347586059182080?s=20&t=3L_johIenQTCGsF9VPNjiw

lib:    https://github.com/danielsaidi/SystemNotification
gif:    https://github.com/danielsaidi/SystemNotification/raw/master/Resources/Demo.gif
---

Since I put a lot of time into various open-source projects, I've decided to write about them every once in a while, at least when I create new stuff or just to show how a specific library can be used. In this post, I'll show how you can use [SystemNotificaton]({{page.lib}}) to mimic the iOS system notifications.

![SystemNotification logo]({{page.image}})


## What is SystemNotification?

[SystemNotificaton]({{page.lib}}) is a `SwiftUI` library that lets you mimic the native iOS system notification, which for instance is presented when you toggle silent mode on and off, connect your AirPods etc.

![An animated gif that shows the SystemNotification library being used]({{page.gif}}){:width="200"}

This makes it possible to create notifications that behave almost like the native ones, which means that your app-specific notifications or hints can look right at home in iOS.

SystemNotification supports `iOS 14`, `macOS 11`, `tvOS 14` and `watchOS 7`, which means that you can use the same notification engine, and even the same notifications, on all platforms.


## Getting started

You must first add [SystemNotificaton]({{page.lib}}) to your project, preferably using the Swift Package Manager. You can then apply a system notification just like you apply a `sheet`, `alert` and `fullScreenModal`:

```swift
import SystemNotification

struct MyView: View {

    var body: some View {
        Text("Hello, world")
            .systemNotification(...)
    }
}
```

You can use both state- and context and message-based notifications, use pre-defined or custom views and style your notifications to great extent.


## State-based notifications

State-based notifications work just like state-based `sheet`, `alert` and `fullScreenModal` modifiers. Just provide the `systemNotification` modifier with an `isActive` binding and the view to present:

```swift
struct MyView: View {

    @State 
    private var isActive = false

    var body: some View {
        List {
            Button("Show") { isActive = true }
            Button("Hide") { isActive = false }
            Button("Toggle") { isActive.toggle() }
        }.systemNotification(
            isActive: $isActive,
            content: notificationView
        )
    }

    func notificationView() -> some View {
        Text("This is a custom notification")
            .padding()
    }
}
```

State-based notifications are easy to use, but if you plan on presenting many different notifications, the context-based approach is more versatile.


## Context-based notifications

Context-based notifications work similar to `sheet`, `alert` and `fullScreenModal`, but the modifier uses an observable `SystemNotificationContext` instead of state:

```swift
struct MyView: View {

    @StateObject 
    private var notification = SystemNotificationContext()

    var body: some View {
        List {
            Button("Show notification", action: showNotification)
            Button("Show orange notification", action: showOrangeNotification)
        }.systemNotification(notification)
    }
    
    func showNotification() {
        notification.present {
            Text("This notification uses a standard configuration")
                .padding()
        }
    }
    
    func showOrangeNotification() {
        notification.present(
            configuration: .init(backgroundColor: .orange)
        ) {
            Text("This notification uses a custom configuration")
                .foregroundColor(.white)
                .padding()
        }
    }
}
```

The context-based approach lets you use the same context in your entire application, with a single view modifier being applied to a single view hierarchy. You can then use the context to present a notification from anywhere in your app.

You can for instance apply a context-based notification to a root `TabView` or `NavigationView`, which ensures that the notification is presented regardless or where in the app the presentation is triggered, above any tab and navigation views.

Note that sheets and full screen covers require a separate modifier to be applied. You can however still use the same context in these modifiers. Also consider the various platform behaviors when picking a proper notification mechanism. For instance, since iPad sheets are presented as square modals in the center of the screen, a system notification may not be the best solution there.

Context-based notifications are very versatile and a great choice if you want to present many different notifications with a single modifier.


## Message-based notifications

Message-based notifications aim to mimic the iOS system notification look and behavior, with a leading tinted icon, a title and a message. To mimic this notification, just use a `SystemNotificationMessage` when presenting your notification:

```swift
struct MyView: View {

    @StateObject 
    private var notification = SystemNotificationContext()

    var body: some View {
        List {
            Button("Show notification", action: showNotification)
        }.systemNotification(notification)
    }
    
    func showNotification() {
        notification.present {
            SystemNotificationMessage(
                icon: Image(systemName: "bell.slash.fill"),
                title: "Silent Mode",
                text: "On",
                style: .init(iconColor: .red)
            )
        }
    }
}
```

The `style` parameter lets you modify the message style, like the colors, spacings etc. However, this only styles the message itself, not the notification. For that, we can use a `SystemNotificationStyle`.


## How to style a system notification

A `SystemNotification` can be styled in a couple of ways. For instance, you can provide a `style` in the view modifier, when you apply a system notification to a view:

```swift
struct MyView: View {

    @StateObject 
    private var notification = SystemNotificationContext()

    var body: some View {
        List {
            ...
        }.systemNotification(
            notification,
            style: .init(backgroundColor: .red)
        )
    }
}
```

This will be used as the default style, and applied to all notifications. You can however override this style whenever you present a notification with a context:

```
notification.present(
    style: .init(backgroundColor: .green)
) {
    Text("A green message")
        .foregroundColor(.white)
}
```

This custom style is applied for as long as the notification is presented, then reset to the default style.


## How to configure a system notification

Just like with the style, a `SystemNotification` can be configured in a couple of ways. For instance, you can provide a `configuration` in the view modifier:

```swift
struct MyView: View {

    @StateObject 
    private var notification = SystemNotificationContext()

    var body: some View {
        List {
            ...
        }.systemNotification(
            notification,
            configuration: .init(duration: 5)
        )
    }
}
```

This will be used as the default configuration, and applied to all notifications. You can however override it whenever you present a notification with a context:

```
notification.present(
    configuration: .init(duration: 10)
) {
    Text("An important, long-lived notification")
}
```

This configuration is applied for as long as the notification is presented, then reset to the default one.


## Putting things together

To wrap things up, let's create a view that presents a system notification that looks like the iOS system notification, but has a green background color, white content and stays on the screen a little longer.

All you need to do is to create a context, then present it with a custom configuration and style:

```swift
struct Preview: View {

    @StateObject
    private var notification = SystemNotificationContext()

    var body: some View {
        TabView {
            NavigationView {
                List {
                    ForEach(1..<100, id: \.self) { item in
                        Text("\(item)")
                            .onTapGesture {
                                notification.present(
                                    configuration: .init(duration: 5),
                                    style: .init(backgroundColor: .green)
                                ) {
                                    SystemNotificationMessage(
                                        icon: Image(systemName: "\(item).circle"),
                                        title: "You tapped item \(item)",
                                        text: "This will disappear in 5 seconds",
                                        style: .init(
                                            iconColor: .white,
                                            textColor: .white,
                                            titleColor: .white
                                        )
                                    )
                                }
                            }
                    }
                }.navigationTitle("Demo")
            }.tabItem { ... }

            Text("2").tabItem { ... }
            Text("3").tabItem { ... }
            Text("4").tabItem { ... }

        }.systemNotification(notification)
    }
}
```

When we present this, you can see that the notification is presented over the `NavigationView`, with some padding to the dynamic island:

![Notification above the navigation bar]({{page.assets}}above-nav.png)

To verify that the notification is also presented over the `TabView`, we can adjust the `style` to use the `bottom` edge instead of the default `top`:

```swift
.onTapGesture {
    notification.present(
        configuration: .init(duration: 5),
        style: .init(
            backgroundColor: .green,
            edge: .bottom
        )
    ) {
    ...
    }
}
```

When we present this, you can see that the notification is presented over the `TabView` as well:

![Notification above the navigation bar]({{page.assets}}above-tab.png)

As a bonus, you can use the same context from all these tabs. The notification will be presented above the `TabView`, which means that it will not disappear when you change tab.



## Conclusion

As you have seen in this article, the [SystemNotificaton]({{page.lib}}) library is pretty focused, and aims to do a single thing well. It also looks and behaves the same on `macOS`, `tvOS` and `watchOS`, which makes it versatile.

If you decide to try it out, I'd love to hear what you think about it.



