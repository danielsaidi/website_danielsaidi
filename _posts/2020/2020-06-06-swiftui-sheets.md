---
title:  "An easier way to manage sheets in SwiftUI"
date:   2020-06-06 20:00:00 +0100
tags:   swift swiftui
icon:   swiftuikit

lib:    https://github.com/danielsaidi/SwiftUIKit
source: https://github.com/danielsaidi/SwiftUIKit/tree/master/Sources/SwiftUIKit/Sheets
tests:  https://github.com/danielsaidi/SwiftUIKit/tree/master/Tests/SwiftUIKitTests/Sheets
---

In this post, we'll look at how to manage SwiftUI sheets in a more maintainable and flexible way. This will allow us to present different sheets in the same way and reduce state management.


## The basics

To present sheets in `SwiftUI`, you use the `sheet` modifier. It takes an `isPresented` binding and a view-producing `content` function:

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

Easy enough, right? Well, this basic example is, but I think it becomes tricky to manage as soon as you want to present multiple sheets from the same screen or reuse sheets across your app.

One problem is that you keep duplicating `isSheetActive` logic everywhere. You also have to duplicate the view producing logic whenever you present the same sheet from multiple views.

I have therefore tried to find a way to work with sheets in a more reusable way that requires less code while still being flexible to support both global and screen-specific sheets.


## SheetContext to the rescue!

After experimenting some with this, I came up with a way to let us reuse a bunch of this sheet-specific logic by gathering it in a `SheetContext` class:

```swift
public class SheetContext: ObservableObject {
    
    public init() {}
    
    @Published public var isActive = false
    
    public private(set) var sheetView: AnyView? {
        didSet { isActive = sheetView != nil }
    }
    
    public func present(_ sheet: SheetPresentable) {
        sheetView = sheet.sheet
    }
    
    public func present<Sheet: View>(_ sheet: Sheet) {
        sheetView = sheet.any()
    }
    
    public func sheet() -> AnyView {
        sheetView?.any() ?? EmptyView().any()
    }
}
```

The context can be used to present any `View` and anything that implements `SheetPresentable`, which can be implemented by anything that can provide a `sheet`:

```swift
public protocol SheetPresentable {
    
    var sheet: AnyView { get }
}
```

With this in place, you can now implement custom sheets in many different ways and present all of them the same way, using this new context.

To use this context within your views, just create a context instance and call any of its `present` functions. To bind it to a view, just use the `sheet` modifier as you normally do:
 
 ```swift
 .sheet(isPresented: $sheetContext.isActive, content: sheetContext.sheet)
 ```

You can define various `SheetPresentable` types in your app. For instance, if you have a set of sheets that should be presented from multiple views, you could create an `AppSheet` enum:

```swift
enum AppSheet: SheetPresentable {
    
    case settings, tutorial
    
    var sheet: AnyView {
        switch self {
        case .settings: return SettingsScreen().any()
        case .tutorial: return TutorialScreen().any()
        }
    }
}
```

Then present it as such:

```swift
sheetContext.present(AppSheet.settings)
```

You can also present any custom view with the same context:

```swift
sheetContext.present(Text("Hello! I'm a custom modal."))
```

If the settings screen has a bunch of sheets that should only be presented from settings, you could create a separate `SettingsSheet` enum and use it in the exact same way.

This means that `SheetContext` can be used to manage all different kind of sheets. It manages your state, while you just have to provide it with sheets you want to present.


## Source code

I have added these services to my [SwiftUIKit]({{page.lib}}) library. You can find the source code [here]({{page.source}}) and the unit tests [here]({{page.tests}}).