---
title: Closure-based gesture recognizers
date:  2018-01-30 09:00:00 +0100
tags:  swift gestures

image: /assets/blog/18/0130.png
image-show: 0

post:  https://medium.com/@sdrzn/adding-gesture-recognizers-with-closures-instead-of-selectors-9fb3e09a8f0b
---

In my [previous post](/blog/2018/01/19/ditching-rxswift), I wrote about how I don't like delegates and target/selectors and how I prefer closures. Let's see how to use closures in gesture recognizers to make things nicer.

In my apps, I work around using delegates and target/selectors by adding action properties to my views. However, this requires me to add these properties to every view or create sub classes, which isn't nice.

I have experimented with extensions, but since extensions can't store
data, I use protocols that ensure that I have closure storage properties, then extend the protocols with closure-based gesture functions. This requires me to implement the protocol for each view, though.

While neither approach is perfect, I still think that they are better than using delegates and selectors. I'd very much prefer Apple to add closure-based gestures.

Today, I found [this article]({{page.post}}) that describes how to use associated objects to let an extension store properties. With this, we can implement `UIView` extensions that add closure-based gesture recognizers to our views:

```swift
public extension UIView {
    
    public func addLongPressGestureRecognizer(action: (() -> Void)?) {
        longPressAction = action
        isUserInteractionEnabled = true
        let selector = #selector(handleLongPress)
        let recognizer = UILongPressGestureRecognizer(target: self, action: selector)
        addGestureRecognizer(recognizer)
    }
}

fileprivate extension UIView {
    
    typealias Action = (() -> Void)
    
    struct Key { static var id = "longPressAction" }
    
    var longPressAction: Action? {
        get {
            return objc_getAssociatedObject(self, &Key.id) as? Action
        }
        set {
            guard let value = newValue else { return }
            let policy = objc_AssociationPolicy.OBJC_ASSOCIATION_RETAIN
            objc_setAssociatedObject(self, &Key.id, value, policy)
        }
    }
    
    @objc func handleLongPress(sender: UILongPressGestureRecognizer) {
        guard sender.state == .began else { return }
        longPressAction?()
    }
}
```

```swift
public extension UIView {
    
    public func addTapGestureRecognizer(action: (() -> Void)?) {
        tapAction = action
        isUserInteractionEnabled = true
        let selector = #selector(handleTap)
        let recognizer = UITapGestureRecognizer(target: self, action: selector)
        addGestureRecognizer(recognizer)
    }
}

fileprivate extension UIView {
    
    typealias Action = (() -> Void)
    
    struct Key { static var id = "tapAction" }
    
    var tapAction: Action? {
        get {
            return objc_getAssociatedObject(self, &Key.id) as? Action
        }
        set {
            guard let value = newValue else { return }
            let policy = objc_AssociationPolicy.OBJC_ASSOCIATION_RETAIN
            objc_setAssociatedObject(self, &Key.id, value, policy)
        }
    }

    @objc func handleTap(sender: UITapGestureRecognizer) {
        tapAction?()
    }
}
```

If this approach doesn't turn out to use private api:s not approved by Apple, I
believe that I've now found a perfect approach to not having to use delegates or
selectors ever again.