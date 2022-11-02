---
title: Modify dates in Swift
date:  2020-05-27 22:00:00 +0100
tags:  swift dates extensions
icon:  swift

lib:    https://github.com/danielsaidi/SwiftKit
source: https://github.com/danielsaidi/SwiftKit/tree/master/Sources/SwiftKit/Date
---

In this post, we'll extend `Date` with functions that let us add and remove seconds, minutes, hours and days to a date.


## The basics

Date manipulation is a common task in many apps. For instance, we may want to know the date five hours from now, to schedule an operation, refresh or remove data etc.

`Date` has an easy way to do this. `addingTimeInterval` lets you add a `TimeInterval` (which is a typealias for `Double`) to any date and returns the resulting date.

For instance, this adds an hour to the current date:

```swift
let date = Date()
date.addingTimeInterval(3600)
```

However, I find `addingTimeInterval` cumbersome to use when you want more fine-grained control or use larger units than seconds. Also, the code isn't that readable.


## More readable extensions

We can use `addingTimeInterval` in a set of extension functions, to create more readable functions for manipulating dates:

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

If you now want to modify a certain date, you can do so with more readable code and also chain multiple operations together:

```swift
let date = Date()
    .adding(days: 3)
    .adding(hours: 2)
    .removing(seconds: 15)
```

I think this is much more readable than the time interval-based function.


## Source code

I have added these extensions to my [SwiftKit]({{page.lib}}) library. You can find the source code [here]({{page.source}}). Feel free to try it out and let me know what you think!