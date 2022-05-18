---
title:  Presenting a SwiftUI view from DocumentGroup
date:   2022-05-16 10:00:00 +0000
tags:   swiftui

icon:   swiftui document-group

tweet:  https://twitter.com/danielsaidi/status/1526212709338988545?s=20&t=c-v01r2HDFnL3BfXzlKa5w
---

The SwiftUI `DocumentGroup` makes it super-easy to create document-based apps. However, the api:s are currently very limited, which means that even the most basic things are hard to achieve. In this post, let's take a quick look at how we can present custom SwiftUI views as modals from a `DocumentGroup`.


## Background

When you setup a DocumentGroup-based app for the first time, you may be surprised how easy it is to get started. You just have to provide a document view that should be used to present documents, and you're good to go. The view can be customized just like any other SwiftUI view, and you can use things like toolbars, navigation views, sheets etc. to create a powerful document viewer.

However, while the `DocumentGroup` is easy to use, it's also pretty limited in functionality. There is very little you can customize with the public api:s, which means that you will have to do some UIKit/AppKit hacking to get even the most basic thing to work.

For instance, say that you want to present a sheet from a document group, e.g. to show an initial app onboarding when the user first opens the app. This is currently not possible with the public api:s, since `DocumentGroup` is not a view. You only get a view once you open a document, which may be too late.


## Solution

We can work around these limitations by dropping down to UIKit and AppKit and use the more extensive api:s that these frameworks provide.

Let's build a UIKit-specific workaround by first defining a `DocumentGroupSheet` protocol that can be implemented by any `SwiftUI` view:

```swift
public protocol DocumentGroupSheet: View {}
```

Let's also define a specific error for the things that can go wrong when doing this workaround:

```swift
public enum DocumentGroupSheetError: Error {
    
    case noParentWindow
}
```

We can now extend `DocumentGroupSheet` with a function that makes it present itself from any document group:

```swift
public extension DocumentGroupSheet {
    
    func presentFromDocumentGroup() throws {
        let window = UIApplication.shared.activeKeyWindows.first
        let parent = window?.rootViewController
        guard let parent = parent else { throw DocumentGroupSheetError.noParentWindow }
        let sheet = UIHostingController(rootView: body)
        parent.present(sheet, animated: true, completion: nil)
    }
}
```

As you can see, the workaround is pretty basic, and doesn't even require the active view controller to be a document group. We first try to get the active root view controller, and throw an error if none exists. We then create a hosting controller with the view body and present it as a sheet.

With this extension, views can either present themselves by calling the present function from within the view. We can also let e.g. the SwiftUI app type create a view instance and present it whenever we want. 


## Conclusion

The SwiftUI `DocumentGroup` is currently limited, but we can easily extend the available functionality by dropping down to UIKit and AppKit. This will hopefully not be needed in future versions of SwiftUI.

You can find the source code in this post in my [SwiftUIKit]({{page.swiftuikit}}) library. Don't hesitate to comment or reach out with any thoughts you may have.