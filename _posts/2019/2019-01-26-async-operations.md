---
title:  "Coordinating async operations"
date:   2019-01-26 21:00:00 +0100
tags:	swift rxswift async-await awaitkit promisekit

tldr: http://danielsaidi.com/blog/2019/01/29/async-operations-bitesized
source: https://github.com/danielsaidi/iExtra/tree/master/iExtra/Operations

awaitkit: https://github.com/yannickl/AwaitKit
promisekit: https://github.com/mxcl/PromiseKit
procedurekit: https://github.com/ProcedureKit/ProcedureKit
rxswift: https://github.com/ReactiveX/RxSwift
rxswift-post: http://danielsaidi.com/blog/2018/01/19/ditching-rxswift
reactivecocoa: https://github.com/ReactiveCocoa/ReactiveCocoa
---

Swift is an amazing language, but I find that it lacks good native support for coordinating multiple async operations in a sophisticated way. In this post, I will look at existing libraries for handling this, then discuss a lightweight alternative.


## TL;DR

This is a really long article, that discusses stuff in detail. If you want a more bite-sized version of this post, I have published a short version of this post [here]({{page.tldr}}).



## 3rd party alternatives

While languages like .NET and JavaScript has native support for async/await and Android has coroutines, Swift developers must rely on 3rd party libraries or build their own solution for working with async operations. Let's look at some open source libraries that aims at simplifying this.


### PromiseKit

[PromiseKit]({{page.promisekit}}) is a nice library that gives you access to a `Promise` model that makes your code look something like this:

```swift
firstly {
    makeRequest1()  // Returns a promise
}.then { result1 in
    makeRequest2(result1)
}.then { result2 in
    doSomethingElse()
}.catch { error in
    // Handle any error
}
``` 

I like promise-based chaining of async operations, but find PromiseKit to be too talky. Furthermore, you'll still be jumping in and out of blocks.


### AwaitKit

[AwaitKit]({{page.awaitkit}}) extends PromiseKit with an `async/await` model that makes the code above look something like this:

```swift
let result1 = try! await(makeRequest1())
let result2 = try! await(makeRequest2(result1))
doSomethingElse()
```

AwaitKit removes the usage of blocks and makes the code look synchronous, which is easier to both write and read. The only thing I find strange with AwaitKit's sample code, is the use of `try!`. If you want to handle errors, just wrap the code above in a `do/catch` instead. Also note that if you decide to go with AwaitKit, you implicitly get a depencendy to PromiseKit as well.


### RxSwift / ReactiveCocoa

If the promise model doesn't appeal to you, take a look at observables. [RxSwift]({{page.rxswift}}) and [ReactiveCocoa]({{page.reactivecocoa}}) are two popular observable libraries. I had a hard time liking RxSwift, though (and haven't tried ReactiveCocoa). I wrote about this in a [blog post]({{page.rxswift-post}}) a year ago.


### ProcedureKit

I found [ProcedureKit]({{page.procedurekit}}) after writing the first version of post. It seems well designed and looked after, with nice documentations and great examples. Not a lot of stars, but it's been going strong for a couple of years and seems to be kept up to date. May be worth checking out.



## Building it yourself

What I dislike with blindly going for the promise/observable-based libraries above, is that they can fundamentally change your architecture. Using async/await or rx will affect your entire code base if you let it, and will make your code heavily depend on 3rd party dependencies.

If you are willing to make that choice, go ahead and give them a try! However, if you want to keep your external dependencies down to a minimum and not fundamentally change your architecture, you can come a long way with encapsulation, abstractions and some custom code.

In the examples below, I will combine a couple of minimal protocols to get two lightweight, modular ways of coordinating the execution of multiple async operations. I will not use Grand Central Dispatch (GCD), but may improve the approach to use GCD under the hood later on. 

For now, however, the most important thing for me with this blog post is to talk *design* and *composition*. How the actual coordination is implemented under the hood is an implementation detail, that can change while the external interface remains intact. So for now, let's focus on discussing, designing and implementing.



## Coordinating simple operations

If we just want to coordinate a set of abstract operations, we can put together a simple approach that we can use to build even cooler things with later on.

Let's start by defining an abstract `Operation`. Note that this name conflicts with `Foundation.Operation`, so you may have to specify it as `MyLib.Operation`.

```swift
protocol Operation {
    
    typealias Completion = (Error?) -> ()
    
    func perform(completion: @escaping Completion)
}
```

To coordinate how operations are executed, let's define an `OperationCoordinator` that can perform a set of operations in any way:

```swift
protocol OperationCoordinator {
    
    typealias Completion = ([Error?]) -> ()
    
    func perform(operations: [Operation], completion: @escaping Completion)
}
```

This coordinator takes a set of operations as input argument, as well as a completion should be called when each operation has completed with an optional error. Depending on the implementation, these errors can either be in the order of execution or completely random.

Now let's specialize these protocols to get some power out of them. Let's start with a concurrent implementation.


### Concurrent operations

First, let's implement a simple concurrent coordinator that performs all operations at the same time:

```swift
class ConcurrentOperationCoordinator: OperationCoordinator {
    
    func perform(operations: [Operation], completion: @escaping Completion) {
        guard operations.count > 0 else { return completion([]) }
        var errors = [Error?]()
        operations.forEach {
            $0.perform { error in
                errors.append(error)
                let isComplete = errors.count == operations.count
                guard isComplete else { return }
                completion(errors)
            }
        }
    }
}
```

This coordinator triggers all operations and waits for all to complete, then call the completion with an unordered list of optional errors.

Using this coordinator is as simple as this:

```swift
class MyOperation: Operation {
    var error: Error?
    func perform(completion: Completion) { 
        completion(error) 
    }
}

let operations = [MyOperation(), MyOperation()]
let coordinator = ConcurrentOperationCoordinator()
coordinator.perform(operations: operations) { errors in
    print("All done")
}
```

We could also use this coordinator as an internal tool in other tools, where the operation setup is hidden from the external interface.


### Serial operations

Sometimes, concurrency is not an option, e.g. when the order of execution matters. For these cases, let's implement a simple serial coordinator:

```swift
class SerialOperationCoordinator: OperationCoordinator {
    
    func perform(operations: [Operation], completion: @escaping Completion) {
        performOperation(at: 0, in: operations, errors: [], completion: completion)
    }
    
    private func performOperation(at index: Int, in operations: [Operation], errors: [Error?], completion: @escaping Completion) {
        guard operations.count > index else { return completion(errors) }
        let operation = operations[index]
        operation.perform { (error) in
            let errors = errors + [error]
            self.performOperation(at: index + 1, in: operations, errors: errors, completion: completion)
        }
    }
}
```

Since it implements the same protocol as `ConcurrentOperationCoordinator`, you can just switch out the implementation in the example above:

```swift
class MyOperation: Operation {
    var error: Error?
    func perform(completion: Completion) { completion(error) }
}

let operations = [MyOperation(), MyOperation()]
let coordinator = SerialOperationCoordinator()
coordinator.perform(operations: operations) { errors in
    print("All done")
}
```

We now have two baisc ways of coordinating a set of parameterless operations. Let's taking this approach further, to build a bit more powerful tools.



## Operating on a collection

As a complement to anonymous operations, let's specify a protocol that describes how to perform an operation on a typed collection:

```swift
protocol CollectionOperation: AnyObject {
    
    associatedtype CollectionType
    typealias T = CollectionType
    typealias Completion = ([Error?]) -> ()
    
    func perform(on collection: [T], completion: @escaping Completion)
}
```

When you implement this protocol, just implement `perform(on:completion:)` and specify the `CollectionType` with a typealias.

Let's take this even further by creating more specialized protocols, that describe how to operate on single items and batches of items:

```swift
protocol ItemOperation: CollectionOperation {
    
    typealias ItemCompletion = (Error?) -> ()
    
    func perform(onItem item: T, completion: @escaping ItemCompletion)
}
```

```swift
protocol BatchOperation: CollectionOperation {
    
    typealias BatchCompletion = (Error?) -> ()
    
    var batchSize: Int { get }
    
    func perform(onBatch batch: [T], completion: @escaping BatchCompletion)
}
```

When you implement these protocols, just implement all functions and and specify the `CollectionType` with a typealias. For the item and batch operations, it's important that you call the item and batch completions when the operation completes for every item/batch, since various implementations will depend on it.


### Let's implement!

We now have three collection operation protocols, but still no implementations nor any real benefits. If we were to stop here, we would just have descriptions of how to operate on collections, items and batches, but would still have to implement everything ourselves.

Let's do something about that! Let's create even more specialized protocols that implement the coordination in various ways.


### Concurrent item operation

The simplest specialization we can make of the protocols above, is to create a protocol that concurrently performs an operation on all items in a collection:

```swift
protocol ConcurrentItemOperation: ItemOperation {}

extension ConcurrentItemOperation {
    
    func perform(on collection: [T], completion: @escaping Completion) {
        guard collection.count > 0 else { return completion([]) }
        var errors = [Error?]()
        collection.forEach {
            perform(onItem: $0) { error in
                errors.append(error)
                let isComplete = errors.count == collection.count
                guard isComplete else { return }
                completion(errors)
            }
        }
    }
}
``` 

This protocol looks a lot like the concurrent coordinator we created earlier. This is intentional, for reasons we'll soon see.

As you can see, this protocol implements `perform(on:completion:)` as a protocol extension. This means that if you implement this protocol instead of `ItemOperation`, you just have to implement `perform(onItem:completion:)`. This protocol will take care of concurrently performing the operation on all items.

Now we're getting somewhere! Your implementation just have to define how each item is handled, and any caller just have to call `perform(on:completion:)`.

Let's see if we can take this further.


### Concurrent batch operation

Using the same approach as above, it's very simple to create a similar protocol that operates on batches instead of single items:

```swift
protocol ConcurrentBatchOperation: BatchOperation {}

extension ConcurrentBatchOperation {
    
    func perform(on collection: [T], completion: @escaping Completion) {
        guard collection.count > 0 else { return completion([]) }
        var errors = [Error?]()
        let batches = collection.batched(withBatchSize: batchSize)
        batches.forEach {
            perform(onBatch: $0) { error in
                errors.append(error)
                let isComplete = errors.count == batches.count
                guard isComplete else { return }
                completion(errors)
            }
        }
    }
}
```

This protocol also implements `perform(on:completion:)` as a protocol extension. This means that if you implement this protocol instead of `BatchOperation`, you just have to implement `perform(onBatch:completion:)`. This protocol will take care of concurrently performing the operation on all batches.

Since both protocols implement `CollectionOperation`, they share the same public api, which means that you trigger item and batch operations in the same way.

Let's see if we can take this even further.


### Serial item operation

If your operation is asynchronous and the order of execution is important, you can't use concurrent operations, since a simple network delay could mess up the completion order. If you can't solve this with system design, you could execute your operations serially instead of concurrently.

Let's create a serial `ItemOperation` for these cases:

```swift
protocol SerialItemOperation: ItemOperation {}

extension SerialItemOperation {
    
    func perform(on collection: [T], completion: @escaping Completion) {
        perform(at: 0, in: collection, errors: [], completion: completion)
    }
    
    private func perform(at index: Int, in collection: [T], errors: [Error?], completion: @escaping Completion) {
        guard collection.count > index else { return completion(errors) }
        let object = collection[index]
        perform(onItem: object) { [weak self] error in
            let errors = errors + [error]
            self?.perform(at: index + 1, in: collection, errors: errors, completion: completion)
        }
    }
}
``` 

This protocol also implements `perform(on:completion:)` as a protocol extension. This means that if you implement this protocol instead of `ItemOperation`, you just have to implement `perform(onItem:completion:)`. This protocol will take care of serially performing the operation on all items. It will wait for each item operation to complete before it proceeds with the next.

Since `ConcurrentItemOperation` and `SerialItemOperation` have the same external interface, you can switch execution strategy by switching out the protocol your operation implements. If your operation implements `ConcurrentItemOperation` and you realize that it must be serial, just replace `ConcurrentItemOperation` with `SerialItemOperation`.

This gives you a lot of flexibility. You can call all collection operations in the same way, and easily make them behave differently by replacing protocols.

Lets wrap this up with a final protocol!


### Serial batch operation

Finally, using the same approach as above, it's very simple to create another protocol that operates on batches of items instead of single items:

```swift
protocol SerialBatchOperation: BatchOperation {}

extension SerialBatchOperation {
    
    func perform(on collection: [T], completion: @escaping Completion) {
        let batches = collection.batched(withBatchSize: batchSize)
        perform(at: 0, in: batches, errors: [], completion: completion)
    }
    
    private func perform(at index: Int, in batches: [[T]], errors: [Error?], completion: @escaping Completion) {
        guard batches.count > index else { return completion(errors) }
        let batch = batches[index]
        perform(onBatch: batch) { [weak self] error in
            let errors = errors + [error]
            self?.perform(at: index + 1, in: batches, errors: errors, completion: completion)
        }
    }
}
```

This protocol also implements `perform(on:completion:)` as a protocol extension. This means that if you implement this protocol instead of `BatchOperation`, you just have to implement `perform(onBatch:completion:)`. This protocol will take care of serially performing the operation on all batches. It will wait for each batch operation to complete before it proceeds with the next.

We now have four completely different collection operations that are externally interchangeable, which means that you call them all in the same way. This gives you the power to chain them together in flexible ways.

Let's put everything together in a short example.


### Examples

Let's build an imaginary image syncer that syncs images that have been taken while the user was offline:


```swift
class ImageSyncer: ConcurrentItemOperation {
    
    typealias CollectionType = UIImage
    
    func perform(onItem item: UIImage, completion: @escaping ItemCompletion) {
        syncImage(item) { error in
            completion(error)
        }
    }

    func syncImage(_ image: UIImage, completion: @escaping ItemCompletion) {
        // Implement this in some way :)
    }
}
```

You can use this image syncer like this:

```swift
let syncer = ImageSyncer()
let images = [offlineImage1, offlineImage2, ...]
syncer.perform(on: images) { errors in
    print("All done!")
}
```

This will sync all images concurrently and print "All done!" when it's done. If you want it to be serial, you just have to change which protocol it implements:

```swift
private class ImageSyncer: SerialItemOperation {
    
    // The rest can be left unchanged :)
}
```

Since the syncer is still a collection operation, you can still call it like this:

```swift
let syncer = ImageSyncer()
let images = [offlineImage1, offlineImage2, ...]
syncer.perform(on: images) { errors in
    print("All done!")
}
```

If your solution supports syncing batches of images instead of individual images, the syncer could implement `ConcurrentBatchOperation` instead:

```swift
class ImageSyncer: ConcurrentBatchOperation {
    
    typealias CollectionType = UIImage
    
    func perform(onBatch batch: [UIImage], completion: @escaping BatchCompletion) {
        syncImages(batch) { error in
            completion(error)
        }
    }

    func syncImages(_ images: [UIImage], completion: @escaping ItemCompletion) {
        // Implement this in some way :)
    }
}
```

The syncer is still a collection operation, so you can still call it like before.

Finally, if you want to perform this operation serially instead of concurrently, just replace `ConcurrentBatchOperation` with `SerialBatchOperation`:

```swift
class ImageSyncer: SerialBatchOperation {
    
    // The rest can be left unchanged :)
}
```

The syncer is still a collection operation, so you can still call it like before.

That's it! We have implemented an image syncer using the new operation protocols, and also changed how it operates with minimal changes!


## Final improvements

With these new collection operations in place, we can simplify the coordinators that we implemented earlier. They simply become:

```swift
class ConcurrentOperationCoordinator: OperationCoordinator, ConcurrentItemOperation {
    
    typealias CollectionType = Operation
    
    func perform(operations: [Operation], completion: @escaping Completion) {
        perform(on: operations, completion: completion)
    }
    
    func perform(onItem item: iExtra.Operation, completion: @escaping ItemCompletion) {
        item.perform(completion: completion)
    }
}


class SerialOperationCoordinator: OperationCoordinator, SerialItemOperation {
    
    typealias CollectionType = Operation
    
    func perform(operations: [Operation], completion: @escaping Completion) {
        perform(on: operations, completion: completion)
    }
    
    func perform(onItem item: iExtra.Operation, completion: @escaping ItemCompletion) {
        item.perform(completion: completion)
    }
}
```

We have now gone full circle and used the things we've created in various ways. I hope you find it useful.



## Conclusion

I hope you liked this post. If you decide to use the pattern, I would love to see some implementations.

Please note that this post does not claim that its implementations is in any way better than [PromiseKit]({{page.promisekit}}), [AwaitKit]({{page.awaitkit}}), [RxSwift]({{page.rxswift}}) and [ReactiveCocoa]({{page.reactivecocoa}}). They are very popular, but sometimes using them is not an option. My advice is to take a look at them and make your own decision.

The implementation in this post is just vanilla Swift with some abstractions and auto-implementations. There are no new keywords that doesn't already exist in Swift, no async/await, no promises, no observables. Just a bunch of tiny protocols. It could probably be improved to use GCD and extended in a bunch of ways, but I hope that you enjoyed the discussions we could have by not walking down that path.

I have pushed the source code to my personal iExtra library (open source, but I mainly maintain it for myself). If you want to try it out, you can find it [here]({{page.source}}).

