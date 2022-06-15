---
title:  SwiftUI 4 custom sheet sizes
date:   2022-06-15 01:00:00 +0000
tags:   swiftui open-source

icon:   swiftui
tweet:  https://twitter.com/danielsaidi/status/1537032522365812736?s=20&t=6cXx2n4Jpm6UDJR8dxPZNg

article: https://danielsaidi.com/blog/2022/06/10/swiftui-4-is-killing-my-open-source-projects
bottomsheet: https://github.com/danielsaidi/BottomSheet
---

WWDC'22 introduced a bunch of amazing additions to SwiftUI, many of which will render many 3rd party libraries obsolete. One such addition is SwiftUI's new support for custom sheet sizes. Let's take a look at this amazing new feature.


## Background

Until now, SwiftUI sheets have only supported a single, large size, where the parent view is pushed back and the sheet takes over most of the screen. When UIKit added support for custom sheet sizes last year, it wasn't added to SwiftUI.

As such, custom sheets has been a popular problem for the community to solve, which is why we have a bunch of open-source projects for this, my own [BottomSheet]({{page.bottomsheet}}) library included. Many of these libraries will however no longer be needed, as SwiftUI gets support for custom sheet sizes in SwiftUI 4 and iOS 16.


## Defining custom sheet sizes

In SwiftUI 4 and iOS 16, you'll be able to use the new `.presentationDetents` modifier to define a set of sizes that a view should support when it's being presented as a sheet:

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
                        .large])        // The previously default sheet size
                    .edgesIgnoringSafeArea(.all)
            }
    }
}
```

This is easy to use and works very well. The view will get a drag handle that the user can pull to move a sheet between its available sizes. The animation is very smooth and the feature satisfying to use.

Note that since the detents argument is a `Set`, adding multiple values of the same type will be ignored. If you for instance add two `.height` detents, only one is applied. Also, these custom sizes don't apply to iPad, where the sheet will still be presented as a centered modal window.

If you define a single sheet size, the drag handle will disappear and the sheet become non-dismissable. The sheet will bounce in a playful manner when you drag it, but it will not resize to other sizes and can't be dismissed by swiping down the sheet. This is perfect for onboarding guides and mandatory dialogs.


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

The sheet will still be draggable between sizes, but the handle will be hidden. I guess this could be useful if you want to create a custom handle that looks or behaves differently than the standard one.


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

The sheet will still be draggable between sizes, but you will not be able to dismiss it by swiping it down.


## Conclusion

The new sheet abilities in SwiftUI 4 and iOS 16 look great and open up for a bunch of new experiences, like onboarding guides, mandatory input modals etc. I can't wait to use them in my apps.

With all these great additions, there's no need for me to keep working on [BottomSheet]({{page.bottomsheet}}), which is why I've created an `xcode-14` branch in which I'll add information about that the library is being deprecated. I will merge this to the main branch when Xcode 14 is released, after which I'll archive the project. It will still be around, though, for people who use it.

I'm however torn on deprecating types in the library, since people may still have to use it for a few more years, until they can upgrade their apps to target iOS 16. Deprecating types in the library would mean that people could be left with annoying deprecation warnings that they can't act on. On the other hand, they will still be able to use older versions of the library, plus I will not create any new versions with these deprecations, so people can just stick to the last released version.

If you have any great insights regarding this, I'd love to hear them.