---
title:  Building a document scanner in SwiftUI
date:   2022-05-09 06:00:00 +0000
tags:   swift swiftui

icon:   swiftui
tweet:  https://twitter.com/danielsaidi/status/1523549448458354688?s=20&t=bGaXlye_gZRmIMHzE1lWGw

swiftuikit: https://github.com/danielsaidi/SwiftUIKit
vision:     https://developer.apple.com/documentation/vision
---

In this post, let's take a quick look at how to we can extend SwiftUI with a document scanner, that uses the device camera to scan documents in iOS.

To achieve this, we'll use Apple's [Vision]({{page.vision}}) framework to create a `VNDocumentCameraViewController`, then embed it in SwiftUI and listen for any activities that take place in the controller.


## Creating the camera view

Let's start with creating a SwiftUI `DocumentCamera` view:

```swift
@available(iOS 13, *)
public struct DocumentCamera: UIViewControllerRepresentable {
    
    public init(
        cancelAction: @escaping CancelAction = {},
        resultAction: @escaping ResultAction) {
        self.cancelAction = cancelAction
        self.resultAction = resultAction
    }
    
    public typealias CameraResult = Result<VNDocumentCameraScan, Error>
    public typealias CancelAction = () -> Void
    public typealias ResultAction = (CameraResult) -> Void
    
    private let cancelAction: CancelAction
    private let resultAction: ResultAction
        
    public func makeUIViewController(context: Context) -> VNDocumentCameraViewController {
        let controller = VNDocumentCameraViewController()
        // controller.delegate = ???
        return controller
    }
    
    public func updateUIViewController(
        _ uiViewController: VNDocumentCameraViewController,
        context: Context) {}
}
```

The view implements `UIViewControllerRepresentable`, since it will wrap a document camera view controller. We also define a few typealiases to make the code easier to read.

We then create our camera view controller instance in `makeUIViewController`. However, since the controller communicates events through delegation, we need to find a way to listen for these events.


## Creating the coordinator

In SwiftUI, views are value types that can be recreated at any time. This makes them poor candidates for delegation. Most often, it's not even allowed to let a value type implement certain protocols.

To solve this, we can define a view `Coordinator` and use that as the delegate:

```swift
@available(iOS 13, *)
public extension DocumentCamera {
    
    class Coordinator: NSObject, VNDocumentCameraViewControllerDelegate {
        
        public init(
            cancelAction: @escaping DocumentCamera.CancelAction,
            resultAction: @escaping DocumentCamera.ResultAction) {
            self.cancelAction = cancelAction
            self.resultAction = resultAction
        }
        
        private let cancelAction: DocumentCamera.CancelAction
        private let resultAction: DocumentCamera.ResultAction

        public func documentCameraViewControllerDidCancel(
            _ controller: VNDocumentCameraViewController) {
            cancelAction()
        }
        
        public func documentCameraViewController(
            _ controller: VNDocumentCameraViewController,
            didFailWithError error: Error) {
            resultAction(.failure(error))
        }
        
        public func documentCameraViewController(
            _ controller: VNDocumentCameraViewController,
            didFinishWith scan: VNDocumentCameraScan) {
            resultAction(.success(scan))
        }
    }
}
```

This coordinator lets us inject functions that can be triggered by the coordinator as certain things happen in the camera controller. In this case, we listen for when the camera cancels, fails, and finishes.


## Using the coordinator

We can now update our `DocumentCamera` to use the coordinator, by rewriting it as such:

```swift
@available(iOS 13, *)
public struct DocumentCamera: UIViewControllerRepresentable {
    
    public init(
        cancelAction: @escaping CancelAction = {},
        resultAction: @escaping ResultAction) {
        self.cancelAction = cancelAction
        self.resultAction = resultAction
    }
    
    public typealias CameraResult = Result<VNDocumentCameraScan, Error>
    public typealias CancelAction = () -> Void
    public typealias ResultAction = (CameraResult) -> Void
    
    private let cancelAction: CancelAction
    private let resultAction: ResultAction
        
    public func makeCoordinator() -> Coordinator {
        Coordinator(
            cancelAction: cancelAction,
            resultAction: resultAction)
    }
    
    public func makeUIViewController(context: Context) -> VNDocumentCameraViewController {
        let controller = VNDocumentCameraViewController()
        controller.delegate = context.coordinator
        return controller
    }
    
    public func updateUIViewController(
        _ uiViewController: VNDocumentCameraViewController,
        context: Context) {}
}
```

`DocumentCamera` passes on the provided actions to the coordinator it creates in `makeCoordinator`, then sets up the context as the controller's delegate, to connect it with the provided actions.


## Conclusion

Wrapping UIKit views and view controllers in SwiftUI is very easy. Just make sure you understand when and how to use a coordinator for more complex tasks.

This code could also be adjusted to support async/await, but that's a topic for another post. Just let me know if you'd like to see such a post.

If you're interested in the source code, you can find it in my [SwiftUIKit]({{page.swiftuikit}}) library. Don't hesitate to comment or reach out with any thoughts you may have. I'd love to hear your thoughts on this.