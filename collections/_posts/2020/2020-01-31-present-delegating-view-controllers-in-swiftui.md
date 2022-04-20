---
title: How to use view controller delegation in SwiftUI
date:  2020-01-31 12:00:00 +0100
tags:  swiftui uikit
icon:  swift
---

Presenting UIKit view controllers in SwiftUI is simple, but things become more complicated when they communicate through delegation. In this post, we'll look at a way to solve this.


## UIViewControllerRepresentable

Since SwiftUI is still young, there are many situations where you may have to use native UIKit views or view controllers, e.g. to compose e-mails, share data etc. You may also have your own view controllers that you want to reuse in SwiftUI.

Presenting a UIKit view controller in SwiftUI is trivial. For instance, presenting a share sheet just requires you to create a `UIViewControllerRepresentable` that wraps the sheet:

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
.sheet(isPresented: $isSheetActive) { ShareSheet(someData) }
...
```

This will present the share sheet in a modal and let you share any data you like. You can also specify application activities, excluded activity types and a callback, although it's not needed.


## View controllers with delegation

Things become a little more complicated if a view controller communicates back using a delegate. Since SwiftUI views are structs, they can't be used as delegates. We need something more.

One solution is to use a `coordinator`, which you can create and return as a nested class in your view:

```swift
struct MyView: View {
    
    class Coordinator {}
    
    func makeCoordinator() -> Coordinator {
        Coordinator()
    }
}
```

Although the view is a struct and can be recreated whenever SwiftUI tells it to, the coordinator will be kept as a single instance, and can therefore be used as a delegate. 

If the delegating view controller is used in many places, you can reuse and compose coordinators to avoid duplicating code. This is however unusual.

Another approach is to have a delegate class for each view controller wrapper that supports delegation. 

Say that we want to use a Vision-based document camera. The `VNDocumentCameraViewController` communicates events using a `VNDocumentCameraViewControllerDelegate`, so you must provide it with such a delegate to know what's going on.

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

The camera can now be presented as long as it's given a delegate. If your view has a coordinator that implements `VNDocumentCameraViewControllerDelegate`, you can just provide that coordinator when you create a `DocumentCamera`.

We can also implement a `DocumentCamera` delegate that use action blocks to communicate delegate events back to the view:

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