---
title: Using structs like enums in Swift
date:  2019-01-30 21:00:00 +0100
tags:  swift
icon:  swift

redirect_from: /blog/2019/01/30/using-structs-like-enums

swift-docs: https://docs.swift.org        
enums:   https://docs.swift.org/swift-book/LanguageGuide/Enumerations.html
structs: https://docs.swift.org/swift-book/LanguageGuide/ClassesAndStructures.html
---

Swift `enum` and `struct` are two powerful tools. In this post, I'll discuss how you typically use them, and how you can use structs like enums when you need more flexibility.


## Enums

[Swift Docs]({{page.swift-docs}}) describes enums like this: 

> "An enumeration defines a common type for a group of related values and enables you to work with those values in a type-safe way within your code."

In short, if you have a fixed set of optional, related values, enums are great. Swift enums are very powerful and support associated values, extensions, nesting etc.

For instance, the following enum specifies food options on a menu:

```swift
enum Food {
    case 
    hamburger, 
    cheeseBurger, 
    veggieBurger,
    chickenNuggets(pcs: Int)
}
```

I will not go into further detail on enums. If you want to learn more, [see the official docs]({{page.enums}}).


## Structs

[Swift Docs]({{page.swift-docs}}) describes structs like this: 

> "Structs are general-purpose, flexible constructs that become the building blocks of your program’s code. You define properties and methods to add functionality to your structures and classes using the same syntax you use to define constants, variables, and functions."

In short, structs are great value types that give you better data integrity than you get with classes. It took me a while to start using structs, but now I can't live without them.

For instance, this struct defines a food order, with an immutable order number and items:

```swift
struct FoodOrder: Codable {

    let orderNumber: Int
    let items: [Food]
}
```  

I will not go into further detail on structs. If you want to learn more, [see the official docs]({{page.structs}}).


## Using structs instead of enums

Given the information above, consider that we want to specify a list of shadow styles, as well as an extension that can be used to apply a shadow style to a UIKit view.

If you'd asked me a few years ago, I would have implemented this using an enum:

```swift
enum ShadowStyle {
    
    case
    small,
    medium,
    large
}
```

We can then attach properties to this enum:


```swift
extension ShadowStyle {
    
    var alpha: Float {
        switch self {
        case .small: 0.2
        case .medium: 0.26
        case .large: 0.6
        }
    }
    
    var blur: CGFloat {
        switch self {
        case .small: 8
        case .medium: 15
        case .large: 40
        }
    }
    
    var color: UIColor { .black }
    
    var spread: CGFloat { 0 }
    
    var x: CGFloat { 0 }
    
    var y: CGFloat {
        switch self {
        case .small: 4
        case .medium: 5
        case .large: 10
        }
    }
}
```

I would then have created a `UIView` extension that can be used to apply a shadow style:

```swift
extension UIView {
    
    func applyShadow(_ shadow: ShadowStyle) {
        layer.applyShadow(
            color: shadow.color,
            alpha: shadow.alpha,
            x: shadow.x,
            y: shadow.y,
            blur: shadow.blur,
            spread: shadow.spread
        )
    }
}

private extension CALayer {
    
    func applyShadow(
        color: UIColor = .black,
        alpha: Float = 0.5,
        x: CGFloat = 0,
        y: CGFloat = 2,
        blur: CGFloat = 4,
        spread: CGFloat = 0
    ) {
        shadowColor = color.cgColor
        shadowOpacity = alpha
        shadowOffset = CGSize(width: x, height: y)
        shadowRadius = blur / 2.0
        shouldRasterize = true
        rasterizationScale = UIScreen.main.scale
        if spread == 0 {
            shadowPath = nil
        } else {
            let dx = -spread
            let rect = bounds.insetBy(dx: dx, dy: dx)
            shadowPath = UIBezierPath(rect: rect).cgPath
        }
    }
}
```

We can now use the enum to apply shadows like this:

```swift
let view = UIView(frame.zero)
view.applyShadow(.medium)
```

This works great, but the enum approach has a big limitation. If the shadow is defined in a library, e.g. in an open source library, we'd be stuck with the fixed values that it provides.

Sometimes, this is EXACTLY what you want - a fixed set of options. If so, use enums. However, if you find that the enum model is holding you back, you can use structs instead.

For instance, this is how a `ShadowStyle` struct could look: 

```swift
struct ShadowStyle {
    
    init(
        alpha: Float,
        blur: CGFloat,
        color: UIColor = .black,
        spread: CGFloat = 0,
        x: CGFloat,
        y: CGFloat
    ) {
        self.alpha = alpha
        self.blur = blur
        self.color = color
        self.spread = spread
        self.x = x
        self.y = y
    }
    
    let alpha: Float
    let blur: CGFloat
    let color: UIColor
    let spread: CGFloat
    let x: CGFloat
    let y: CGFloat
}
```

This struct has the same properties as the enum, but is easier to overview. We define the properties when we create a shadow, after which they're immutable.

We can now add as many shadow styles as we want, in a way that I think is more natural:

```swift
extension Shadow {

    static var small: Shadow {
        .init(alpha: 0.2, blur: 8, x: 0, y: 4)
    }
    
    static var medium: Shadow {
        .init(alpha: 0.26, blur: 15, x: 0, y: 5)
    }
    
    static var large: Shadow {
        .init(alpha: 0.6, blur: 40, x: 0, y: 10)
    }
}
```

We now have a set of shadow styles that we can apply just like with the enum from before:

```swift
let view = UIView(frame.zero)
view.applyShadow(.medium)
```

This is much more flexible. No enum is holding us back. However, there are some side-effects with structs that you must be aware of.


## Unexpected side-effects with using structs

You can handle structs in much the same way as an enum, e.g. compare two `Equatable` instances, switch over values etc.

For enums, it would look like this:

```swift
public enum Direction: Equatable {
    case up, down, left, right
}

let environment = Direction.down

if environment == .up { print("Up") }       // Won't happen
if environment == .down { print("Down") }   // Will happen

switch environment {
case .up: print("Up")                       // Won't happen
default: print("Not up")                    // Will happen
}
```

For structs, the equality check and switching looks identical:

```swift
struct Direction: Equatable {}

extension Direction {
    static var up: Direction { .init() }
    static var down: Direction { .init() }
    static var left: Direction { .init() }
    static var right: Direction { .init() }
}

let direction = Direction.down

if direction == .up { print("Up") }         // Will happen
if direction == .down { print("Down") }     // Will happen

switch direction {
case .up: print("Up")                       // Will happen
default: print("Not up")                    // Won't happen
}
```

...but the results does not. We create a `.down` direction, but when we check for equality and switch over it, it's equal to both `.up` and `.down`. What's going on?

The reason is that all directions *are* identical! The `Direction` struct is implicitly equatable, but uses identical values for all four "cases".

To solve this, every struct options must be unique. We can solve this by making sure that `Equatable` behaves correctly, for instance:

```swift
struct Direction: Equatable {
    let name: String
}

extension Direction {
    static var up: Direction { .init(name: "up") }
    static var down: Direction { .init(name: "down") }
    static var left: Direction { .init(name: "left") }
    static var right: Direction { .init(name: "right") }
}

let direction = Direction.down

if direction == .up { print("Up") }         // Won't happen
if direction == .down { print("Down") }     // Will happen

switch direction {
case .up: print("Up")                       // Won't happen
default: print("Not up")                    // Will happen
}
```

By making each direction unique, this now works! We don't have to implement any equality logic, just provide the struct with a property that is unique for each case, and we're done.


## Conclusion

Enums are great when you want a fixed set of options. However, a struct that behaves like an enum is better when you want a type that can be extended with more options.

Another option is to used parameterized enum cases, which you can use to define various predefined configurations. You'd still however be constrained to the defined cases.

When this post was written, Swift enums couldn't implement certain protocols if a case had parameters. This is however no longer the case, so this will not hold you back.

In the end, use the approach you feel is most fitting for your use-case. Just watch out for the pitfalls that exist with either approach.