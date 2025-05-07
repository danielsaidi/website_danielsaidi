---
title: Extending the Swift Result type
date:  2020-06-03 12:00:00 +0100
tags:  swift extensions
icon:  swift

lib:    https://github.com/danielsaidi/SwiftUIKit
source: https://github.com/danielsaidi/SwiftUIKit/tree/master/Sources/SwiftUIKit/Extensions
---

In this post, we'll extend the Swift native `Result` type with extensions that make it easier to use it in certain situations. 


## The basics

`Result` is a basic enum that consists of a `Success` and a `Failure` type. When you have a result instance, you can switch over it like this:

```swift
let result = Result<Bool, Error>.success(true)
switch result {
    case .failure(let error): print(error)
    case .success(let result): print(result)
}
```

While this is easy and the encouraged way to use `Result`, I'd prefer more convenient tools for working with result values.


## Extending Result

I think it would be convenient to quickly check if a `Result` is a failure or success, without having to switch over it. This is easily implemented with two extensions:

```swift
public extension Result {
    
    var isFailure: Bool { !isSuccess }
    
    var isSuccess: Bool {
        switch self {
        case .failure: false
        case .success: true
        }
    } 
```

We can now use `.isSuccess` and `.isFailure` to get this base information about the result.

I also like to have ways to quickly access the result error or success value. Let's implement this with two additional extensions:

```swift
public extension Result {
    
    var failureError: Failure? {
        switch self {
        case .failure(let error): error
        case .success: nil
        }
    }
    
    var successResult: Success? {
        switch self {
        case .failure: nil
        case .success(let value): value
        }
    }
}
```

We can now call `.failureError` and `.successResult` to get the generic, optional success value, or the result error, without having to switch over the result.


## Conclusion

This extensions in this post changes how you can work with `Result`. Since it's not the way that Swift as a language seems to think you should handle results, use them with caution.


## Source Code

I have added these extensions to my [SwiftUIKit]({{page.lib}}) library. You can find the source code [here]({{page.source}}). Feel free to try it out and let me know what you think!