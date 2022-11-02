---
title: Extending the Swift Result type
date:  2020-06-03 12:00:00 +0100
tags:  swift extensions
icon:  swift

lib:    https://github.com/danielsaidi/SwiftKit
source: https://github.com/danielsaidi/SwiftKit/tree/master/Sources/SwiftKit/Extensions
---

In this post, we'll extend `Result` with extensions that make using it easier in certain situations. 


## The basics

`Result` is a very basic enum that consists of a `Success` and a `Failure` type. When you have a result instance, you can switch over it to inspect it:

```swift
let result = Result<Bool, Error>.success(true)
switch result {
    case .failure(let error): print(error)
    case .success(let result): print(result)
}
```

While this is easy enough and the encouraged way to use `Result`, I'd prefer more convenient tools for working with result values.


## Extending `Result`

First, I think it would be convenient to quickly check if a `Result` is a failure or success, without having to switch over it. This is easily implemented with two extensions:

```swift
public extension Result {
    
    var isFailure: Bool { !isSuccess }
    
    var isSuccess: Bool {
        switch self {
        case .failure: return false
        case .success: return true
        }
    } 
```

We can now call `.isSuccess` and `.isFailure` to get information about the nature of the result.

I'd also prefer to have ways to quickly access the failure error or success result. Let's implement this with two additional extensions:

```swift
public extension Result {
    
    var failureError: Failure? {
        switch self {
        case .failure(let error): return error
        case .success: return nil
        }
    }
    
    var successResult: Success? {
        switch self {
        case .failure: return nil
        case .success(let value): return value
        }
    }
}
```

We can now call `.failureError` and `.successResult` to get the generic, optional values without having to switch over the result.


## Conclusion

This extensions in this post changes how you can work with `Result`. Since it's not the way that Swift as a language seems to think you should handle results, use them with caution.


## Source code

I have added these extensions to my [SwiftKit]({{page.lib}}) library. You can find the source code [here]({{page.source}}). Feel free to try it out and let me know what you think!