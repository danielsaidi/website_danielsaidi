---
title: Case-sensitive String replace operations
date:  2020-06-04 16:00:00 +0100
tags:  swift
icon:  swift

lib:    https://github.com/danielsaidi/SwiftKit
source: https://github.com/danielsaidi/SwiftKit/tree/master/Sources/SwiftKit/Extensions/String
tests:  https://github.com/danielsaidi/SwiftKit/tree/master/Tests/SwiftKitTests/Extensions/String
---

In this post, we'll discuss how to replace all occurences of a string within another string. We'll then create an extension that allows for easier case-sensitive replacements.


## The basics

Replacing all occurences of a string within another string is easy, using `replacingOccurrences(of:with:)`:

```swift
let string = "This string contains text"
string.replacingOccurrences(of: "text", with: "characters")
// => "This string contains characters"
```

However, this will not perform a case-insensitive replacement, which means that

```swift
string.replacingOccurrences(of: "Text", with: "characters")
// => "This string contains text"
```

To allow for case-insensitive contains checks, you can provide a `.caseInsensitive` option:

```swift
string.replacingOccurrences(of: "Text", with: "characters", options: .caseInsensitive)
// => "This string contains characters"
```

However, I think that the function name is too long and that `options` is tedious to use.


## Extending String

We can create shorter and slightly more convenient extensions to let us perform case-sensitive and case-insensitive replacements:

```swift
public extension String {
    
    func replacing(_ string: String, with: String) -> String {
        replacingOccurrences(of: string, with: with)
    }
    
    func replacing(_ string: String, with: String, caseSensitive: Bool) -> String {
        caseSensitive
            ? replacing(string, with: with)
            : replacingOccurrences(of: string, with: with, options: .caseInsensitive)
    }
}
```

We can now perform case-sensitive and case-insensitive contains checks the same way:

```swift
string.replacing("text", with: "characters")
string.replacing("Text", with: "characters", caseSensitive: true)
```

I like this since it lets me write a little less code when replacing strings in Swift.


## Source code

I have added these extensions to my [SwiftKit]({{page.lib}}) library. You can find the source code [here]({{page.source}}) and the unit tests [here]({{page.tests}}).