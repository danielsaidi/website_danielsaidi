---
title: URL-encoding strings in Swift
date:  2020-06-04 20:00:00 +0100
tags:  swift
icon:  swift

lib:    https://github.com/danielsaidi/SwiftUIKit
source: https://github.com/danielsaidi/SwiftUIKit/tree/master/Sources/SwiftUIKit/Extensions/String
---

In this post, we'll look at how to url encode strings. We'll then create an extension that let's us do this easier and with less code.


## The basics

As far as I know, Swift has no great native way to url encode strings. We can come a bit on our way by using `addingPercentEncoding`:

```swift
let string = "Hello, world & beyond!"
string.addingPercentEncoding(withAllowedCharacters: .urlPathAllowed)
// => "Hello,%20world%20&%20beyond!"
```

However, this will not perform a complete encoding. If a string contains `&` for instance, it remains unchanged and mess up things if the string should be used as query parameters.

To solve this, we need to replace all `&` with their url encoded form, `%26`:

```swift
string.addingPercentEncoding(withAllowedCharacters: .urlPathAllowed)
    .replacingOccurrences(of: "&", with: "%26")
// => "Hello,%20world%20%26%20beyond!"
```

This is better, but I think you see where I am going with this. We can't do this every time we want to url encode a string. We can do better.


## Extending String

Let's create a `String` extension to help us perform url encoding better:

```swift
public extension String {
    
    func urlEncoded() -> String? {
        addingPercentEncoding(withAllowedCharacters: .urlPathAllowed)?
            .replacingOccurrences(of: "&", with: "%26")
    }
}
```

We can now perform url encoding in a much more compact and readable way:

```swift
string.urlEncoded()
// => "Hello,%20world%20%26%20beyond!"
```

I think this is a lot cleaner. It's also less error-prone, since we don't repeat the same logic over and over in our codebase. 

Also, if we need to handle another character, we just have to change this single extension.


## Source Code

I have added this extension to my [SwiftUIKit]({{page.lib}}) library. You can find the source code [here]({{page.source}}). Feel free to try it out and let me know what you think!