---
title:  "Present delegating view controllers in SwiftUI"
date:   2020-01-31 12:00:00 +0100
tags:   swiftui uikit vision
---

Presenting `UIKit` view controllers in SwiftUI is trivial, but things become more complicated when a controller communicates back through delegation. Since `SwiftUI` views are structs, they therefore can't be delegates. In this post, we'll look at one way to solve this.


## UIViewControllerRepresentable

Since SwiftUI is still very young, there are many situations where you may have to use native UIKit view controllers, e.g. to compose e-mails, share data etc. You may also have your own view controllers that you want to reuse in SwiftUI.

Presenting a `UIKit` view controller in SwiftUI is trivial. For instance, presenting a share sheet just requires you to create a `UIViewControllerRepresentable` that wraps the sheet:

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

You can then present the share sheet as a SwiftUI `sheet`:

```swift
...
.sheet(isPresented: $isSheetActive) { ShareSheet(...) }
...
```

This will present the share sheet in a modal and let you share any data you like.


## View controllers with delegation

Things become a little more complicated if a view controller communicates back using a delegate. Since SwiftUI views are structs, they can't be used as delegates, so we need something more.

One solution is to use a `coordinator`, which you can create and return as a nested class within your view:

```swift
struct MyView: View {
    
    class Coordinator {}
    
    func makeCoordinator() -> Coordinator {
        Coordinator()
    }
}
```

This coordinator can implement any delegates you need. If the delegating view controller is used in many places, you can reuse and compose coordinators to avoid duplicating code.

Another approach is to have a specific `delegate` class for each view controller wrapper that supports delegation. For instance, say that we want to present a Vision-based document camera. `VNDocumentCameraViewController` communicates events using a `VNDocumentCameraViewControllerDelegate`, so you must provide it with such a delegate to know what's going on.

First, let's wrap the view controller in a `UIViewControllerRepresentable`:

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

The camera can now be presented as long as it is provided with a delegate. If your SwiftUI view has a coordinator that implements `VNDocumentCameraViewControllerDelegate`, you can just provide the coordinator when you create a `DocumentCamera`.

We can also implement a companion `DocumentCamera` delegate that use action blocks to communicate delegate events back to the view:

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

This approach lets you bind the delegate events to actions directly within the view. A view can now just present the document camera with this delegate and provide its own actions, like this:

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

I personally prefer this approach, since it makes the `DocumentCamera` class provide you with everything you need. All you have to do is to inject the functions you want it to trigger.

Thanks for reading! Feel free to leave comments and feedback in the discussion section below.