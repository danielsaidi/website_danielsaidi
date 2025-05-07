---
title: Modifying dates in Swift
date:  2020-05-27 22:00:00 +0100
tags:  swift dates extensions
icon:  swift

lib:    https://github.com/danielsaidi/SwiftUIKit
source: https://github.com/danielsaidi/SwiftUIKit/tree/master/Sources/SwiftUIKit/Date
---

In this post, we'll look at how to modify dates in Swift. We'll also extend `Date` with ways to modify dates with clean, readable code.


## The basics

Date manipulation is a common task in many apps. For instance, we may want to know the date five hours from now, to schedule an operation, refresh or remove data etc.

`Date` has an `addingTimeInterval(...)` function that can add and remove a `TimeInterval` (a typealias for `Double` seconds) to any date and returns the resulting date.

For instance, this adds an hour to the current date:

```swift
Date().addingTimeInterval(3600)
```

and this removes an hour from the current date:

```swift
Date().addingTimeInterval(-3600)
```

However, I find `addingTimeInterval` cumbersome to use when you want more fine-grained control or use larger units than seconds. Also, the code isn't that readable.


## Date extensions

We can use the function above in a set of `Date` extensions, to get more readable code:

```swift
public extension Date {
    
    func adding(days: Double) -> Date {
        let seconds = Double(days) * 60 * 60 * 24
        return addingTimeInterval(seconds)
    }
    
    func adding(hours: Double) -> Date {
        let seconds = Double(hours) * 60 * 60
        return addingTimeInterval(seconds)
    }
    
    func adding(minutes: Double) -> Date {
        let seconds = Double(minutes) * 60
        return addingTimeInterval(seconds)
    }
    
    func adding(seconds: Double) -> Date {
        addingTimeInterval(Double(seconds))
    }
    
    func removing(days: Double) -> Date {
        adding(days: -days)
    }
    
    func removing(hours: Double) -> Date {
        adding(hours: -hours)
    }
    
    func removing(minutes: Double) -> Date {
        adding(minutes: -minutes)
    }
    
    func removing(seconds: Double) -> Date {
        adding(seconds: -seconds)
    }
}
```

You can now modify dates with cleaner code and also chain multiple operations together:

```swift
let date = Date()
    .adding(days: 3)
    .adding(hours: 2)
    .removing(seconds: 15)
```

I think this is much more readable than the time interval-based function.


## Source Code

I have added these extensions to my [SwiftUIKit]({{page.lib}}) library. You can find the source code [here]({{page.source}}). Feel free to try it out and let me know what you think!