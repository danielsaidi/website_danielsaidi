---
title: Case-sensitive String contains checks in Swift
date:  2020-06-04 12:00:00 +0100
tags:  swift extensions
icon:  swift

redirect_from: /blog/2020/06/04/string-contains

lib:    https://github.com/danielsaidi/SwiftUIKit
source: https://github.com/danielsaidi/SwiftUIKit/tree/master/Sources/SwiftUIKit/Extensions/String
---

In this post, we'll look at how to check if a string contains another string. We'll then create an extension that allows for easier case-insensitive checks.


## The basics

We can use `contains` to check if a string contains another string:

```swift
let string = "This string contains text"
string.contains("contains") // => true
```

However, this will not perform a case-insensitive check, which means that

```swift
let string = "This string contains text"
string.contains("Contains") // => false
```

To allow for case-insensitive contains checks, you can lower-case both strings:

```swift
string.lowercased().contains("Contains".lowercased()) // => true
```

This is however not performant, since you create two new strings to perform this check. 

You could use `range` instead, and provide it with a `.caseInsensitive` option:

```swift
string.range(of: "Contains", options: .caseInsensitive) != nil // => true
```

However, I don't think that this is that readable. We can do better.


## Extending String

I think a more readable approach is to create a `contains` extension with a `caseSensitive` argument and adjust its logic depending on if the check should be case-sensitive or not:

```swift
public extension String {
    
    func contains(_ string: String, caseSensitive: Bool) -> Bool {
        caseSensitive
            ? contains(string)
            : range(of: string, options: .caseInsensitive) != nil
    }
}
```

We can now perform case-sensitive and case-insensitive contains checks the same way:

```swift
string.contains("contains") // => true
string.contains("Contains") // => false
string.contains("Contains", caseSensitive: true) // => false
string.contains("Contains", caseSensitive: false) // => true
```

I think that this is a lot cleaner, and for case-sensitive checks a lot more readable.


## Source Code

I have added this extension to my [SwiftUIKit]({{page.lib}}) library. You can find the source code [here]({{page.source}}). Feel free to try it out and let me know what you think!