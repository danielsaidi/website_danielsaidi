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

In this post, we'll look at how to easily manage and present toasts in `SwiftUI`, in a way that borrows heavily from the [sheet]({{page.sheets}}) and [alert]({{page.alerts}}) approaches discussed in earlier posts.


## TLDR;

If you find this post too long, I have added this to my [SwiftUIKit]({{page.lib}}) library. You can find the source code [here]({{page.source}}) and checkout the demo app for a fully working example.


## The basics

A toast is traditionally a short message or feedback that is presented as an overlay modal for a brief moment, before it is automatically dismissed. It usually slides or fades in from the top or bottom of the screen and can also present undo actions or other related actions.

While the toast pattern has been a first class citizen in Android for a long time, Apple has never (at least to my knowledge) provided a native toast api for iOS. There are numerous great packages for this, but you are more or less forced to go 3rd party or build it yourself.

Luckily, this is very easy to do in SwiftUI. Let's look at how to build a way to present toasts in a way similar [sheets]({{page.sheets}}) and [alerts]({{page.alerts}}).


## ToastContext

Instead of managing state in every view that should be able to present toasts, I prefer to create an observable object that handles this.

For toasts, it all starts with a simple, observable class called `ToastContext`:

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

As you can see, `ToastContext` basically only contains code for presenting a `Toast` (which is just a view) or a `ToastProvider`. We'll come back to the provider concept shortly.

You may also notice that it inherits something called `PresentationContext`. Let's take a closer look at this base class.


## PresentationContext

Since I want to handle toasts in the same way as alerts and sheets, I have created a `PresentationContext` on which I base other similar solutions to the same kind of problem.

`PresentationContext` is an `ObservableObject` base class that handles state and views for presentable things, like sheets, alerts, toasts etc. It's pretty simple:

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

As we saw earlier, `ToastContext` can present a `Toast` and an `ToastProvider`, where `ToastProvider` is a protocol for anything that can provide a toast view:

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


## Toast modifiers

In `SwiftUI`, you present alerts and sheets with by adding a modifier to the presenting view and set the state binding to true. 

We can create such a modifier for presenting toasts as well:

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

With these new tools at our disposal, we can present toasts in a much easier way.

First, create a context property in any view that should be able to present  toasts:

```swift
@ObservedObject private var toastContext = ToastContext()
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

I use a `ToastStyle` to style the toasts in various ways. That way, you can create texts, images etc. in the same way, by just providing a style to the `toast` modifier. That separates the style of the toast from the style of its content.


## ObservedObject vs State

`@ObservedObject` mostly works great, but I have had problems in multiplatform apps that target iOS 14, where toasts don't appear or immediately close. Replacing `@ObservedObject` with `@State` has solved the problem for me, but it is not consistent. For instance, it does not work in the demo app this post links to. My advice is to try `@ObservedObject` first and replace it with `@State` if it doesn't work.


## Future Improvements

Is I previously mentioned, the `toast` modifier above just adds the toast as an overlay, which means that it will appear at the center over the presenting view. I plan on extending it with positioning and transition options.

The toast logic I use has support for additional styling, but you could very well just implement an app-specific toast as well. I just added the style option since I need it for my personal projects.


## Conclusion

As you can see, `ToastContext` can be used to manage all different kind of toasts. It manages all state for you and lets you use a more convenient modifier. All you have to do is provide it with the toasts you want to present.


## Source code

I have added these components to my [SwiftUIKit]({{page.lib}}) library. You can find the source code [here]({{page.source}}).