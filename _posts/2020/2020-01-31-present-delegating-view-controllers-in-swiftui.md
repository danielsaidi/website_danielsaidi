---
title:  "Present delegating view controllers in SwiftUI"
date:   2020-01-31 12:00:00 +0100
tags:   swiftui uikit vision
---

In SwiftUI, presenting `UIKit` view controllers is trivial, using `UIViewControllerRepresentable`. However, things become more complicated if a view controller communicates through delegation, since `SwiftUI` views are structs and therefore can't be delegates. In this post, we'll look at one way to solve this.

Since SwiftUI is still very young, there are many situations where you may have to use native UIKit view controllers, e.g. to compose e-mails, share data etc. You may also have many view controllers of your own, that you want to reuse in SwiftUI.


## View controllers without delegation 

Things are pretty straightforward when a view controller doesn't have to communicate back to the SwiftUI view. For instance, presenting a share sheet just requires you to create a `UIViewControllerRepresentable` that wraps the share sheet:

```swift
struct ShareSheet: UIViewControllerRepresentable {
    
    typealias Callback = (
        _ activityType: UIActivity.ActivityType?, 
        _ completed: Bool, 
        _ returnedItems: [Any]?, 
        _ error: Error?) -> Void
      
    let activityItems: [Any]
    let applicationActivities: [UIActivity]? = nil
    let excludedActivityTypes: [UIActivity.ActivityType]? = nil
    let callback: Callback? = nil
      
    func makeUIViewController(context: Context) -> UIActivityViewController {
        let controller = UIActivityViewController(
            activityItems: activityItems,
            applicationActivities: applicationActivities)
        controller.excludedActivityTypes = excludedActivityTypes
        controller.completionWithItemsHandler = callback
        return controller
    }
      
    func updateUIViewController(_ uiViewController: UIActivityViewController, context: Context) {
        // Nothing to see here, carry on
    }
}
```

You can then present this as a `sheet`, just as you would with any other SwiftUI view:

```swift
...
.sheet(isPresented: $isSheetActive) { ShareSheet(...) }
...
```

This will present the share sheet in a modal sheet and let you share any data you like.


## View controllers with delegation

Things become a little more complicated if a view controller has to communicate back to the SwiftUI view, using a delegate. Since SwiftUI views are structs, they can't be used as delegates, so we need something more.

One solution is to use a `coordinator`, which you can create and return as a nested class within your view:

```swift
struct MyView: View {
    
    class Coordinator {}
    
    func makeCoordinator() -> Coordinator {
        Coordinator()
    }
}
```

This coordinator can implement any delegates you need. If the view controller is used in multiple places in your app, you can reuse and compose coordinators together to avoid duplicating code.

Another approach is to have a specific `delegate` class for each representable view that must support delegation.

For instance, say that we want to present a Vision-based document camera and listen for scan events. `VNDocumentCameraViewController` communicates its events using a `VNDocumentCameraViewControllerDelegate`, so you must provide it with such a delegate to know what's going on.

First, let's wrap the `VNDocumentCameraViewController` in a `UIViewControllerRepresentable`:

```swift
struct DocumentCamera: UIViewControllerRepresentable {

    init(delegate: VNDocumentCameraViewControllerDelegate) {
        self.delegate = delegate
    }

    private let delegate: VNDocumentCameraViewControllerDelegate
    
    func makeUIViewController(context: Context) -> VNDocumentCameraViewController {
        let controller = VNDocumentCameraViewController()
        controller.delegate = delegate
        return controller
    }
    
    func updateUIViewController(_ uiViewController: VNDocumentCameraViewController, context: Context) {}
}
```

Pretty easy, right? The document camera can now be presented as long as it is provided with a delegate.

If your SwiftUI view has a coordinator that implements `VNDocumentCameraViewControllerDelegate`, you could just provide it when you create a document camera instance.

However, we can also implement a general companion delegate class, that just use action blocks to provide delegate events back to any custom actions:

```swift
extension DocumentCamera {
    
    class Delegate: NSObject, VNDocumentCameraViewControllerDelegate {
        
        init(
            didCancel: @escaping EmptyAction,
            didFail: @escaping ModelAction<Error>,
            didFinish: @escaping ModelAction<VNDocumentCameraScan>) {
            self.didCancel = didCancel
            self.didFail = didFail
            self.didFinish = didFinish
        }
        
        private let didCancel: EmptyAction
        private let didFail: ModelAction<Error>
        private let didFinish: ModelAction<VNDocumentCameraScan>
        
        func documentCameraViewControllerDidCancel(_ controller: VNDocumentCameraViewController) {
            didCancel()
        }
        
        func documentCameraViewController(_ controller: VNDocumentCameraViewController, didFailWithError error: Error) {
            didFail(error)
        }
        
        func documentCameraViewController(_ controller: VNDocumentCameraViewController, didFinishWith scan: VNDocumentCameraScan) {
            didFinish(scan)
        }
    }
}
```

Your SwiftUI views then don't have to fiddle around with delegates and coordinators. They can just present the document camera, using this delegate and provide it with their own actions, for instance:

```swift
...
.sheet(isPresented: $isSheetActive) {
    DocumentCamera(delegate: DocumentCamera.Delegate(
        didCancel: { ... }
        didFail: { error in ... }
        didFinish: { scan in ... }
    ))
}
...
```

I personally prefer this approach, since it makes the `DocumentCamera` class provide you with everything you need. All you have to do is to point it to the functions you want it to trigger.

Thanks for reading! Feel free to leave comments and feedback in the discussion section below.