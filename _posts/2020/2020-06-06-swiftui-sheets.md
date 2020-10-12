---
title: An easier way to manage sheets in SwiftUI
date:  2020-06-06 20:00:00 +0100
tags:  swift swiftui
icon:  swiftuikit

lib:    https://github.com/danielsaidi/SwiftUIKit
source: https://github.com/danielsaidi/SwiftUIKit/tree/master/Sources/SwiftUIKit/Sheets
presentation-context: https://github.com/danielsaidi/SwiftUIKit/tree/master/Sources/SwiftUIKit/Contexts/PresentationContext.swift
---

In this post, we'll look at an easier way to manage sheets in `SwiftUI`, that lets us reuse functionality, reduce state management and present many different sheets in the same way.


## The basics

To present sheets in a `SwiftUI` app, you would normally use a `sheet` modifier that takes an `isPresented` binding and a view-producing `content` function:

```swift
struct MyView: View {
    
    @State private var isSheetActive = false
    private let sheetView = "Hello, world!"
    
    var body: some View {
        Button("Show sheet", action: showSheet)
            .sheet(isPresented: $isSheetActive, content: { sheetView })
        }
    }
}
```

This example is simple, but I think it becomes tricky to manage sheets as soon as you want to present multiple sheets from the same screen or reuse sheets across an app. You may end up duplicating `isSheetActive` logic as well as the view builder logic.

I therefore tried to find a way to work with sheets in a more reusable way, that requires less code and less state while still being flexible to support both global and screen-specific sheets.


## SheetContext

After pondering this problem for a while, I think I have come up with a solution that simplies working with `SwiftUI` sheets. It all starts with an observable class called `SheetContext`:

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

As you can see, `SheetContext` basically only contains code for presenting a `Sheet` or a `SheetProvider`. We'll come back to these concepts shortly.

You may also notice that it inherits something called `PresentationContext`. Let's take a closer look at this base class.


## PresentationContext

Since I find that the sheet problem also is true for alerts, context menus etc., I have created a `PresentationContext` on which I base other similar solutions to the same kind of problem.

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

By calling the more specific functions in `SheetContext`, the `PresentationContext` state is properly updated.


## SheetProvider

As we saw earlier, `SheetContext` can present a `Sheet` and a `SheetProvider`. `Sheet` is just a view, while `SheetProvider` is a protocol for anything that can provide a sheet view:

```swift
public protocol SheetProvider {
    
    var sheet: AnyView { get }
}
```

With this in place, you can now implement custom sheets in many different ways and present all of them the same way, using this new context.

For instance, you can have an enum that represents the various sheets your app supports:

```swift
enum AppSheet: SheetProvider {
    
    case settings, tutorial
    
    var sheet: AnyView {
        switch self {
        case .settings: return SettingsScreen().any()
        case .tutorial: return TutorialScreen().any()
        }
    }
}
```


## New sheet modifier

Since `SheetContext` handles all state for us, we can now implement a new `sheet` modifier for presenting sheets:

```swift
public extension View {
    
    func sheet(context: SheetContext) -> some View {
        sheet(isPresented: context.isActiveBinding, content: context.content)
    }
}
```

The new modifier just provides the standard `sheet` modifier with the context's state, which makes things easier for you.


## Presenting a sheet

With these new tools at our disposal, we can present sheets in a much easier way.

First, create a `@State` property in any view that should be able to present sheets:

```swift
@State private var sheetContext = SheetContext()
```

then add a `SheetContext` specific view modifier to the view:

```swift
.sheet(context: sheetContext)
```

You can now present any view and `SheetProvider` as a sheet, for instance the `AppSheet`:

```swift
sheetContext.present(AppSheet.settings)
```

You can also present any custom view in the same way, using the same context:

```swift
sheetContext.present(Text("Hello! I'm a custom modal."))
```


## Conclusion

As you can see, `SheetContext` can be used to manage all different kind of sheets and views. It manages all state for you and lets you use a more convenient sheet modifier. All you have to do is provide it with the sheets you want to present.


## Source code

I have added these components to my [SwiftUIKit]({{page.lib}}) library. You can find the source code [here]({{page.source}}).