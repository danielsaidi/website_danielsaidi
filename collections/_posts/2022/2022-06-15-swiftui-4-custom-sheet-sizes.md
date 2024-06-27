---
title:  Custom sheet sizes in SwiftUI
date:   2022-06-15 01:00:00 +0000
tags:   swiftui sheet presentation-detents

icon:   swiftui
tweet:  https://twitter.com/danielsaidi/status/1537032522365812736?s=20&t=6cXx2n4Jpm6UDJR8dxPZNg

article: https://danielsaidi.com/blog/2022/06/21/undimmed-presentation-detents-in-swiftui
bottomsheet: https://github.com/danielsaidi/BottomSheet
---

WWDC'22 introduced many amazing additions to SwiftUI, of which many will render many 3rd party libraries obsolete. One such is SwiftUI's new support for custom sheet sizes.


## Background

Until now, SwiftUI sheets have only supported a single, large size, where the parent view is pushed back and the sheet takes over most of the screen.

When UIKit added support for custom sheet sizes last year, it wasn't added to SwiftUI. As such, custom sheets has been a popular problem for the community to solve.

We have a bunch of open-source projects for this, my own [BottomSheet]({{page.bottomsheet}}) included. Many of these will however no longer be needed when SwiftUI gets support for custom sheet sizes.


## Defining custom sheet sizes

In SwiftUI 4 and iOS 16, you'll be able to use the new `.presentationDetents` view modifier to define a set of sheet sizes that a view should support:

```swift
struct MyView: View {
        
    @State private var isPresented = true

    var body: some View {
        Color.green.edgesIgnoringSafeArea(.all)
            .sheet(isPresented: $isPresented) {
                Color.red
                    .presentationDetents([
                        .height(100),   // 100 points
                        .fraction(0.2), // 20% of the available height
                        .medium,        // Takes up about half the screen
                        .large]         // The previous default size
                    )
                    .edgesIgnoringSafeArea(.all)
            }
    }
}
```

This is easy to use and works very well. The view gets a drag handle that move the sheet between its available sizes. The animation is very smooth and the feature satisfying to use.

If you define a single size, the handle disappears and the sheet become non-dismissable. The sheet will bounce in a delightful way when you drag it, but will not resize to other sizes.

Single-sized sheets also can't be dismissed by swiping down the sheet. This is perfect for onboarding guides and mandatory dialogs.

Note that this don't apply to iPad, where the sheet is still presented as a centered window.


## Hiding the drag handle

If you want to support multiple sheet sizes, but still want to hide the drag handle, you can use the new `.presentationDragIndicator` modifier:

```swift
struct MyView: View {
        
    @State private var isPresented = true

    var body: some View {
        Color.green.edgesIgnoringSafeArea(.all)
            .sheet(isPresented: $isPresented) {
                Color.red
                    .presentationDetents([.height(100), .fraction(20), .medium, .large])
                    .presentationDragIndicator(.hidden)  // <-- 
                    .edgesIgnoringSafeArea(.all)
            }
    }
}
```

The sheet will still be resizeable, but the handle will be hidden. I guess this can be useful if you want to create a custom handle that looks different than the standard one.


## Undimming the underlying view

In UIKit, you can define the largest undimmed presentation detent. This can be used to create static sheets that don't dim the underlying view, such as in Apple Maps.

In SwiftUI, this is not yet possible with the native api:s. I have therefore written [an article]({{page.article}}) that describes how you can do this in SwiftUI as well.


## Disabling interactive dismissal

If you want to support multiple sheet sizes, but still want the sheet to be non-dismissable, you can use the `.interactiveDismissDisabled` modifier:

```swift
struct MyView: View {
        
    @State private var isPresented = true

    var body: some View {
        Color.green.edgesIgnoringSafeArea(.all)
            .sheet(isPresented: $isPresented) {
                Color.red
                    .presentationDetents([.height(100), .fraction(20), .medium, .large])
                    .interactiveDismissDisabled()  // <-- 
                    .edgesIgnoringSafeArea(.all)
            }
    }
}
```

The sheet will still be draggable between sizes, but will not dismiss when you swipe down.


## Conclusion

The new sheet abilities in SwiftUI 4 & iOS 16 look great and open up for new experiences, like onboarding guides, mandatory input modals etc. I can't wait to use it in my apps.

With these great additions, there's no need for me to keep working on [BottomSheet]({{page.bottomsheet}}). I will archive it and keep it around for a few years, then remove it.