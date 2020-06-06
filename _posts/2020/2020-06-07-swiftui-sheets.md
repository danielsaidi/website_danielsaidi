---
title:  "An easier way to manage sheets in SwiftUI"
date:   2020-06-06 10:00:00 +0100
tags:   swift swiftui
icon:   swiftuikit

lib:    https://github.com/danielsaidi/SwiftUIKit
source: https://github.com/danielsaidi/SwiftUIKit/tree/master/Sources/SwiftUIKit/Sheets
tests:  https://github.com/danielsaidi/SwiftUIKit/tree/master/Tests/SwiftUIKitTests/Sheets
---

In this post, we'll look at how to manage SwiftUI modals sheets in a more maintainable and flexible way. This will allow us to use global and view-specific sheets in the same way.


## The basics

To present sheets in `SwiftUI`, you use the `sheet` modifier, which takes an `isPresented` binding and a view-producing `content` function:

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

Easy enough, right? Well, this basic example is, but I think it becomes really tricky to manage as soon as you want to present multiple sheets from the same screen or reuse sheets across your application.

One problem is that you keep duplicating the `isSheetActive` logic everywhere. You also have to duplicate the view producing logic whenever you present the same sheet from multiple views.

I have therefore tried to find a way to work with sheets in a more reusable way that requires less code while still being flexible to support both global and screen-specific sheets.


## Sheet Context

After experimenting some with this, I came up with a way to let us reuse a bunch of this sheet-specific logic by gathering it in a `SheetContext` class:

```swift
public class SheetContext: ObservableObject {
    
    public init() {}
    
    @Published public var isActive = false
    
    public var sheet: SheetPresentable? {
        didSet { isActive = sheet != nil }
    }
    
    public func view() -> AnyView {
        if let view = sheet?.sheetView { return view.any() }
        return EmptyView().any()
    }
    
    public func present(_ sheet: SheetPresentable) {
        self.sheet = sheet
    }
}
```
 
The `SheetPresentable` protocol can be implemented by any types that can provide a view to this context. It could for instance be an app-wide or view-specific enum...or both. 

```swift
public protocol SheetPresentable {
    
    var sheetView: AnyView { get }
}
```

The context can now be used to manage action sheets and let us present a wide range of sheets with a single modifier, in fact the same modifier as we already use.
 
To use this context within your view, create an instance of the context and set its `sheet` property whenever you want to show a sheet. You can also use `present(_ sheet: SheetPresentable)` which just sets the sheet property.
 
To bind the context's sheet to your view, you can just use the `sheet` modifier as you would do with any other sheet:
 
 ```swift
 .sheet(isPresented: $sheetContext.isActive, content: sheetContext.view)
 ```

You can now define various `SheetPresentable` types in your app. For instance, if you have a set of sheets that should be presented from multiple views, you could create an `AppSheet` enum:

```swift
enum AppSheet: SheetPresentable {
    
    case settings, tutorial
    
    var sheetView: AnyView {
        switch self {
        case .settings: return SettingsScreen().any()
        case .tutorial: return TutorialScreen().any()
        }
    }
}
```

Then present it as such:

```swift
sheetContext.sheet = AppSheet.settings
```

or

```swift
sheetContext.present(AppSheet.settings)
```

If the settings screen has a bunch of sheets that should only be presented from settings, you could create a separate `SettingsSheet` enum and use it in the exact same way.


## Source code

I have added these services to my [SwiftKit]({{page.lib}}) library. You can find the source code [here]({{page.source}}) and the unit tests [here]({{page.tests}}).