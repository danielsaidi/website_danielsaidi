---
title: Base64 encode and decode strings in Swift
date:  2020-06-04 08:00:00 +0100
tags:  swift extensions
icon:  swift

lib:    https://github.com/danielsaidi/SwiftUIKit
source: https://github.com/danielsaidi/SwiftUIKit/tree/master/Sources/SwiftUIKit/Extensions/String
---

In this post, we'll discuss how to Base64 encode and decode strings in Swift. We'll also create a couple of extensions to make this easier to use and more readable.


## The basics

You can use `base64EncodedString()` to Base64-encode a `String`'s data value in Swift:

```swift
let string = "Let's encode this string"
let encoded = string.data(using: .utf8)?.base64EncodedString()
```

To decode a Base64 encoded string, convert it to data, then create a string from the data:

```swift
guard let data = Data(base64Encoded: self) else { return nil }
return String(data: data, encoding: .utf8)
```

Both encoding and decoding can fail, which means that they both return optional strings. 


## Extending String

Although the above operations are straightforward, I prefer to use more convenient and readable extensions, to avoid having to write the same code over and over.

To Base64 encode and decode strings, I just wrap the logic in a `String` extension:

```swift
extension String {

    func base64Encoded() -> String? {
        data(using: .utf8)?.base64EncodedString()
    }

    func base64Decoded() -> String? {
        guard let data = Data(base64Encoded: self) else { return nil }
        return String(data: data, encoding: .utf8)
    }
}
```

We can now encode and decode strings like this:

```swift
let string = "Let's encode this string"
let encoded = string.base64Encoded()
let decoded = encoded?.base64Decoded()
```

I think that this is a lot cleaner. Since the decode logic requires a `guard`, you also save one line each time and avoid having to use control flow where you may not want it.


## Source Code

I have added these extensions to my [SwiftUIKit]({{page.lib}}) library. You can find the source code [here]({{page.source}}). Feel free to try it out and let me know what you think!