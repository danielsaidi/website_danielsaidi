---
title: Case-sensitive string replacements in Swift
date:  2020-06-04 16:00:00 +0100
tags:  swift extensions
icon:  swift

lib:    https://github.com/danielsaidi/SwiftUIKit
source: https://github.com/danielsaidi/SwiftUIKit/tree/master/Sources/SwiftUIKit/Extensions/String
---

In this post, we'll discuss how to replace all occurences of a string in another string and then create an extension that allows for easier case-sensitive replacements.


## The basics

We can use `replacingOccurrences(of:with:)` to replace all occurences of a certain string within another string:

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
string.replacingOccurrences(
    of: "Text", 
    with: "characters", 
    options: .caseInsensitive
) // => "This string contains characters"
```

However, I think that the function name is too long and that `options` is tedious to use.


## Extending String

We can create shorter and convenient `String` extensions to let us perform case-sensitive and case-insensitive replacements:

```swift
public extension String {
    
    func replacing(
        _ string: String, 
        with: String
    ) -> String {
        replacingOccurrences(of: string, with: with)
    }
    
    func replacing(
        _ string: String, 
        with: String, 
        caseSensitive: Bool
    ) -> String {
        caseSensitive
            ? replacing(string, with: with)
            : replacingOccurrences(
                of: string, 
                with: with, 
                options: .caseInsensitive
            )
    }
}
```

We can now perform case-sensitive and case-insensitive contains checks the same way:

```swift
string.replacing("text", with: "characters")
string.replacing("Text", with: "characters", caseSensitive: true)
```

I like this since it lets me write a little less code when replacing strings in Swift.


## Source Code

I have added these extensions to my [SwiftUIKit]({{page.lib}}) library. You can find the source code [here]({{page.source}}). Feel free to try it out and let me know what you think!