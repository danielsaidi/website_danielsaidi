---
title: An easier way to manage full screen covers in SwiftUI
date:  2020-10-28 20:00:00 +0100
tags:  swiftui full-screen-cover
icon:  swiftui

lib:    https://github.com/danielsaidi/SwiftUIKit
source: https://github.com/danielsaidi/SwiftUIKit/tree/master/Sources/SwiftUIKit/Presentation/FullScreenCover
---

In this post, we'll look at an easier way to manage full screen covers in SwiftUI, in a way that lets us reuse functionality, reduce state management and present many different covers with the same modifier.


## TLDR;

If you find this post too long, I have added this to my [SwiftUIKit]({{page.lib}}) library. You can find the source code [here]({{page.source}}). Feel free to try it out and let me know what you think.


## The basics

To present covers in SwiftUI, you use the `fullScreenCover` modifier that takes an `isPresented` binding and a `content` function (since this was written, more options have been added):


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

This can become tricky when you have to present multiple covers from the same screen or reuse covers across an app. You may end up duplicating code, state, view builders etc.

I have therefore tried to find a way to handle covers in a more reusable way, that requires less code and less state, while still being flexible to support both global and screen-specific covers.

It all begins with a very simple state manager that I call `FullScreenCoverContext`.


## Full screen cover context

Instead of managing state in every view that presents covers, I use a `FullScreenCoverContext`:

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

As you can see, it contains code for presenting a `Cover` (which is just a view) or a cover provider. We'll come back to the provider shortly.

You may also notice that it inherits something called `PresentationContext`. Let's take a closer look at this base class.


## PresentationContext

Since I find that this problem is also true for alerts, sheets etc. I have a `PresentationContext`, which is a small `ObservableObject` base class with an `isActive` binding and a generic `content` view:

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

By calling the cover-specific functions in `FullScreenCoverContext`, the context is properly updated.


## Cover provider

As we saw earlier, `FullScreenCoverContext` can present views and cover providers. `Cover` is just a view, while `FullScreenCoverProvider` is a protocol for anything that can provide cover views:

```swift
public protocol FullScreenCoverProvider {
    
    var cover: AnyView { get }
}
```

For instance, you can have an enum that represents various covers that your app supports:

```swift
enum AppCover: FullScreenCoverProvider {
    
    case settings
    case tutorial
    
    var cover: AnyView {
        switch self {
        case .settings: return SettingsScreen().any()
        case .tutorial: return TutorialScreen().any()
        }
    }
}
```

Then present these covers like this:

```swift
context.present(AppCover.settings)
```

This makes it possible to create plain cover views or app- and view-specific enums and present all of them in the same way, using the same context.


## New fullScreenCover modifier

To present full screen covers, your context must be added to a view. We can do this by wrapping the native `fullScreenCover` modifier in a context-based modifier and provide it with the context state:

```swift
public extension View {
    
    func fullScreenCover(_ context: FullScreenCoverContext) -> some View {
        fullScreenCover(isPresented: context.isActiveBinding, content: context.content)
    }
}
```

If you use this modifier instead of the native `fullScreenCover` modifier, you can then use the context to present covers.


## Presenting a cover

With these new tools at our disposal, we can present covers in a much easier way. 

First, create a context property:

```swift
@StateObject private var cover = FullScreenCoverContext()
```

then add a `fullScreenCover` modifier to the view:

```swift
.fullScreenCover(cover)
```

You can now present any views or `FullScreenCoverProvider`s with the context:

```swift
// Present a view
cover.present(Text("Hello, I'm a custom cover."))
```

```swift
// Present a cover provider
cover.present(AppCover.settings)
```

You no longer need multiple `@State` properties for different covers or switch over an enum to determine which cover to show.


## Conclusion

`FullScreenCoverContext` can be used to present all different kind of views. It manages state for you and lets you use a more convenient modifier. All you have to do is provide it with the views to present.


## Source code

I have added these types to my [SwiftUIKit]({{page.lib}}) library. You can find the source code [here]({{page.source}}). Feel free to try it out and let me know what you think.