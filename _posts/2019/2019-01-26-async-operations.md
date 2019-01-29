---
title:  "Coordinating async operations"
date:   2019-01-26 21:00:00 +0100
tags:	swift rxswift async-await awaitkit promisekit

source: https://github.com/danielsaidi/iExtra/tree/master/iExtra/Operations
promisekit: https://github.com/mxcl/PromiseKit
awaitkit: https://github.com/yannickl/AwaitKit
rxswift: https://github.com/ReactiveX/RxSwift
rxswift-post: http://danielsaidi.com/blog/2018/01/19/ditching-rxswift
reactivecocoa: https://github.com/ReactiveCocoa/ReactiveCocoa
---

Swift is an amazing language, but I find that it lacks good native support for coordinating multiple async operations in a sophisticated way. In this post, I will look at existing libraries for handling this, then discuss a lightweight alternative.



## Existing alternatives

While languages like .NET and JavaScript has native support for async/await, Swift developers must rely on 3rd party libraries or build their own solution. Let's look at some open source libraries that aims at simplifying working with async operations.

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

I think that the promise-based chaining of async operations is nice, but find PromiseKit to be too talky. Furthermore, you'll still be jumping in and out of blocks.

### AwaitKit

[AwaitKit]({{page.awaitkit}}) extends PromiseKit with an `async/await` model that makes the code above look something like this:


```swift
let result1 = try! await(makeRequest1())
let result2 = try! await(makeRequest2(result1))
doSomethingElse()
```

In my opinion, this is much nicer. It removes the blocks altogether and makes the code look synchronous, which is both easier to write and to read. The only thing that's strange with AwaitKit's sample code, is the use of `try!`, as above. If you want to handle any error, just wrap the code above in a `do/catch` instead.

Keep in mind that if you decide to go with AwaitKit, you will implicitly get a depencendy to PromiseKit as well, since AwaitKit uses PromiseKit under the hood.

### RxSwift / ReactiveCocoa

If the promise model doesn't appeal to you, have a look at observables. [RxSwift]({{page.rxswift}}) and [ReactiveCocoa]({{page.reactivecocoa}}) are two popular observable-based libraries, that may be worth checking out. I had a hard time liking RxSwift, though (and haven't tried ReactiveCocoa). If you want to read more about why, please see the original post [here]({{page.rxswift-post}}).



## Building it yourself

What I dislike with the libraries above, is that they fundamentally change your architecture. Using async/await or rx will affect your entire code base and make your code heavily depend on 3rd party dependencies.

If you are willing to make that choice, give it a try! However, if you want to keep your external dependencies down to a minimum and avoid fundamentally change your architecture, you can come a long way with encapsulation, abstractions and some custom code.

In the examples below, I will combine a couple of minimal protocols to get two lightweight, modular ways of coordinating the execution of multiple async operations.



## Coordinating simple operations

If we just want to coordinate a set of operations, we can easily put together a parallell and a sequential approach that we can use to build even cooler things later on.

Let's start by defining a simple `Operation` protocol. Note that this name conflicts with `Foundation.Operation`, so just import the library that contains it for any class that implements `Operation` or specify it as `YourLibrary.Operation`.

```swift
public protocol Operation {
    
    typealias Completion = (Error?) -> ()
    
    func perform(completion: @escaping Completion)
}
```

Simple, right? An operation can be anything that implements `perform { optionalError in }`. We can now describe a protocol that can be implemented by anything that can perform a set of operations:

```swift
public protocol OperationCoordinator {
    
    typealias Completion = ([Error?]) -> ()
    
    func performOperations(_ operations: [Operation], completion: @escaping Completion)
}
```

### Parallel operations

Now, let's create a specialized protocol, that implements `performOperations` to perform all operations in parallel:

```swift
public protocol ParallelOperationCoordinator: OperationCoordinator {}

public extension ParallelOperationCoordinator {
    
    func performOperations(_ operations: [Operation], completion: @escaping Completion) {
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

I'm not sure if this should be a class or a protocol as above, but given the rule of composition over inheritance, I think it's nice to get this logic if you just implement `ParallelOperationCoordinator`.

As you can see above, this protocol will triggers all operations, then wait for all to complete, gather any errors (which will not be guaranteed to be in original order) and call the main completion.

You could implement this as such:

```swift
class MyOperation: Operation {
    var error: Error?
    func perform(completion: Completion) { completion(error) }
}

class MyCoordinator: ParallelOperationCoordinator {}

let operations = [MyOperation(), MyOperation()]
let coordinator = MyCoordinator()
coordinator.perform(operations) { print("All done") }
```

Without any more code, we can now create coordinators that perform parallel operations. We could also make the coordinator self contained like this:

```swift
class SyncOperation: Operation {
    var error: Error?
    func perform(completion: Completion) { 
        doSomeAsyncTask { error in
            completion(error) 
        }
    }
}

class MyDataSyncer: ParallelOperationCoordinator {

    private var syncOperations: [Operation] {
        return anySyncOperations
    }

    public func performSynchronization(completion: ([Error?]) -> ()) {
        perform(syncOperations) { errors in
            completion(errors)
        }
    }
}

let coordinator = MyDataSyncer()
coordinator.performSynchronization { print("All done") }
```

### Sequential operations

If we would like to perform operations sequentially instead of in parallel, we could create another protocol that implements `performOperations` in a sequential way:

```swift
public protocol SequentialOperationCoordinator: OperationCoordinator {}

public extension SequentialOperationCoordinator {
    
    func performOperations(_ operations: [Operation], completion: @escaping Completion) {
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

Implememting this would be identical:

```swift
class MyOperation: Operation {
    var error: Error?
    func perform(completion: Completion) { completion(error) }
}

class MyCoordinator: SequentialOperationCoordinator {}

let operations = [MyOperation(), MyOperation()]
let coordinator = MyCoordinator()
coordinator.perform(operations) { print("All done") }
```

Given the `MyDataSyncer` above, all you'd have to do to make is sequential, is just to replace the protocol it implements:

```swift
class MyDataSyncer: SequentialOperationCoordinator {

    // The rest of the code is unchanged
}
```

We now have two ways to coordinate simple, parameterless operations. However, we can use the same approach to create other protocols that give us even more power.



## Operating on a collection

If we want an instance to operate on something specific instead of something implicit and unknown, like the operation above, we could create another protocol for operations that operate on collections of "things", like strings, ints, cars, operations etc.

First, let's define a protocol that describes how to operate on a collection:

```swift
public protocol CollectionOperation: AnyObject {
    
    associatedtype CollectionType
    typealias T = CollectionType
    typealias Completion = ([Error?]) -> ()
    
    func performOperation(on collection: [T], completion: @escaping Completion)
}
```

There's nothing special about this. When you implement this protocol, just implement `performOperation(on:completion:)` and specify `CollectionType` with a typealias. However, it's not that helpful, since it's just...a description. We must do some more coding to get some real benefits out of this.

If we consider the concept of "performing an operation on a collection of items", there are several ways to do this, for instance:

* Perform the operation on the entire collection
* Perform the operation on individual items in the collection
* Perform the operation on batches of items from the collection

`CollectionOperation` already covers the first case, although it does not provide an implementation. For the other two cases, let's create some specialized versions of the protocol.


### Item operation

The task of "performing an operation on individual items in a collection" could be described with a protocol that extends `CollectionOperation`, as such:

```swift
public protocol ItemOperation: CollectionOperation {
    
    typealias ItemCompletion = (Error?) -> ()
    
    func performOperation(onItem item: T, completion: @escaping ItemCompletion)
}
```

When implementing this protocol, you must implement `CollectionOperation` as well as `performOperation(onItem:completion:)`, then call it for every item in the collection. It's important to call the item completion block when the operation finishes for every item, since coordinations will depend on it.


### Batch operation

The task of "performing an operation on batches of items from a collection" could be described with another protocol that also extends `CollectionOperation`:

```swift
public protocol BatchOperation: CollectionOperation {
    
    typealias BatchCompletion = (Error?) -> ()
    
    var batchSize: Int { get }
    
    func performOperation(onBatch batch: [T], completion: @escaping BatchCompletion)
}
```

When implementing this protocol, you must implement `CollectionOperation` as well as `performOperation(onItem:completion:)`, then call it for every batch of items that is extracted from the collection.


### Still no power?

We now have three collection operation protocols, but still no implementations nor any real benefits. If we were to stop here, we would just have descriptions of how to operate on collections, items and batches, but would still have to implement everything ourselves.

Let's do something about that! Let's create even more specialized protocols that implement the most critical part - how to coordinate the operation.


### Parallel item operation

The simplest specialization we can make of the protocols above, is to create a protocol that performs an operation on all items in a collection parallel, like this:

```swift
public protocol ParallelItemOperation: ItemOperation {}

public extension ParallelItemOperation {
    
    public func performOperation(on collection: [T], completion: @escaping Completion) {
        guard collection.count > 0 else { return completion([]) }
        var errors = [Error?]()
        collection.forEach {
            performOperation(onItem: $0) { error in
                errors.append(error)
                let isComplete = errors.count == collection.count
                guard isComplete else { return }
                completion(errors)
            }
        }
    }
}
``` 

This protocol implements `performOperation(on:completion:)` in a protocol extension, which means that you just have to implement `performOperation(onItem:completion:)` when you implement it.

Now we're getting somewhere! This actually gives us some power. Your implementation just have to define how each item is handled, and callers just have to care about calling `performOperation(on:completion:)` with a collection.

Let's see if we can take this further.


### Parallel batch operation

Using the same approach as above, it's very simple to create a similar protocol that operates on batches instead of single items:

```swift
public protocol ParallelBatchOperation: BatchOperation {}

public extension ParallelBatchOperation {
    
    public func performOperation(on collection: [T], completion: @escaping Completion) {
        guard collection.count > 0 else { return completion([]) }
        var errors = [Error?]()
        let batches = collection.batched(withBatchSize: batchSize)
        batches.forEach {
            performOperation(onBatch: $0) { error in
                errors.append(error)
                let isComplete = errors.count == batches.count
                guard isComplete else { return }
                completion(errors)
            }
        }
    }
}
```

This protocol also implements `performOperation(on:completion:)` in a protocol extension, which means that you just have to implement `performOperation(onItem:completion:)` when you implement it.

Just like `ParallelBatchOperation`, your implementation just have to define how each item is handled, and callers just have to care about calling `performOperation(on:completion:)` with a collection. 

Since both protocols implement `CollectionOperation`, they're also externally interchangable, which means that you can trigger parallel item and batch operations in the same way.

Let's see if we can take this even further.


### Sequential item operation

If your operation is asynchronous and the order of execution is important, you can't use parallel operations, since a simple network delay could mess up the completion order. The best way to handle this is to design your solution, so that the order of execution is irrelevant. However, if you can't do this, you must execute your operations sequentially instead of in parallel.

Let's create another `ItemOperation`, that performs an operation sequentially instead:

```swift
public protocol SequentialItemOperation: ItemOperation {}

public extension SequentialItemOperation {
    
    func performOperation(on collection: [T], completion: @escaping Completion) {
        performOperation(at: 0, in: collection, errors: [], completion: completion)
    }
    
    private func performOperation(at index: Int, in collection: [T], errors: [Error?], completion: @escaping Completion) {
        guard collection.count > index else { return completion(errors) }
        let object = collection[index]
        performOperation(onItem: object) { [weak self] error in
            let errors = errors + [error]
            self?.performOperation(at: index + 1, in: collection, errors: errors, completion: completion)
        }
    }
}
``` 

This protocol also implements `performOperation(on:completion:)` in a protocol extension, which means that you just have to implement `performOperation(onItem:completion:)` when you implement it. It will wait for each item operation to complete before it proceeds with the next.

Now things are getting interesting! Since `ParallelItemOperation` and `SequentialItemOperation` has the same external interface, you can switch execution strategy by switching out the protocol your operation implements. If your sync operation implements `ParallelItemOperation` and you realize that it must be sequential, just replace `ParallelItemOperation` with `SequentialItemOperation`.

This gives you a lot of flexibility. You can call all collection operations in the same way, and if you want them to behave differently, you just let them implement the most fitting protocol.

Lets wrap this up with a final protocol!


### Sequential batch operation

Finally, using the same approach as above, it's very simple to create another sequential protocol that operates on batches of items instead of single items:

```swift
public protocol SequentialBatchOperation: BatchOperation {}

public extension SequentialBatchOperation {
    
    func performOperation(on collection: [T], completion: @escaping Completion) {
        let batches = collection.batched(withBatchSize: batchSize)
        performOperation(at: 0, in: batches, errors: [], completion: completion)
    }
    
    private func performOperation(at index: Int, in batches: [[T]], errors: [Error?], completion: @escaping Completion) {
        guard batches.count > index else { return completion(errors) }
        let batch = batches[index]
        performOperation(onBatch: batch) { [weak self] error in
            let errors = errors + [error]
            self?.performOperation(at: index + 1, in: batches, errors: errors, completion: completion)
        }
    }
}
```

This protocol also implements `performOperation(on:completion:)` in a protocol extension, which means that you just have to implement `performOperation(onItem:completion:)` when you implement it. It will wait for each batch operation to complete before it proceeds with the next.

We now have four different collection operations, that are externally interchangeable. Internally, two are item operations and two are batch operations. This is beneficial for each implementation, since you can switch from sequential to parallel execution by just changing protocol implementation.

Let's put this together in a short example.


### Examples

Let's look at how we could implement these protocols. Let's build an imaginary image syncer that syncs images that has been taken while the user is offline:


```swift
class ImageSyncer: ParallelItemOperation {
    
    typealias CollectionType = UIImage
    
    func performOperation(onItem item: UIImage, completion: @escaping ItemCompletion) {
        syncImagesInSomeWay(item) { error in
            completion(error)
        }
    }
}
```

This syncer will iterate over a collection of images and sync each image individually. You can instantiate and call it as such:

```swift
let syncer = ImageSyncer()
let images = [offlineImage1, offlineImage2, ...]
syncer.performOperation(on: images) { errors in
    print("All done!")
}
```

This will sync all images in parallel, then print "All done!" when it finishes. 

If you for some reason want to perform this operation sequentially instead, you just have to change which protocol the image syncer implements, like this:

```swift
private class ImageSyncer: SequentialItemOperation {
    
    // The rest can be left unchanged :)
}
```

Since the syncer is still a collection operation, the external interface is unchanged, so you can still call it as such:

```swift
let syncer = ImageSyncer()
let images = [offlineImage1, offlineImage2, ...]
syncer.performOperation(on: images) { errors in
    print("All done!")
}
```

If your solution would support syncing images in parallel batches instead of individually, the syncer could implement `ParallelBatchOperation` instead:

```swift
private class ImageSyncer: ParallelBatchOperation {
    
    typealias CollectionType = UIImage
    
    func performOperation(onBatch batch: [UIImage], completion: @escaping BatchCompletion) {
        syncImagesInSomeWay(batch) { error in
            completion(error)
        }
    }
}
```

Since the syncer is still a collection operation, the external interface is unchanged, so you can still call it as such:

```swift
let syncer = ImageSyncer()
let images = [offlineImage1, offlineImage2, ...]
syncer.performOperation(on: images) { errors in
    print("All done!")
}
```

Finally, if you for some reason want to perform this batch-based operation sequentially instead of in parallel, you just have to switch which protocol:

```swift
private class ImageSyncer: SequentialBatchOperation {
    
    // The rest can be left unchanged :)
}
```

Since the syncer is still a collection operation, the external interface is unchanged, so you can still call it as such:

```swift
let syncer = ImageSyncer()
let images = [offlineImage1, offlineImage2, ...]
syncer.performOperation(on: images) { errors in
    print("All done!")
}
```

And that's about it. We have now implemented an image syncer, using the new operation protocols, and also changed how it operates with minimal changes in our code.


## Conclusion

In this post, we have looked at some popular libraries for working with async operations in more sophisticated ways than just using completion blocks. [PromiseKit]({{page.promisekit}}), [AwaitKit]({{page.awaitkit}}), [RxSwift]({{page.rxswift}}) and [ReactiveCocoa]({{page.reactivecocoa}}) are very popular libraries, that I however find affect too much of the code base and have too many side-effects. My advice, is that you have a look at them and make your own decision.

We then implemented a lightweight operation model from scratch, which can be used to coordinate async operations without affecting your code style at all. It's just vanilla Swift with some abstractions and auto-implementations. There are no new keywords that doesn't already exist in Swift, no async/await, no promises, no observables. Just seven tiny protocols.

If you want to take a look at the source code, I have posted it as part of my private-ish iExtra library (open source, but I mainly maintain it for myself). You can find the source code [here]({{page.source}}).