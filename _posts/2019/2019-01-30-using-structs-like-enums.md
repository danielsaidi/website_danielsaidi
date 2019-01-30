---
title:  "Using structs like enums"
date:   2019-01-30 21:00:00 +0100
tags:	swift

swift-docs: https://docs.swift.org        
enums:   https://docs.swift.org/swift-book/LanguageGuide/Enumerations.html
structs: https://docs.swift.org/swift-book/LanguageGuide/ClassesAndStructures.html
---


Swift `enum` and `struct` are two powerful tools. In this post, I'll discuss how you typically use them and how to use structs like enums when you need more flexibility.


## Enums

[Swift Docs]({{page.swift-docs}}) describes enums like this: "An enumeration defines a common type for a group of related values and enables you to work with those values in a type-safe way within your code."

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

I will not go into further detail on how enums work. If you want to learn more, check out [the official docs]({{page.enums}}).


## Structs

[Swift Docs]({{page.swift-docs}}) describes structs like this: "Structures are general-purpose, flexible constructs that become the building blocks of your program’s code. You define properties and methods to add functionality to your structures and classes using the same syntax you use to define constants, variables, and functions."

In short, if you need to pass values around, structs are great and give you better data integrity than you get with classes. It took me a while to start using structs, but now I can't live without them.

For instance, the following struct defines a food order with an immutable order number and item collection:

```swift
struct FoodOrder: Codable {

    let orderNumber: Int
    let items: [Food]
}
```  

As with enums, I will not go into detail on how structs work. If you want to learn more, check out [the official docs]({{page.structs}}).


## Using structs instead of enums

Given the discussion above, consider that we'd like to specify a list of possible shadow styles as well as an extension that can be used to apply it to views.

If you'd asked me some years ago, I would have done this as an enum:

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
        case .small: return 0.2
        case .medium: return 0.26
        case .large: return 0.6
        }
    }
    
    var blur: CGFloat {
        switch self {
        case .small: return 8
        case .medium: return 15
        case .large: return 40
        }
    }
    
    var color: UIColor {
        return .black
    }
    
    var spread: CGFloat {
        return 0
    }
    
    var x: CGFloat {
        return 0
    }
    
    var y: CGFloat {
        switch self {
        case .small: return 4
        case .medium: return 5
        case .large: return 10
        }
    }
}
```

I would then finally have created a `UIView` extension that can be used to apply typed shadows to views:

```swift
extension UIView {
    
    func applyShadow(_ shadow: ShadowStyle) {
        layer.applyShadow(
            color: shadow.color,
            alpha: shadow.alpha,
            x: shadow.x,
            y: shadow.y,
            blur: shadow.blur,
            spread: shadow.spread)
    }
}

private extension CALayer {
    
    func applyShadow(
        color: UIColor = .black,
        alpha: Float = 0.5,
        x: CGFloat = 0,
        y: CGFloat = 2,
        blur: CGFloat = 4,
        spread: CGFloat = 0) {
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

We can now create shadow instances and use the enum as arguments, like this:

```swift
let myShadow = Shadow.medium

let view = UIView(frame.zero)
view.applyShadow(.medium)
```

This works great, but there is a big limitation with this enum approach. If the shadow is defined in a library, e.g. in an open source library, we'd be stuck with the fixed values that it provides. There would be no way for an app to create custom shadows.

Sometimes, this is EXACTLY what you want - a fixed set of options. If so, use enums with a smile on your face. However, if you find that the enum model is holding you back, you can simulate enums using structs and get a much more flexible result.

To do so, first create a `Shadow` struct:

```swift
struct Shadow {
    
    init(
        alpha: Float,
        blur: CGFloat,
        color: UIColor = .black,
        spread: CGFloat = 0,
        x: CGFloat,
        y: CGFloat) {
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

This struct has the same properties as the enum, but they are much easier to overview. You define them when you create a shadow. After that, they're immutable.

We now have a shadow type with properties, but not any predefined styles, opposite to when we created the enum. We then started with the available options, then defined their properties. I think this feels more natural.

We can now redefine the shadow styles from before as struct extensions:

```swift
extension Shadow {

    static var small: Shadow {
        return Shadow(alpha: 0.2, blur: 8, x: 0, y: 4)
    }
    
    static var medium: Shadow {
        return Shadow(alpha: 0.26, blur: 15, x: 0, y: 5)
    }
    
    static var large: Shadow {
        return Shadow(alpha: 0.6, blur: 40, x: 0, y: 10)
    }
}
```

That's it! We now have an extensive set of shadow styles that we can apply just like the enum from before:

```swift
let myShadow = Shadow.medium

let view = UIView(frame.zero)
view.applyShadow(.medium)
```

If we would have to create another app-specific shadow for a unique tvOS app, we'd just have to define another static shadow property like above.

This is much better...or at least much more flexible. No enum is holding us back anymore. However, if you switch over to structs, there are some side-effects that you must be aware of.


## Unexpected side-effects

You can handle structs in much the same way as an enum, e.g. compare two instances if they implement `Equatable`, switch over a value etc.

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
    static var up: Direction { return Direction() }
    static var down: Direction { return Direction() }
    static var left: Direction { return Direction() }
    static var right: Direction { return Direction() }
}

let direction = Direction.down

if direction == .up { print("Up") }         // Will happen
if direction == .down { print("Down") }     // Will happen

switch direction {
case .up: print("Up")                       // Will happen
default: print("Not up")                    // Won't happen
}
```

...but the comparison results are not. We create a `.down` direction, but when we check for equality and switch over it, it's equal to both `.up` and `.down`.

What is going on here?? Well, the reason is that all directions *are* identical! The `Direction` struct is equatable, but basically, it uses identical values for all four "cases".

To solve this, every struct options must be unique. We can solve this by making sure that `Equatable` behaves correctly, for instance:

```swift
struct Direction: Equatable {
    let name: String
}

extension Direction {
    static var up: Direction { return Direction(name: "up") }
    static var down: Direction { return Direction(name: "down") }
    static var left: Direction { return Direction(name: "left") }
    static var right: Direction { return Direction(name: "right") }
}

let direction = Direction.down

if direction == .up { print("Up") }         // Won't happen
if direction == .down { print("Down") }     // Will happen

switch direction {
case .up: print("Up")                       // Won't happen
default: print("Not up")                    // Will happen
}
```

Yes, this works! As you can see, we don't have to implement any equality logic, just provide the struct with a property that will be unique for each case.



## Conclusion

Enums are great when you want a fixed set of options. However, structs that behave like enums are better when you want a type that can be extended with more options. Just watch out for the unexpected pitfalls.




