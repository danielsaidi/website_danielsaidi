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

Swift is an amazing language, but I find that it lacks good native support for coordinating async operations in a sophisticated way. In this post, I will look at existing libraries for handling this, then discuss a lightweight alternative.


## TL;DR

This is a pretty long article that discusses things in detail. If you want a more compact version, I have published a shorter one [here]({{page.tldr}}).



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

If we just want to coordinate a set of abstract operations, we can use a simple approach that involves operations and coordinators.

Let's start by defining an abstract `Operation`. Note that this name conflicts with `Foundation.Operation`, so you may have to specify it as `MyLib.Operation` or simply just import the library you need.

```swift
protocol Operation {
    
    typealias Completion = (Error?) -> ()
    
    func perform(completion: @escaping Completion)
}
```

To coordinate how operations are executed, let's define an `OperationCoordinator`:

```swift
protocol OperationCoordinator {
    
    typealias Completion = ([Error?]) -> ()
    
    func perform(operations: [Operation], completion: @escaping Completion)
}
```

This coordinator takes a set of operations as well as a completion that should be called when each operation has completed. Depending on the implementation, the complection errors (one optional per operation) can either be in order or random.

We can now implement coordinators that implement the coordinator protocol in various ways. Let's start with a concurrent one.


### Concurrent operations

Creating a concurrent operation coordinator is really easy:

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

This coordinator triggers all operations and waits for them all to complete with an optional error, then calls the completion with an unordered list of optional errors. Using this coordinator is very easy:

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

We could also use this coordinator as an internal tool in other classes, where the operations are hidden from the external interface.


### Serial operations

If concurrency is not an option, e.g. when the order of execution matters, we could use a serial coordinator instead:

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

Since this class implements the same protocol as the previous coordinator, you can just switch out the implementation in the example above:

```swift
let coordinator = SerialOperationCoordinator()
coordinator.perform(operations: operations) { errors in
    print("All done")
}
```

We now have two basic ways of coordinating multiple parameterless operations. It has its use, but I would prefer to work with collections in a more sophisticated ways, where this coordination is one tool of many.

Instead of working on anonymous operations, let's take the approach further. We will start off with some modeling before we get to the real benefits.



## Operating on a collection

As a complement to anonymous operations, let's create a protocol that describes how to perform an operation on a typed collection:

```swift
protocol CollectionOperation: AnyObject {
    
    associatedtype CollectionType
    typealias T = CollectionType
    typealias Completion = ([Error?]) -> ()
    
    func perform(on collection: [T], completion: @escaping Completion)
}
```

When you implement this protocol, just implement `perform(on:completion:)` and specify the `CollectionType` with a typealias.

Now let's create more specialized versions of this protocol, that describe how to operate on single items and batches of items:

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

When you implement these protocols, it's important that you call the item and batch completions when the operation completes for every item/batch. As you will see, various implementations will depend on these completions to be called.

We now have three protocols that desribe how to operate on collections, but no implementations nor any real benefits. If we were to stop here, we would just have descriptions of how to operate on collections, items and batches, but would have to implement everything ourselves. 

Let's do something about this, by creating even more specialized versions of these protocols.


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

This protocol looks a lot like the concurrent coordinator we created earlier. This is intentional, for reasons you'll soon see.

As you can see, this protocol implements `perform(on:completion:)` as a protocol extension. This means that if you implement this protocol instead of `ItemOperation`, you just have to implement `perform(onItem:completion:)`. This protocol will take care of concurrently performing the operation on all items.

Now we're getting somewhere! Your implementation just have to define how each item is handled, and any caller just have to call `perform(on:completion:)`.


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


### Serial item operation

If your operation is asynchronous and the order of execution is important, you can't use concurrent operations, since a simple network delay could mess up the completion order. The best way to solve such issues is to design your system to support concurrency, but if you can't do this, you could execute your operations serially instead of concurrently.

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

This protocol also implements `perform(on:completion:)` as a protocol extension. This means that if you implement this protocol instead of `ItemOperation`, you just have to implement `perform(onItem:completion:)`. This protocol will take care of serially performing the operation on all items, and will wait for each item operation to complete before it proceeds with the next.

Since `ConcurrentItemOperation` and `SerialItemOperation` have the same external interface, you can switch execution strategy by switching out the protocol your operation implements. If your operation implements `ConcurrentItemOperation` and you realize that it must be serial, just replace `ConcurrentItemOperation` with `SerialItemOperation`. This gives you a lot of flexibility. You can call all collection operations in the same way, and easily make them behave differently by replacing protocols.


### Serial batch operation

Finally, using the same approach as above, we can easily create another protocol that operates on batches of items instead of single items:

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

This protocol also implements `perform(on:completion:)` as a protocol extension. This means that if you implement this protocol instead of `BatchOperation`, you just have to implement `perform(onBatch:completion:)`. This protocol will take care of serially performing the operation on all batches, and will wait for each batch operation to complete before it proceeds with the next.


## Examples

We now have four completely different collection operations that are externally and internally interchangeable, which gives you a lot of freedom to chain them together in flexible ways. Let's put everything together in a short example.


Let's build an imaginary image syncer that syncs images that were taken while the user was offline:


```swift
class ImageSyncer: ConcurrentItemOperation {
    
    typealias CollectionType = UIImage
    
    func perform(onItem item: UIImage, completion: @escaping ItemCompletion) {
        syncImage(item) { error in
            completion(error)
        }
    }

    func syncImage(_ image: UIImage, completion: @escaping ItemCompletion) {
        // Implement this in some way and all completion when done :)
    }
}
```

You can use this image syncer like this, as an internal tool:

```swift
let syncer = ImageSyncer()
let images = [offlineImage1, offlineImage2, ...]
syncer.perform(on: images) { errors in
    print("All done!")
}
```

This will sync all images concurrently and print "All done!" when it's done. 

You could also make this syncer an internal tool, where the class that uses it provides it with images and just exposes a `syncOfflineImages()` function that you interact with, without talking operations.

If you'd like this syncer to be serial instead, you'd just have to change which protocol it implements:

```swift
private class ImageSyncer: SerialItemOperation {
    
    // The rest can be left unchanged :)
}
```

Since it's still a collection operation, you can still call it like this:

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

With these new collection operations in place, we can simplify the coordinators that we implemented earlier, so that they simply become:

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

