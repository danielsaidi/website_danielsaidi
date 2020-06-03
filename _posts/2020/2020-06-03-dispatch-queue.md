---
title:  "Delay and chain operations with DispatchQueue"
date:   2020-06-03 12:00:00 +0100
tags:   swift
icon:   swift

lib:    https://github.com/danielsaidi/SwiftKit
source: https://github.com/danielsaidi/SwiftKit/tree/master/Sources/SwiftKit/Extensions
tests:  https://github.com/danielsaidi/SwiftKit/tree/master/Tests/SwiftKitTests/Extensions
---

In this post, we'll look at how `DispatchQueue` can be used to delay and chain operations.


## Delaying operations

Delaying operations with `DispatchQueue` is very easy, using `asyncAfter`:

```swift
let queue = DispatchQueue.main
let delay = DispatchTimeInterval.milliseconds(50)
queue.asyncAfter(deadline: .now() + delay) {
    print("I was delayed 50 milliseconds")
}
```

However, I think the `deadline` function is strangely named and not that convenient to use, since you have to add `.now()` to your "deadline". It's probably correct from how the queue operates, but using it is not that nice.

Let's create an extension that makes delaying operations easier:

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

I think this is much cleaner, although I'd prefer the function to be called `performAfter(...)` instead. However, I chose this name to harmonize with the existing apis.


## Chaining operations

Chaining operations with `DispatchQueue` is easy as well, using `async` then performing another operation on the same or another queue:

```swift
let queue = DispatchQueue.main
async {
    { print("Hello") }
    queue.async { print(", world!") }
}
```

If the async operation returns a value, it can be passed to the chained operation like this:

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

As with the delay operations, I'd have preferred to use `perform` instead of `async` and will perhaps over time make the functions even more compact, but for now I've chosen to conform to the existing apis.


## Source code

I have added these extensions to my [SwiftKit]({{page.lib}}) library. You can find the source code [here]({{page.source}}) and the unit tests [here]({{page.tests}}).