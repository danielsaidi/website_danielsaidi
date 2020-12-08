---
title: Avoiding the keyboard in SwiftUI
date:  2020-06-08 23:00:00 +0100
tags:  swift swiftui
icon:  swiftuikit

lib:    https://github.com/danielsaidi/SwiftUIKit

so: https://stackoverflow.com/questions/56716311/how-to-show-complete-list-when-keyboard-is-showing-up-in-swiftui
---

**UPDATE! This is now a built-in feature in SwiftUI, so I have removed it from [SwiftUIKit]({{page.lib}}) and will not maintain it anymore. This post is only kept for reference.** 

In this short post, we'll look at how to make SwiftUI avoid the keyboard and slide the current view up to remain in focus when the keyboard is presented.

I tried out a couple of different solutions before stumbling over a nice solution by a guy named Bogdan Farca [at this Stack Overflow page]({{page.so}}).

He suggests a view modifier that listens to `UIResponder.keyboardWillShowNotification` and `UIResponder.keyboardFrameEndUserInfoKey` and adjusts the bottom padding of the view whenever the keyboard is presented and dismissed:

```swift
public struct KeyboardAvoiding: ViewModifier {
    
    @State var currentHeight: CGFloat = 0
    
    public func body(content: Content) -> some View {
        content
            .padding(.bottom, currentHeight)
            .edgesIgnoringSafeArea(currentHeight == 0 ? Edge.Set() : .bottom)
            .onAppear(perform: subscribeToKeyboardEvents)
    }
    
    private let keyboardWillOpen = NotificationCenter.default
        .publisher(for: UIResponder.keyboardWillShowNotification)
        .map { $0.userInfo![UIResponder.keyboardFrameEndUserInfoKey] as? CGRect ?? .zero }
        .map { $0.height }
    
    private let keyboardWillHide =  NotificationCenter.default
        .publisher(for: UIResponder.keyboardWillHideNotification)
        .map { _ in CGFloat.zero }
    
    private func subscribeToKeyboardEvents() {
        _ = Publishers.Merge(keyboardWillOpen, keyboardWillHide)
            .subscribe(on: RunLoop.main)
            .assign(to: \.currentHeight, on: self)
    }
}
```

To make it even easier to use, I have created a `keyboardAvoiding` view extension, that just applies this custom modifier:

```swift
public extension View {
    
    func keyboardAvoiding() -> some View {
        self.modifier(KeyboardAvoiding())
    }
}
```

Easy enough, right? With this modifier, you can just apply `.keyboardAvoiding()` to any view that you want to be able to automatically avoid the keyboard.