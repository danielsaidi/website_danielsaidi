---
title:  "Comparing dates in swift"
date:   2020-05-28 22:00:00 +0100
tags:   swift
icon:   swift

lib:    https://github.com/danielsaidi/SwiftKit
source: https://github.com/danielsaidi/SwiftKit/tree/master/Sources/SwiftKit/Date
tests:  https://github.com/danielsaidi/SwiftKit/tree/master/Tests/SwiftKitTests/Date
---

In this post, we'll extend Swift's `Date` type with functions that let us compare dates with clean, readable code.


## The basics

Date comparisons are common tasks in many apps. For instance, we may want to know if a certain date is before or after another.

`Date` has a very easy way to do this. `compare` lets you compare a date with another and returns the resulting `ComparisonResultdate`.

For instance, this checks whether or not a certain date is after another date:

```swift
let date1 = Date(timeIntervalSince1970: 0)
let date2 = Date(timeIntervalSince1970: 1)
let result = date1.compare(date2) == .orderedDescending // This is false
```

I find this approach cumbersome and that the resulting code isn't that readable. I think we can extend `Date` with functions that make these kind of operations more readable.


## More readable extensions

We can use `compare` in a set of extension functions, to create more readable functions for comparing dates:

```swift
public extension Date {
    
    func isAfter(_ date: Date) -> Bool {
        compare(date) == .orderedDescending
    }
    
    func isBefore(_ date: Date) -> Bool {
        compare(date) == .orderedAscending
    }
    
    func isSameAs(_ date: Date) -> Bool {
        compare(date) == .orderedSame
    }
}
```

If you now want to compare dates, you can do so with more readable code:

```swift
let date1 = Date(timeIntervalSince1970: 0)
let date2 = Date(timeIntervalSince1970: 1)
date1.isAfter(date2)    // false
date1.isBefore(date2)   // true
date1.isSameAs(date2)   // false
```

I think this is much more readable than checking whether or not the comparison result is ascending or descending.


## Grab the code

I have added these extensions to my [SwiftKit]({{page.lib}}) library, which contains a lot of additional functionality for Swift. You can find the source code [here]({{page.source}}) and the unit tests [here]({{page.tests}}).