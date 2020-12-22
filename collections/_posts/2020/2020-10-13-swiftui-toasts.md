---
title: An easy way to manage toasts in SwiftUI
date:  2020-10-13 08:00:00 +0100
tags:  swift swiftui
icon:  swiftuikit

lib:    https://github.com/danielsaidi/SwiftUIKit
source: https://github.com/danielsaidi/SwiftUIKit/tree/master/Sources/SwiftUIKit/Toasts
alerts: http://danielsaidi.com/blog/2020/06/07/swiftui-alerts
sheets: http://danielsaidi.com/blog/2020/06/06/swiftui-sheets
---

In this post, we'll look at how to easily manage and present toasts in SwiftUI, in a way that borrows heavily from the [sheet]({{page.sheets}}) and [alert]({{page.alerts}}) approaches discussed in earlier posts.


## TLDR;

If you find this post too long, I have added this to my [SwiftUIKit]({{page.lib}}) library. You can find the source code [here]({{page.source}}) and checkout the demo app for a fully working example.


## The basics

A toast is a short message or feedback that is presented as an overlay for a short moment, or until the user performs an action within it. It usually slides or fades in and can also present undo or other related actions.

While the toast pattern has been a first class citizen in Android for a long time, Apple has never (at least to my knowledge) provided a native toast api for iOS. There are numerous great packages for this, but you are more or less forced to go 3rd party or build it yourself.

Luckily, this is very easy to do in SwiftUI. Let's look at how to build a way to present toasts in a way similar to [sheets]({{page.sheets}}) and [alerts]({{page.alerts}}).

It all begins with a very simple state manager that I call `ToastContext`.


## ToastContext

Instead of managing state in every view that should present toasts, I use a `ToastContext`:

```swift
public class ToastContext: PresentationContext<AnyView> {
    
    public override func content() -> AnyView {
        contentView ?? EmptyView().any()
    }
    
    public func present<Toast: View>(_ toast: Toast) {
        present(toast.any())
    }
    
    public func present(_ provider: ToastProvider) {
        contentView = provider.toast
    }
}
```

As you can see, it basically only contains code for presenting a `Toast` (which is just a view) or a `ToastProvider`. We'll come back to the provider shortly.

You may also notice that it inherits something called `PresentationContext`. Let's take a closer look at this base class.


## PresentationContext

Since I find that the toast presentation problem also is true for alerts, sheets etc., I have a `PresentationContext`, which is a pretty simple `ObservableObject` base class:

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

By calling the more specific functions in `ToastContext`, the `PresentationContext` state is properly updated.


## ToastProvider

As we saw earlier, `ToastContext` can present a `Toast` and an `ToastProvider`. `Toast` is just a view, while `ToastProvider` is a protocol for anything that can provide a toast view:

```swift
public protocol ToastProvider {
    
    var toast: AnyView { get }
}
```

With this in place, you can now implement custom toasts in many different ways and present all of them the same way, using this new context.

For instance, you can have an enum that represents the various toasts your app supports:

```swift
enum AppToast: ToastProvider {
    
    case warning
    
    var toast: AnyView {
        switch self {
        case .warning: return Text("The e-mail was deleted")
        }
    }
}
```

This makes it possible to create app and view specific enums that contain your app's toast logic, which makes your presenting views easier to manage.


## Toast modifiers

In SwiftUI, you present alerts and sheets by adding modifiers to the presenting view. We can create such modifiers for toasts as well:

```swift
func toast<Content: View>(
    isPresented: Binding<Bool>,
    content: () -> Content,
    duration seconds: TimeInterval = 2) -> some View {
    if isPresented.wrappedValue { deactivate(isPresented, afterDuration: seconds) }
    let opacity = isPresented.wrappedValue ? 1.0 : 0.0
    return overlay(content()
        .opacity(opacity))
}
```

Since `ToastContext` can handle all state for us, we can also implement a context-based `toast` modifier that simplifies things further:

```swift
func toast(
    context: ToastContext,
    duration seconds: TimeInterval = 2) -> some View {
    toast(isPresented: context.isActiveBinding,
        content: context.content,
        duration: seconds)
}
```

Unlike alerts and sheets, toasts have a limited time they should be presented. We can set this time with the `duration` parameter.

If you look at the first modifier, you can see that it will add the toast as an overlay to the presenting view, which means that it will appear at the center over the view. This is generally not the standard toast behavior, so you have to do some manual positioning and transitioning (read more under "Future Improvements").


## Presenting a toast

With these new tools at our disposal, we can present toasts in a much easier way. First, create a context property:

```swift
@StateObject private var toastContext = ToastContext()
```

then add an `toast` modifier to the view:

```swift
.toast(context: toastContext)
```

You can now present any `ToastProvider` as a toast, for instance `AppToast`:

```swift
toastContext.present(AppToast.warning)
```

You can also present any custom view in the same way, using the same context:

```swift
toastContext.present(Text("Hello, I'm a custom toast!"))
```

That's it, your view don't need multiple `@State` properties for different toasts or to switch over an enum to determine which toast to show.


## Styling a toast

The solution above can show any view you like as a toast, which means that you can create specific toast style modifiers to style views in a consistent way.

However, the toast modifier that I link to from this post has functionality for providing styles, which further separates toast styling from the toast content.


## @StateObject vs @ObservedObject

Use `@StateObject` for your contexts whenever possible. However, if you target `iOS 13` or if the context is created and managed by another part of your app, use `@ObservedObject`.


## Future Improvements

Is I previously mentioned, the `toast` modifier above just adds the toast as an overlay, which means that it will appear at the center over the presenting view. I plan on extending it with positioning and transition options.

The toast logic I use has support for additional styling, but you could very well just implement an app-specific toast as well. I just added the style option since I need it for my personal projects.


## Conclusion

As you can see, `ToastContext` can be used to manage all different kind of toasts. It manages all state for you and lets you use a more convenient modifier. All you have to do is provide it with the toasts you want to present.


## Source code

I have added these components to my [SwiftUIKit]({{page.lib}}) library. You can find the source code [here]({{page.source}}).