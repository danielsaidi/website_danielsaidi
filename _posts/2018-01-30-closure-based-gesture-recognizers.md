---
title:  Closure-based gesture recognizers
date:   2018-01-30 09:00:00 +0100
tags:	swift
---


In my [previous post](/blog/2018/01/19/ditching-rxswift), I wrote about how I do
not like iOS delegates and target/selectors and how I prefer to use closures.

In my various apps, I have worked around using delegates and target/selectors by
adding action properties to my various views. However, this approach requires me
to add these properties to every view or create sub classes. Both approaches are
horrible and not very scalable.

I have also experimented with using extensions, but since extensions can't store
any properties, I have defined protocols that ensure that I have closure storage
properties, then extended the protocols with closure-based gesture functions. It
still requires me to implement the protocol for each view, though.

So while neither approach is perfect (they are still better than using delegates
and selectors, though) and I'd prefer it if Apple did add closure-based gestures,
I have used these approaches in a mix and match fashion, while still dreaming of
a perfect setup.

Then...

Today, I found [this blog post](https://medium.com/@sdrzn/adding-gesture-recognizers-with-closures-instead-of-selectors-9fb3e09a8f0b)
where Saoud describes how you can use associated objects to let extensions store
custom backing properties. Using this approach, we can now implement two `UIView`
extensions that lets us add closure-based gesture recognizers to our views:

```swift
import UIKit

public extension UIView {
    
    public func addLongPressGestureRecognizer(action: (() -> Void)?) {
        isUserInteractionEnabled = true
        longPressGestureRecognizerAction = action
        let selector = #selector(handleLongPressGesture)
        let recognizer = UILongPressGestureRecognizer(target: self, action: selector)
        addGestureRecognizer(recognizer)
    }
}

fileprivate extension UIView {
    
    typealias Action = (() -> Void)
    
    struct Key {
        static var gesture = "UIView_longPressGesture_action"
    }
    
    var longPressGestureRecognizerAction: Action? {
        get {
            return objc_getAssociatedObject(self, &Key.gesture) as? Action
        }
        set {
            guard let value = newValue else { return }
            let policy = objc_AssociationPolicy.OBJC_ASSOCIATION_RETAIN
            objc_setAssociatedObject(self, &Key.gesture, value, policy)
        }
    }
    
    @objc func handleLongPressGesture(sender: UITapGestureRecognizer) {
        longPressGestureRecognizerAction?()
    }
}
```

```swift
import UIKit

public extension UIView {
    
    public func addTapGestureRecognizer(action: (() -> Void)?) {
        isUserInteractionEnabled = true
        tapGestureRecognizerAction = action
        let selector = #selector(handleTapGesture)
        let recognizer = UITapGestureRecognizer(target: self, action: selector)
        addGestureRecognizer(recognizer)
    }
}

fileprivate extension UIView {
    
    typealias Action = (() -> Void)
    
    struct Key {
        static var gesture = "UIView_tapGesture_action"
    }
    
    var tapGestureRecognizerAction: Action? {
        get {
            return objc_getAssociatedObject(self, &Key.gesture) as? Action
        }
        set {
            guard let value = newValue else { return }
            let policy = objc_AssociationPolicy.OBJC_ASSOCIATION_RETAIN
            objc_setAssociatedObject(self, &Key.gesture, value, policy)
        }
    }

    @objc func handleTapGesture(sender: UITapGestureRecognizer) {
        tapGestureRecognizerAction?()
    }
}
```

If this approach does not turn out to use private api:s not approved by Apple, I
believe that I've now found a perfect approach to not having to use delegates or
selectors ever again.

Thanks Saoud!

Daniel
