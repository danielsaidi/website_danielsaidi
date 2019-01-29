---
title:  "Coordinating async operations - TL;DR"
date:   2019-01-29 23:00:00 +0100
tags:   swift rxswift async-await awaitkit promisekit

full-version: http://danielsaidi.com/blog/2019/01/26/async-operations
source: https://github.com/danielsaidi/iExtra/tree/master/iExtra/Operations
promisekit: https://github.com/mxcl/PromiseKit
awaitkit: https://github.com/yannickl/AwaitKit
rxswift: https://github.com/ReactiveX/RxSwift
rxswift-post: http://danielsaidi.com/blog/2018/01/19/ditching-rxswift
reactivecocoa: https://github.com/ReactiveCocoa/ReactiveCocoa
---

This is a shorter version of my previous post on async operations. It focuses more on code and has less discussions than the [full version]({{page.full-version}}).


## Full version

The full version of this blog post contains more discussions, more extensive examples and also covers existing alternatives ([PromiseKit]({{page.promisekit}}), [AwaitKit]({{page.awaitkit}}), [RxSwift]({{page.rxswift}}) and [ReactiveCocoa]({{page.reactivecocoa}})) in more detail, including a discussion on why I don't use them. You can read it [here]({{page.full-version}}).



## Coordinating simple operations

If we just want to coordinate a set of operations, we can easily put together a parallell and a sequential approach that we can use to build even cooler things later on:

```swift
public protocol Operation {
    
    typealias Completion = (Error?) -> ()
    
    func perform(completion: @escaping Completion)
}
```

To coordinate how operations are executed, let's first create another protocol:

```swift
public protocol OperationCoordinator {
    
    typealias Completion = ([Error?]) -> ()
    
    func performOperations(_ operations: [Operation], completion: @escaping Completion)
}
```

### Parallel operations

Let's create a more specialized coordinator protocol that performs all operations in parallel:

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

If you implement this protocol, you don't have to write any more code. You get the entire coordination model for free. You could implement it as simple as this:

```swift
class MyOperation: Operation {
    var error: Error?
    func perform(completion: Completion) { completion(error) }
}

class MyCoordinator: ParallelOperationCoordinator {}

let operations = [MyOperation(), MyOperation()]
let coordinator = MyCoordinator()
coordinator.perform(operations) { errors in print("All done") }
```

We could also make a coordinator that is self contained, like this:

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

    public func synchronizeSomething(completion: ([Error?]) -> ()) {
        perform(syncOperations) { errors in
            completion(errors)
        }
    }
}

let coordinator = MyDataSyncer()
coordinator.performSynchronization { errors in print("All done") }
```

### Sequential operations

To perform sequential operations, we could create another protocol like this:

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

Implementing this would be identical to the parallel version above:

```swift
class MyOperation: Operation {
    var error: Error?
    func perform(completion: Completion) { completion(error) }
}

class MyCoordinator: SequentialOperationCoordinator {}

let operations = [MyOperation(), MyOperation()]
let coordinator = MyCoordinator()
coordinator.perform(operations) { errors in print("All done") }
```

The only difference is that the coordinator now implements `SequentialOperationCoordinator` instead of `ParallelOperationCoordinator`. The same goes for the data syncer:

```swift
class MyDataSyncer: SequentialOperationCoordinator {

    // The rest of the code is unchanged
}
```

We now have two ways to coordinate simple, parameterless operations. Now let's look at taking this approach to build even more powerful things.



## Operating on a collection

If we want to operate on something specific instead of something unknown as above, we could create another protocol for operations that operate on collections:

```swift
public protocol CollectionOperation: AnyObject {
    
    associatedtype CollectionType
    typealias T = CollectionType
    typealias Completion = ([Error?]) -> ()
    
    func performOperation(on collection: [T], completion: @escaping Completion)
}
```

When you implement this protocol, just implement `performOperation(on:completion:)` and specify the `CollectionType` with a typealias.

Now let's create more specialized versions of this protocol, that describe how to operate on single items and batches of items.


### Item operation

Let's extend `CollectionOperation` to describe an operation that operates on items in a collection:

```swift
public protocol ItemOperation: CollectionOperation {
    
    typealias ItemCompletion = (Error?) -> ()
    
    func performOperation(onItem item: T, completion: @escaping ItemCompletion)
}
```

When implementing this protocol, just implement `CollectionOperation` and `performOperation(onItem:completion:)`, then call it for every item in the collection.


### Batch operation

Let's also extend `CollectionOperation` to describe an operation that operates on batches of items from a collection:

```swift
public protocol BatchOperation: CollectionOperation {
    
    typealias BatchCompletion = (Error?) -> ()
    
    var batchSize: Int { get }
    
    func performOperation(onBatch batch: [T], completion: @escaping BatchCompletion)
}
```

When implementing this protocol, you must implement `CollectionOperation` and `performOperation(onBatch:completion:)`, then call it for every batch of items that is extracted from the collection.

Now let's create even more specialized protocols that implement the coordination.


### Parallel item operation

Let's create a protocol that performs an operation on all items in a collection in parallel:

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

This protocol implements `performOperation(on:completion:)`, which means that you just have to implement `performOperation(onItem:completion:)` if you implement it.
Your implementation just have to define how each item is handled.

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

This protocol also implements `performOperation(on:completion:)`, which means that you just have to implement `performOperation(onItem:completion:)` here as well.

Just like with `ParallelItemOperation`, your implementation just have to define how each item is handled, and callers just have to call `performOperation(...)`. 

Let's see if we can take this even further.


### Sequential item operation

If your operation is asynchronous and the order of execution is important, you must use sequential execution. Let's create another `ItemOperation` for this:

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

This protocol also implements `performOperation(on:completion:)`, which means that you just have to implement `performOperation(onItem:completion:)` here as well. It will wait for each item operation to complete before it proceeds with the next.

Lets wrap this up with a final protocol!


### Sequential batch operation

Using the same approach as above, it's very simple to create another sequential protocol that operates on batches of items instead of single items:

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

This protocol also implements `performOperation(on:completion:)`, which means that you just have to implement `performOperation(onItem:completion:)` here as well. It will wait for each batch operation to complete before it proceeds with the next.

Let's put this together in a short example.


### Examples

Let's build an imaginary image syncer that syncs images that have been taken while the user was offline:


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

You can instantiate and call it as such:

```swift
let syncer = ImageSyncer()
let images = [offlineImage1, offlineImage2, ...]
syncer.performOperation(on: images) { errors in
    print("All done!")
}
```

If you want to perform this operation sequentially instead, you just have to change which protocol the image syncer implements:

```swift
private class ImageSyncer: SequentialItemOperation {
    
    // The rest can be left unchanged :)
}
```

The syncer is still a collection operation, so you can still call it like this:

```swift
let syncer = ImageSyncer()
let images = [offlineImage1, offlineImage2, ...]
syncer.performOperation(on: images) { errors in
    print("All done!")
}
```

If your solution would support batches instead of individual images, the syncer could implement `ParallelBatchOperation` instead:

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

The syncer is still a collection operation, so you can still call it like before.

Finally, if you want to perform this operation sequentially instead of in parallel, just switch protocol again:

```swift
private class ImageSyncer: SequentialBatchOperation {
    
    // The rest can be left unchanged :)
}
```

The syncer is still a collection operation, so you can still call it like before.

That's it! We have implemented an image syncer, using the new operation protocols, and also changed how it operates with minimal changes in our code. Great job!


## Final improvements

With the collection operation protocols in place, we can simplify our earlier operation coordinations. If they implement these protocols, they simply become:

```swift
public protocol ParallelOperationCoordinator: OperationCoordinator, ParallelItemOperation where CollectionType == Operation {}

public extension ParallelOperationCoordinator {
    
    func performOperation(onItem item: iExtra.Operation, completion: @escaping ItemCompletion) {
        item.perform(completion: completion)
    }
    
    func performOperations(_ operations: [Operation], completion: @escaping Completion) {
        performOperation(on: operations, completion: completion)
    }
}

public protocol SequentialOperationCoordinator: OperationCoordinator, SequentialItemOperation where CollectionType == Operation {}

public extension SequentialOperationCoordinator {
    
    func performOperation(onItem item: iExtra.Operation, completion: @escaping ItemCompletion) {
        item.perform(completion: completion)
    }
    
    func performOperations(_ operations: [Operation], completion: @escaping Completion) {
        performOperation(on: operations, completion: completion)
    }
}
```

We now have a solution that uses itself extensively, and lets you use whatever parts you find useful. I hope you find it useful.



## Conclusion

I hope you liked this post. If you decide to use the pattern, I would love to see some implementations.

I have pushed the source code to my personal iExtra library (open source, but I mainly maintain it for myself). If you want to try it out, you can find it [here]({{page.source}}).

