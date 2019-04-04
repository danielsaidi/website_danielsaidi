---
title:  "Constrain Swift extensions with protocols"
date:   2019-03-28 21:00:00 +0100
tags:   swift
icon:   swift
---

I really love the Swift type system and its extension model. You have to use it with care, but combined with careful system design, they give you a lot of power. In this short post, I discuss how to keep your extensions from being exposed everywhere.

A common use case for Swift extensions is to decorate foundation classes with additional functionality. For instance, adding this extension to your app would add a new `center` property to all instances of `CGRect`:

```swift
extension CGRect {
    
    var center: CGPoint {
        return CGPoint(x: midX, y: midY)
    }
}
```

This is a valid extension, since it fits the rect model. All rects could have this property without it feeling strange or being wrong. Sometimes, extensions are even so "correct" and commonly needed, that they make their way into the standard library. One example is `random(...)`, which was added to the standard library in Swift 4.2.

Sometimes, however, extensions are applicable, but not suitable, for all instances of the class they extend. For instance, consider this `UIView` extension:

```swift
extension UIView {
    
    private var key: String { return "shake" }
    
    func startShaking() {
        wobble(Int.max)
    }
    
    func stopShaking() {
        layer.removeAnimation(forKey: key)
    }
    
    func shake(_ numberOfTimes: Int) {
        let animation = CABasicAnimation(keyPath: "transform.rotation")
        animation.toValue = -Double.pi/128
        animation.fromValue = Double.pi/128
        animation.duration = 0.2
        animation.repeatCount = Float(numberOfTimes)
        animation.autoreverses = true
        layer.add(animation, forKey: key)
    }
}
```

Adding this extension to your app would make it possible to add a shake effect to all views in your app. While this is perfectly valid, it's probably not a good idea. Not only does it make it possible to shake views that should not be shaken, but `startShaking()`, `stopShaking()` and `shake(_ numberOfTimes: Int)` would also show up as auto complete suggestions for all views. This means that adding many extensions to general classes, will bloat your code base with functionality that may be invalid in many contexts.

To avoid this problem, you should restrict the scope of your extensions to ensure that they can only be used intentionally. One way to do this is to create protocols to which you constrain the extensions. For instance, by adding a `Shakeable` protocol, we can stop all views from becoming shakeable when you add the extension above to your app:

```swift
protocol Shakeable {}

extension Shakeable where Self: UIView {
    
    private var key: String { return "shake" }
    
    func startShaking() {
        shake(Int.max)
    }
    
    func stopShaking() {
        layer.removeAnimation(forKey: key)
    }
    
    func shake(_ numberOfTimes: Int) {
        let animation = CABasicAnimation(keyPath: "transform.rotation")
        animation.toValue = -Double.pi/128
        animation.fromValue = Double.pi/128
        animation.duration = 0.2
        animation.repeatCount = Float(numberOfTimes)
        animation.autoreverses = true
        layer.add(animation, forKey: key)
    }
}
```

By adding a `Shakeable` protocol as above, we can constrain the shake extensions to only apply to `UIView`s that implement `Shakeable`. This gives you total control over where the extension can be used.