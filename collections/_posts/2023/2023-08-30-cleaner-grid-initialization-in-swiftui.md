---
title:  Cleaner grid initialization in SwiftUI
date:   2023-08-30 06:00:00 +0000
tags:   swiftui

icon:   swiftui

tweet:  https://twitter.com/danielsaidi/status/1696851725477351689?s=20
toot:   https://mastodon.social/@danielsaidi/110978398679088671
---

SwiftUI's `LazyVGrid` and `LazyHGrid` are great for creating powerful and flexible grids, but I always struggle with how to define columns when creating them. I've therefore created some extensions to make this easier. Let's take a look.

SwiftUI lets you define multiple columns for your grid, which is really convenient. For instance, this would create a grid where the items would alternate between a fixed size of `100` and `150`:

```swift
LazyVGrid(columns: [.init(.fixed(100)), .init(.fixed(150))]) {
    ...
}
```

and this would create a grid where all items will try to find an ideal size within a provided size range:

```swift
LazyVGrid(columns: [.init(.adaptive(minimum: 100, maximum: 150))]) {
    ...
}
```

That `columns` is an array is great, since it lets us combine the different `GridItem` types for maximum flexibility. However, I think the above reads really bad.

For instance, I tried typing the fixed-size example in plain text when writing this post, but to no surprise it had syntax errors when pasting it into Xcode. Also, having to provide array brackets for the adaptive item, when just having a single item, never becomes intuitive to me. Finally, having to type `.init(...)` makes the code harder to read.

To improve this, we can add a couple of `GridItem` and `Collection` extensions:

```swift
@available(iOS 14.0, tvOS 14.0, watchOS 7.0, *)
public extension GridItem {
    
    /// Multiple items in the space of a single flexible item.
    static func adaptive(minimum: CGFloat, maximum: CGFloat) -> Self {
        .init(.adaptive(minimum: minimum, maximum: maximum))
    }
    
    /// A single item with the specified fixed size.
    static func fixed(_ size: CGFloat) -> Self {
        .init(.fixed(size))
    }
    
    /// A single flexible item.
    static func flexible(minimum: CGFloat, maximum: CGFloat) -> Self {
        .init(.flexible(minimum: minimum, maximum: maximum))
    }
}

@available(iOS 14.0, tvOS 14.0, watchOS 7.0, *)
public extension Collection where Element == GridItem {
    
    /// Multiple items in the space of a single flexible item.
    static func adaptive(minimum: CGFloat, maximum: CGFloat) -> [Element] {
        [.adaptive(minimum: minimum, maximum: maximum)]
    }
    
    /// A single item with the specified fixed size.
    static func fixed(_ size: CGFloat) -> [Element] {
        [.fixed(size)]
    }
    
    /// Multiple items with the specified fixed sizes.
    static func fixed(_ sizes: [CGFloat]) -> [Element] {
        sizes.map { .fixed($0) }
    }
    
    /// A single flexible item.
    static func flexible(minimum: CGFloat, maximum: CGFloat) -> [Element] {
        [.flexible(minimum: minimum, maximum: maximum)]
    }
}
```

These static builders remove the need of having to use `.init(...)`, and make it easier to specify single items, as well as multiple fixed-size items.

With these extensions in place, you can now create grids like this:

```swift
LazyVGrid(columns: .fixed(100)) { ... }
LazyVGrid(columns: [.fixed(100), .fixed(150)]) { ... }
LazyVGrid(columns: .adaptive(minimum: 100, maximum: 150)) { ... }
```

Since we can still specify columns as arrays, just in a little cleaner way, we don't loose any flexibility and can mix and match items as we like.