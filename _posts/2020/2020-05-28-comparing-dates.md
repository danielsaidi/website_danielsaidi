---
title:  "Comparing dates in Swift"
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

`Date` has a very easy way to do this. I used to use `compare` a while back, but later versions of Swift let's you use `>`, `<` and `==` (thank you for reminding me, Jonas), for instance:

```swift
let date1 = Date(timeIntervalSince1970: 0)
let date2 = Date(timeIntervalSince1970: 1)
date1 > date2    // false
date1 < date2    // true
date1 == date2   // false
```

Even though this is cleaer than checking the comparison result, I still don't like this. I think Swift shines when it's readable, and this is more syntax than semantics.

That's something that I in fact think for many types, except maybe bools and numbers. By expressing something in written form, it's much harder to accidentally mistype `<` as `>`.

I think we can extend `Date` with functions that make these kind of operations more readable.


## More readable extensions

We can use the operations above in a set of extensions to create more readable functions for comparing dates:

```swift
public extension Date {
    
    func isAfter(_ date: Date) -> Bool {
        self > date
    }
    
    func isBefore(_ date: Date) -> Bool {
        self < date
    }
    
    func isSame(as date: Date) -> Bool {
        self == date
    }
}
```

If you now want to compare dates, you can do so with more readable code:

```swift
let date1 = Date(timeIntervalSince1970: 0)
let date2 = Date(timeIntervalSince1970: 1)
date1.isAfter(date2)    // false
date1.isBefore(date2)   // true
date1.isSame(as: date2) // false
```

I think this is more readable and less error prone. This semantic approach is something I often use, and something I think Swift should add more of to the language.

For instance, `myBool.toggle()` is easier to read than `myBool = !myBool`, which I think feels more like c/c++ that what most of Swift looks like.


## Source code

I have added these extensions to my [SwiftKit]({{page.lib}}) library. You can find the source code [here]({{page.source}}) and the unit tests [here]({{page.tests}}).