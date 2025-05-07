---
title: Coordinating async operations in Swift
date:  2019-01-26 21:00:00 +0100
tags:  swift async
icon:  swift

tldr: http://danielsaidi.com/blog/2019/01/29/async-operations-bitesized
source: https://github.com/danielsaidi/iExtra/tree/master/iExtra/Operations
demo: https://github.com/danielsaidi/Demo_Async

awaitkit: https://github.com/yannickl/AwaitKit
promisekit: https://github.com/mxcl/PromiseKit
procedurekit: https://github.com/ProcedureKit/ProcedureKit
rxswift: https://github.com/ReactiveX/RxSwift
rxswift-post: http://danielsaidi.com/blog/2018/01/19/ditching-rxswift
reactivecocoa: https://github.com/ReactiveCocoa/ReactiveCocoa
---

Swift is an amazing language, but it currently lacks good support for coordinating async operations in a sophisticated way. In this post, I will look at ways to solve this.


## Update: 2021

As Swift now has support for async/await, very little in this post is still relevant. I'll keep it around as a historic document, for future archeologists to find.


## 3rd party alternatives

While .NET and JavaScript has native support for async/await, and Kotlin has coroutines, Swift relies on complicated foundation tools, 3rd party libraries, or custom solutions.

Let's look at some existing libraries that aims at solving this problem.


### PromiseKit

[PromiseKit]({{page.promisekit}}) gives you access to a `Promise` model that lets you code something like this:

```swift
firstly {
    makeRequest1()          // Returns a promise
}.then { result1 in
    makeRequest2(result1)   // Returns a promise
}.then { result2 in
    doSomethingElse()       // Returns a promise
}.catch { error in
    // Handle any error
}
``` 

I like this chaining of operations, but find PromiseKit to be too talky. Furthermore, you still jump in and out of blocks and have little flexibility in how to perform your operations.


### AwaitKit

[AwaitKit]({{page.awaitkit}}) extends PromiseKit with `async/await` that makes the code above look like this:

```swift
let result1 = try! await(makeRequest1())
let result2 = try! await(makeRequest2(result1))
try! doSomethingElse()
```

AwaitKit makes the promise-based code look synchronous, which is easier to write & read.

While the sample code uses `try!`, I'd recommend you to use `do/catch` to avoid crashes. Also note that with AwaitKit, you get an implicit dependency to PromiseKit as well.


### RxSwift / ReactiveCocoa

If promises and async/await doesn't appeal to you, take a look at observables. [RxSwift]({{page.rxswift}}) and [ReactiveCocoa]({{page.reactivecocoa}}) are two popular libraries for working with observables and streams of data. 

I had a hard time liking RxSwift, though (and haven't tried ReactiveCocoa) and wrote about my experience in a [post]({{page.rxswift-post}}).


### ProcedureKit

I found [ProcedureKit]({{page.procedurekit}}) after writing the first version of post. It seems well designed and well maintained, with nice documentations and great examples. It may be worth checking out.


## Building it yourself

What I dislike with using the libraries above, is that they can fundamentally change your architecture, and will make your code heavily depend on 3rd party dependencies.

If you are willing to make that choice, make it a *conscious* choice. However, if you want to keep your external dependencies down to a minimum and not fundamentally change your architecture, you can come a long way with some protocols and extensions.


## A protocol-based example

In the example below, I will create some tiny protocols to get a modular way of composing operations and coordinating the execution of multiple operations. 

I will not use `Grand Central Dispatch` (GCD) or `NSOperationQueue`, but may improve the approach to use these technologies later. 

For now, let's focus on the design and composition. The coordination implementation can change later, while keeping the external interfaces intact. Let's focus on the system design.


## Operation types

In this post, we'll be talking about *plain operations*, *collection operations*, *item operations* and *batch operations*, when we need them and how to compose and coordinate them. 

Let's begin by discussing the most basic operation - a plain, parameterless one.


## Basic operations

Let's start by defining a basic `Operation` protocol. It conflicts with `Foundation.Operation`, so you have to use `[MyLib].Operation` when referring to it, or only import its library.

```swift
public protocol Operation {
    
    typealias OperationCompletion = (Error?) -> ()
    
    func perform(completion: @escaping OperationCompletion)
}
```

This protocol can be implemented by anything that can be "performed" without parameters. It's basically just an abstract description of any kind of operation. 

Your operations can be as complex as you need, as long as they can be performed without parameters and completes with an optional error.

With this in place, we can now describe a basic way of coordinating many operations, by defining an `OperationCoordinator` protocol:

```swift
public protocol OperationCoordinator {
    
    typealias OperationCoordinatorCompletion = ([Error]) -> ()
    
    func perform(
        _ operations: [Operation], 
        completion: @escaping OperationCoordinatorCompletion
    )
}
```

An operation coordinator performs a set of operations and completes with a list of errors, which is empty if all operations complete without error.

Depending on the coordinator, the results and errors are returned in order or randomly. A serial coordinator returns things in order, while a concurrent returns in random order.

We can now create coordinator implementations that implement the protocol differently. 

Let's start with a concurrent operation coordinator:

```swift
class ConcurrentOperationCoordinator: OperationCoordinator {
    
    func perform(
        _ operations: [Operation], 
        completion: @escaping OperationCoordinatorCompletion
    ) {
        guard operations.count > 0 else { return completion([]) }
        var errors = [Error?]()
        operations.forEach {
            $0.perform { error in
                errors.append(error)
                let isComplete = errors.count == operations.count
                guard isComplete else { return }
                completion(errors.compactMap { $0 })
            }
        }
    }
}
```

This coordinator triggers all operations at once and waits for them all to complete. It then calls the main completion with an unordered list of errors, if any.

Using the coordinator is easy. In the code below, we define an operation that does nothing, then executes two such operations concurrently.

```swift
class MyOperation: Operation {
    func perform(completion: @escaping (Error?) -> ()) {
        completion(nil) 
    }
}

let operations = [MyOperation(), MyOperation()]
let coordinator = ConcurrentOperationCoordinator()
coordinator.perform(operations) { errors in
    print("All done")
}
```

If concurrency is not an option, we can easily create a serial operation coordinator as well:

```swift
class SerialOperationCoordinator: OperationCoordinator {
    
    func perform(
        _ operations: [Operation], 
        completion: @escaping OperationCoordinatorCompletion
    ) {
        performOperation(
            at: 0, 
            in: operations, 
            errors: [], 
            completion: completion
        )
    }
    
    private func performOperation(
        at index: Int, 
        in operations: [Operation], 
        errors: [Error?], 
        completion: @escaping OperationCoordinatorCompletion
    ) {
        if index >= operations.count { return completion(errors.compactMap { $0 }) }
        let operation = operations[index]
        operation.perform { error in
            let errors = errors + [error]
            self.performOperation(
                at: index + 1,
                in: operations,
                errors: errors,
                completion: completion
            )
        }
    }
}
```

This coordinator starts by performing the first operation, then wait for it to complete before moving on to the next. It then calls the main completion with an ordered list of errors, if any.

Since the serial coordinator implements the same protocol as the concurrent one, you can just switch out the implementation in the example above:

```swift
let operations = [MyOperation(), MyOperation()]
let coordinator = SerialOperationCoordinator()
coordinator.perform(operations) { errors in
    print("All done")
}
```

We now have two basic ways of coordinating multiple operations. This approach is already useful, but lacks granular control over how we can describe and compose operations.

Let's take this approach further by looking at how we can operate on collections of items.

But first, note that you don't have to use coordinators directly. They can be internal tools for other classes, where the operation concept is hidden from the external interface. 

For instance, consider an `DataSyncer` protocol with a `syncData()` function. Although the protocol is clean, its implementations can still use operations and coordinators.



## Operating on a collection of items

While parameterless operations are great, since they can do anything, we can define more operation types to give us more intricate control.

For instance, consider these operations:

```swift
public protocol OperationItemTypeProvider {
    
    associatedtype OperationItemType
}
```

```swift
public protocol CollectionOperation: OperationItemTypeProvider {
    
    typealias CollectionCompletion = ([Error]) -> ()
    
    func perform(onCollection collection: [OperationItemType], completion: @escaping CollectionCompletion)
}
```

```swift
public protocol ItemOperation: OperationItemTypeProvider {
    
    typealias ItemCompletion = (Error?) -> ()
    
    func perform(onItem item: OperationItemType, completion: @escaping ItemCompletion)
}
```

```swift
public protocol BatchOperation: OperationItemTypeProvider {
    
    typealias BatchCompletion = (Error?) -> ()
    
    func perform(onBatch batch: [OperationItemType], completion: @escaping BatchCompletion)
}
```

These protocols describe how you can operate on items, collections, and batches. When you implement them, you must define `OperationItemType` and implement `perform`.

We now have more detailed protocols, but (so far) no real benefits. If we were to stop here, we would have to implement everything ourselves. 

Let's fix this by creating more specialized protocols that provide specific coordination logic.


### Concurrent operations

To add specialized logic, let's create a `CollectionOperation` and `ItemOperation` composite that provides us with a concurrent collection operation implementation:

```swift
public protocol ConcurrentCollectionItemOperation: CollectionOperation, ItemOperation {}

public extension ConcurrentCollectionItemOperation {
    
    func perform(onCollection collection: [OperationItemType], completion: @escaping CollectionCompletion) {
        guard collection.count > 0 else { return completion([]) }
        var errors = [Error?]()
        collection.forEach {
            perform(onItem: $0) { error in
                errors.append(error)
                let isComplete = errors.count == collection.count
                guard isComplete else { return }
                completion(errors.compactMap { $0 })
            }
        }
    }
}
``` 

This protocol now provides a `perform(onCollection:)` implementation that performs the operation concurrently on each item in the collection.

This means that we only have to implement `perform(onItem:)` when implementing this protocol, since the operation coordination is already handled by `perform(onCollection:)`.

Now we're getting somewhere! This actually brings us some real power, and makes it a lot easier to implement a concurrent operation, since the coordination is already implemented.

Using this approach, we can create a protocol that operates on batches instead of items:

```swift
public protocol ConcurrentCollectionBatchOperation: CollectionOperation, BatchOperation {
    
    var batchSize: Int { get }
}

public extension ConcurrentCollectionBatchOperation {
    
    func perform(onCollection collection: [OperationItemType], completion: @escaping CollectionCompletion) {
        guard collection.count > 0 else { return completion([]) }
        var errors = [Error?]()
        let batches = collection.batched(withBatchSize: batchSize)
        batches.forEach {
            perform(onBatch: $0) { error in
                errors.append(error)
                let isComplete = errors.count == batches.count
                guard isComplete else { return }
                completion(errors.compactMap { $0 })
            }
        }
    }
}
```

This protocol also implements `perform(onCollection:)`, but does so by chopping up the collection in batches, then performs the operation concurrently on every batch.

Since both protocols implement `CollectionOperation`, they are interchangeable. This means that we can use them in the same way, but either operate on items or batches.


### Serial operation

You can't use concurrent operations if the execution order matters. While it's best to design your system for concurrency, you can use serial operations if this is not possible.

Let's create serial variants of the item and batch operations above, to show how easy it is. 

Let's start with a serial item operation:

```swift
public protocol SerialCollectionItemOperation: CollectionOperation, ItemOperation {}

public extension SerialCollectionItemOperation {
    
    func perform(
        onCollection collection: [OperationItemType], 
        completion: @escaping CollectionCompletion
    ) {
        perform(at: 0, in: collection, errors: [], completion: completion)
    }
}

private extension SerialCollectionItemOperation {
    
    func perform(
        at index: Int, 
        in collection: [OperationItemType], 
        errors: [Error], 
        completion: @escaping CollectionCompletion
    ) {
        guard collection.count > index else { return completion(errors) }
        let object = collection[index]
        perform(onItem: object) { error in
            let errors = errors + [error].compactMap { $0 }
            self.perform(
                at: index + 1, 
                in: collection, 
                errors: errors, 
                completion: completion
            )
        }
    }
}
``` 

Just as the concurrent one, this protocol implements `perform(onCollection:)`, but does so serially instead of concurrently. 

When you implement this protocol, you only have to implement `perform(onItem:)`, since the operation coordination is already handled.

This protocol is interchangeable with `ConcurrentCollectionItemOperation`, since they have the same external interface. This means that if you have a concurrent implementation, you can make it serial just by letting it implement `SerialCollectionItemOperation`.

We can easily create a serial batch operation as well:

```swift
public protocol SerialCollectionBatchOperation: CollectionOperation, BatchOperation {
    
    var batchSize: Int { get }
}

public extension SerialCollectionBatchOperation {
    
    func perform(
        onCollection collection: [OperationItemType], 
        completion: @escaping CollectionCompletion
    ) {
        let batches = collection.batched(withBatchSize: batchSize)
        perform(at: 0, in: batches, errors: [], completion: completion)
    }
}

private extension SerialCollectionBatchOperation {
    
    func perform(
        at index: Int, 
        in batches: [[OperationItemType]], 
        errors: [Error], 
        completion: @escaping CollectionCompletion
    ) {
        guard batches.count > index else { return completion(errors) }
        let batch = batches[index]
        perform(onBatch: batch) { error in
            let errors = errors + [error].compactMap { $0 }
            self.perform(
                at: index + 1, 
                in: batches, 
                errors: errors, 
                completion: completion
            )
        }
    }
}
```

This protocol implements `perform(onCollection:)`, but serially instead of concurrently. If you implement it, you only have to implement `perform(onBatch:)` to determine how each batch of items should be handled. 

This operation is interchangeable with `ConcurrentCollectionBatchOperation`, since they share the same external interface. 

This approach gives you a lot of flexibility. You can call all operations the same way, and can easily make them behave differently by replacing which protocols they implement.


## Final improvements

With these operations in place, we can simplify the coordinators we implemented earlier. 

Looking at the code, it's obvious that they share a bunch of logic with the operations we just created. This means that we could describe them in another way:

```swift
public class ConcurrentOperationCoordinator: OperationCoordinator, ConcurrentCollectionItemOperation {
    
    public init() {}
    
    public typealias OperationItemType = Operation
    
    public func perform(_ operations: [Operation], completion: @escaping CollectionCompletion) {
        perform(onCollection: operations, completion: completion)
    }
    
    public func perform(onItem item: Operation, completion: @escaping ItemCompletion) {
        item.perform(completion: completion)
    }
}
```

```swift
public class SerialOperationCoordinator: OperationCoordinator, SerialCollectionItemOperation {

    public init() {}

    public typealias OperationItemType = Operation

    public func perform(_ operations: [Operation], completion: @escaping CollectionCompletion) {
        perform(onCollection: operations, completion: completion)
    }

    public func perform(onItem item: Operation, completion: @escaping ItemCompletion) {
        item.perform(completion: completion)
    }
}
```

The coordinators are basically just item operations, where the item type is `Operation`. This means that we can rewrite them as above, using a lot of the various operations.



## Conclusion

I hope you liked this post. If you try this approach, I'd love to hear how things worked out.

This post doesn't claim that the coordinator approach is better than [PromiseKit]({{page.promisekit}}), [AwaitKit]({{page.awaitkit}}), [RxSwift]({{page.rxswift}}) and [ReactiveCocoa]({{page.reactivecocoa}}). They are all very popular, just not for me. 

This coordinator approach uses plain Swift with some protocols and implementations, and doesn't introduce any new keywords that don't already exist, no async/await, no promises, no observables. It's basically just a bunch of tiny protocols. 

The implementations could be improved with GCD and extended in a bunch of ways, but I hope you enjoyed the discussions we could have by not walking down that path. 

I have pushed the coordinator source code to [this repository]({{page.source}}) if you would like to try it out.