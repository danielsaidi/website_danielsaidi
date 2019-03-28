---
title:  "Swift extension constraints"
date:   2019-03-28 21:00:00 +0100
tags:   swift
---

I really love the Swift extension model. You have to use it with care, sure, but combined with careful system design, they give you a lot of power. In this short post, I discuss how to keep your extensions from being exposed everywhere.

A common use case for Swift extensions is to decorate foundation classes with additional functionality. For instance, adding this extension to your app would add a new `center` property to all instances of `CGRect`:

```swift
extension CGRect {
    
    var center: CGPoint {
        return CGPoint(x: midX, y: midY)
    }
}
```

This is a valid extension, since it fits the rect model. All rects could have this property without it feeling strange or wrong.

Sometimes, extensions are so useful that they make their way into the standard library. One example is `random(...)` for numerics, which was implemented over and over by developers until it made its way into the standard library.

Sometimes, however, extensions are valid for the class they extend, but not suitable to all instances of that class. For instance, consider this `UIView` extension:

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

Adding this extension to your app would let you to add a shake effect to all views in the app. Now...this is not such a good idea. Not only does it make it possible to shake views that perhaps should not be shakeable, but `startShaking()`, `stopShaking()` and `shake(_ numberOfTimes: Int)` would also show up in the auto complete window, as soon as you want to do anything with any view.

Adding extensions in this way will bloat your code base with functionality that may be invalid in many contexts. Just because a view *can* be shaked doesn't mean that it *should* be shaked.

Instead, you should add these kinds of exstensions in a constrained way that makes sense. Consider this alternative:

```swift
protocol Shakeable {}

extension Shakeable where Self: UIView {
    
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

By adding a `Shakeable` protocol as above, we can constrain the shake extensions to only apply to `UIView`s that implement `Shakeable`. This gives you total control over where the extension is applicable and avoids bloating all views in your application with this functionality.