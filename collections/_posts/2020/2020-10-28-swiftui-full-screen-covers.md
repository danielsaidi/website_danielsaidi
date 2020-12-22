---
title: An easier way to manage full screen covers in SwiftUI
date:  2020-10-28 20:00:00 +0100
tags:  swift swiftui
icon:  swiftuikit

lib:    https://github.com/danielsaidi/SwiftUIKit
source: https://github.com/danielsaidi/SwiftUIKit/tree/master/Sources/SwiftUIKit/Sheets
---

In this post, we'll look at an easier way to manage full screen covers in SwiftUI, that lets us reuse functionality, reduce state management and present many covers in the same way.


## TLDR;

If you find this post too long, I have added this to my [SwiftUIKit]({{page.lib}}) library. You can find the source code [here]({{page.source}}) and checkout the demo app for a fully working example.


## The basics

To present full screen covers in SwiftUI, you use the `fullScreenCover` modifier that takes an `isPresented` binding and a `content` function:

```swift
struct MyView: View {
    
    @State private var isCoverActive = false
    
    var body: some View {
        Button("Show cover", action: showCover)
            .fullScreenCover(isPresented: $isCoverActive, content: coverContent)
    }
    
    func coverContent() -> some View {
        NavigationView {
            Text("Hello, world!")
                .navigationBarItems(trailing: Button("Close", action: dismiss))
        }
    }

    func showCover() {
        isCoverActive = true
    }
}
```

This can become tricky when you have to present multiple covers from the same screen or reuse covers across an app. You may end up duplicating state and view builder logic and having to write the same code many times.

I therefore tried to find a way to work with covers in a more reusable way, that requires less code and less state while still being flexible to support both global and screen-specific covers.

It all begins with a very simple state manager that I call `FullScreenCoverContext`.


## FullScreenCoverContext

Instead of managing state in every view that should present covers, I use a `FullScreenCoverContext`:

```swift
public class FullScreenCoverContext: PresentationContext<AnyView> {
    
    public override func content() -> AnyView {
        contentView ?? EmptyView().any()
    }
    
    public func present<Cover: View>(_ cover: Cover) {
        present(cover.any())
    }
    
    public func present(_ provider: FullScreenCoverProvider) {
        present(provider.cover)
    }
}
```

As you can see, it basically only contains code for presenting a `Cover` (which is just a view) or a `FullScreenCoverProvider`. We'll come back to the provider shortly.

You may also notice that it inherits something called `PresentationContext`. Let's take a closer look at this base class.


## PresentationContext

Since I find that the cover presentation problem also is true for alerts, sheets etc., I have a `PresentationContext`, which is a pretty simple `ObservableObject` base class:

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

By calling the more specific functions in `FullScreenCoverContext`, the `PresentationContext` state is properly updated.


## FullScreenCoverProvider

As we saw earlier, `FullScreenCoverContext` can present a `Cover` and a `FullScreenCoverProvider`. `Cover` is just a view, while `FullScreenCoverProvider` is a protocol for anything that can provide a cover view:

```swift
public protocol FullScreenCoverProvider {
    
    var cover: AnyView { get }
}
```

With this in place, you can now implement custom covers in many different ways and present all of them the same way, using this new context.

For instance, you can have an enum that represents the various covers your app supports:

```swift
enum AppCover: FullScreenCoverProvider {
    
    case settings, tutorial
    
    var cover: AnyView {
        switch self {
        case .settings: return SettingsScreen().any()
        case .tutorial: return TutorialScreen().any()
        }
    }
}
```

This makes it possible to create app and view specific enums that contain your app's covers, which can all be presented in the same way.


## New fullScreenCover modifier

In SwiftUI, you present full screen covers by adding a modifier to the presenting view. With the new `FullScreenCoverContext` managing our state, we can create a new `fullScreenCover` modifier:

```swift
public extension View {
    
    func fullScreenCover(context: FullScreenCoverContext) -> some View {
        fullScreenCover(isPresented: context.isActiveBinding, content: context.content)
    }
}
```

The new modifier just provides the standard `fullScreenCover` modifier with the context's state, which makes things easier for you.


## Presenting a cover

With these new tools at our disposal, we can present full screen covers in a much easier way. First, create a context property:

```swift
@StateObject private var coverContext = FullScreenCoverContext()
```

then add a `fullScreenCover` modifier to the view:

```swift
.fullScreenCover(context: coverContext)
```

You can now present any `FullScreenCoverProvider` as a cover, for instance `AppCover`:

```swift
coverContext.present(AppCover.settings)
```

You can also present any custom view in the same way, using the same context:

```swift
coverContext.present(Text("Hello, I'm a custom cover."))
```

That's it, your view don't need multiple `@State` properties for different covers or to switch over an enum to determine which cover to show.


## @StateObject vs @ObservedObject

Use `@StateObject` for your contexts whenever possible. However, if you target `iOS 13` or if the context is created and managed by another part of your app, use `@ObservedObject`.


## Conclusion

As you can see, `FullScreenCoverContext` can be used to manage all different kind of views. It manages all state for you and lets you use a more convenient modifier. All you have to do is provide it with the covers you want to present.


## Source code

I have added these components to my [SwiftUIKit]({{page.lib}}) library. You can find the source code [here]({{page.source}}).