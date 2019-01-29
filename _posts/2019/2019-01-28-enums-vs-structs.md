---
title:  "Enums vs. structs"
date:   2119-01-28 21:00:00 +0100
tags:	swift
---

Enums: Fixed set of options, final, comparisons, when you're in control
Structs: Dynamic set of options, expansive, when others should be able to extend

```swift
import UIKit

public struct Shadow {
    
    public init(
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
    
    public let alpha: Float
    public let blur: CGFloat
    public let color: UIColor
    public let spread: CGFloat
    public let x: CGFloat
    public let y: CGFloat
}
```

```
import UIKit

public extension Shadow {

    public static var card: Shadow {
        return Shadow(alpha: 0.08, blur: 2, x: 0, y: 2)
    }
    
    public static var smallSoft: Shadow {
        return Shadow(alpha: 0.2, blur: 8, x: 0, y: 4)
    }
    
    public static var smallStrong: Shadow {
        return Shadow(alpha: 0.5, blur: 8, x: 0, y: 4)
    }
    
    public static var mediumSoft: Shadow {
        return Shadow(alpha: 0.26, blur: 15, x: 0, y: 5)
    }
    
    public static var mediumStrong: Shadow {
        return Shadow(alpha: 0.6, blur: 15, x: 0, y: 5)
    }
    
    public static var large: Shadow {
        return Shadow(alpha: 0.6, blur: 40, x: 0, y: 10)
    }
}
```


```
import UIKit

public extension UIView {
    
    public func applyShadow(_ shadow: Shadow) {
        clipsToBounds = false
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


```
import UIKit

public enum ShadowStyle {
    
    case
    card,
    smallSoft,
    smallStrong,
    mediumSoft,
    mediumStrong,
    large
}


public extension ShadowStyle {
    
    var alpha: Float {
        switch self {
        case .card: return 0.08
        case .smallSoft: return 0.2
        case .smallStrong: return 0.5
        case .mediumSoft: return 0.26
        case .mediumStrong: return 0.6
        case .large: return 0.6
        }
    }
    
    var blur: CGFloat {
        switch self {
        case .card: return 2
        case .smallSoft: return 8
        case .smallStrong: return 8
        case .mediumSoft: return 15
        case .mediumStrong: return 15
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
        case .card: return 2
        case .smallSoft: return 4
        case .smallStrong: return 4
        case .mediumSoft: return 5
        case .mediumStrong: return 5
        case .large: return 10
        }
    }
}
```

```
public extension UIView {
    
    func applyShadow(_ style: ShadowStyle) {
        clipsToBounds = false
        layer.applySketchShadow(
            color: style.color,
            alpha: style.alpha,
            x: style.x,
            y: style.y,
            blur: style.blur,
            spread: style.spread)
    }
}


// Taken from https://stackoverflow.com/a/48489506/2024434

private extension CALayer {
    
    func applySketchShadow(
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