---
title: Delay and chain Swift operations with DispatchQueue
date:  2020-06-03 12:00:00 +0100
tags:  swift async
icon:  swift

redirect_from: /blog/2020/06/03/dispatch-queue

lib:    https://github.com/danielsaidi/SwiftUIKit
source: https://github.com/danielsaidi/SwiftUIKit/tree/master/Sources/SwiftUIKit/Extensions
---

In this post, we'll look at how `DispatchQueue` can be used to delay and chain operations. We'll also extend it with convenient functions that simplify these tasks.


## Delaying operations

We can easily delaying operations with `DispatchQueue` by using the `asyncAfter` function:

```swift
let delay = DispatchTimeInterval.milliseconds(50)
DispatchQueue.main.asyncAfter(deadline: .now() + delay) {
    print("I was delayed 50 milliseconds")
}
```

However, I think the `deadline` is not that convenient, since you have to add `.now()` to it. It's probably correct from how a queue works, but it's not that nice to use.

We can create an extension that makes delaying operations easier:

```swift
public extension DispatchQueue {
    
    func asyncAfter(
        _ interval: DispatchTimeInterval,
        execute: @escaping () -> Void) {
        asyncAfter(
            deadline: .now() + interval,
            execute: execute)
    }
}
```

We can now delay operations like this:

```swift
let queue = DispatchQueue.main
queue.asyncAfter(.seconds(1)) {
    print("I was delayed 1 second")
}
```

I think this is much cleaner, although I'd prefer the function to be called `performAfter(...)`. I however chose this name to harmonize with the existing APIs.


## Chaining operations

Chaining operations with `DispatchQueue` is easy as well, using `async` to perform another operation on the same or another queue:

```swift
let queue = DispatchQueue.main
queue.async {
    { print("Hello") }
    queue.async { print(", world!") }
}
```

If an async operation returns a value, it can be passed to the chained operation like this:

```swift
let queue = DispatchQueue.main
queue.async {
    let result = { return "Hello" }
    queue.async { print(result + ", world!") }
}
```

This works well, but like `asyncAfter`, I think this is lacking in usability as well.

Let's create an extension that makes chaining operations easier:

```swift
public extension DispatchQueue {
    
    func async<T>(
        execute: @escaping () -> T,
        then completion: @escaping (T) -> Void,
        on completionQueue: DispatchQueue = .main) {
        async {
            let result = execute()
            completionQueue.async {
                completion(result)
            }
        }
    }
}
```

We can now chain operations like this:

```swift
let queue = DispatchQueue.main
queue.async(
    execute: { return "Hello"}, 
    then: { print($0 + ", world!") },
    on: .main
)
```

If you're happy with using the default `.main` completion queue, this can also be expressed as:

```swift
let queue = DispatchQueue.main
queue.async(execute: { return "Hello"}) {
    print($0 + ", world!")
}
```

As with the delay operations, I'd have preferred to use `perform` instead of `async` and will perhaps over time make the functions more compact, but for now I've chosen to conform to the existing apis.


## Source Code

I have added these extensions to my [SwiftUIKit]({{page.lib}}) library. You can find the source code [here]({{page.source}}). Feel free to try it out and let me know what you think!