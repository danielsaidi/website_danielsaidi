---
title: An easier way to manage sheets in SwiftUI
date:  2020-06-06 20:00:00 +0100
tags:  swiftui sheet
icon:  swiftui

github: https://github.com/danielsaidi/SwiftUIKit
source: https://github.com/danielsaidi/SwiftUIKit/tree/master/Sources/SwiftUIKit/Presentation/Sheet
---

In this post, we'll look at an easier way to manage sheets in SwiftUI, in a way that lets us reuse functionality, reduce state management and present many different sheets with the same modifier.


## TLDR;

If you find this post too long, I have added this to my [SwiftUIKit]({{page.github}}) library. You can find the source code [here]({{page.source}}).  If you decide to try it out, I'd be very interested in hearing what you think.


## The basics

To present sheets in SwiftUI, you use the `sheet` modifier that takes an `isPresented` binding and a `content` function (since this was written, more options have been added):

```swift
struct MyView: View {
    
    @State private var isSheetActive = false
    
    var body: some View {
        Button("Show sheet", action: showSheet)
            .sheet(isPresented: $isSheetActive, content: sheetContent)
    }
    
    func sheetContent() -> some View {
        Text("Hello, world!")
    }

    func showSheet() {
        isSheetActive = true
    }
}
```

This can become tricky when you have to present multiple sheets from the same screen or reuse sheets across an app. You may end up duplicating code, state, view builders etc.

I have therefore tried to find a way to handle sheets in a more reusable way, that requires less code and less state, while still being flexible to support both global and screen-specific sheets.

It all begins with a very simple state manager that I call `SheetContext`.


## Sheet context

Instead of managing state in every view that should present sheets, I use a `SheetContext`:

```swift
public class SheetContext: PresentationContext<AnyView> {
    
    public override func content() -> AnyView {
        contentView ?? EmptyView().any()
    }
    
    public func present<Sheet: View>(_ sheet: Sheet) {
        present(sheet.any())
    }
    
    public func present(_ provider: SheetProvider) {
        contentView = provider.sheet
    }
}
```

As you can see, it contains code for presenting a `Sheet` (which is just a view) or a `SheetProvider`. We'll come back to the provider shortly.

You may also notice that it inherits something called `PresentationContext`. Let's take a closer look at this base class.


## Presentation context

Since I find that this problem is also true for alerts, modals etc. I have a `PresentationContext`, which is a small `ObservableObject` base class with an `isActive` binding and a generic `content` view:

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

By calling the sheet-specific functions in `SheetContext`, the context state is properly updated.


## Sheet provider

As we saw earlier, `SheetContext` can present `Sheet` views and `SheetProvider`s. `Sheet` is just a view, while `SheetProvider` is a protocol for anything that can provide sheet views:

```swift
public protocol SheetProvider {
    
    var sheet: AnyView { get }
}
```

For instance, you can have an enum that represents various sheets that your app supports:

```swift
enum AppSheet: SheetProvider {
    
    case settings
    case tutorial
    
    var sheet: AnyView {
        switch self {
        case .settings: return SettingsScreen().any()
        case .tutorial: return TutorialScreen().any()
        }
    }
}
```

Then present these sheets like this:

```swift
context.present(AppSheet.settings)
```

This makes it possible to create plain sheet views or app- and view-specific enums and present all of them in the same way, using the same context.


## New sheet modifier

To present sheet, your context must be added to a view. We can do this by wrapping the native `sheet` modifier in a context-based modifier and provide it with the context state:

```swift
public extension View {
    
    func sheet(_ context: SheetContext) -> some View {
        sheet(isPresented: context.isActiveBinding, content: context.content)
    }
}
```

If you use this modifier instead of the native `sheet` modifier, you can use the context to present sheets.


## Presenting a sheet

With these new tools at our disposal, we can present sheets in a much easier way. 

First, create a context property:

```swift
@StateObject private var sheet = SheetContext()
```

then add a `sheet` modifier to the view:

```swift
.sheet(sheet)
```

You can now present any views or `SheetProvider`s with the context:

```swift
// Present a view
sheet.present(Text("Hello, I'm a custom sheet."))
```

```swift
// Present a sheet provider
sheet.present(AppSheet.settings)
```

You no longer need multiple `@State` properties for different sheets or switch over an enum to determine which sheet to show.


## Conclusion

`SheetContext` can be used to present all different kind of views. It manages all state for you and lets you use a more convenient modifier. All you have to do is provide it with the views to present.


## Source code

I have added this to my [SwiftUIKit]({{page.github}}) library. You can find the source code [here]({{page.source}}). Feel free to try it out and let me know what you think.