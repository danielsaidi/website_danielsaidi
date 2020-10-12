---
title: Coordinating async operations
date:  2019-01-26 21:00:00 +0100
tags:  swift rxswift
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

Swift is an amazing language, but I find that it lacks good native support for coordinating async operations in sophisticated ways. In this post, I will look at existing libraries for solving this problem, then discuss a lightweight alternative and show you how to implement it with a couple of simple protocols and implementations.


## 3rd party alternatives

While languages like .NET and JavaScript has native support for async/await and Kotlin has coroutines, Swift developers must rely on 3rd party libraries or build their own solution for working with async operations. Let's look at a couple of existing libraries.


### PromiseKit

[PromiseKit]({{page.promisekit}}) is a nice library that gives you access to a `Promise` model that makes your code look something like this:

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

I like this chaining of async operations, but find PromiseKit to be too talky. Furthermore, you will still be jumping in and out of blocks and have little flexibility in how to perform your operations.


### AwaitKit

[AwaitKit]({{page.awaitkit}}) extends PromiseKit with an `async/await` model that makes the code above look something like this:

```swift
let result1 = try! await(makeRequest1())
let result2 = try! await(makeRequest2(result1))
try! doSomethingElse()
```

AwaitKit makes the promise-based code look synchronous, which is much easier to write and read. The only thing I find strange with their sample code, is that they use `try!`. I'd recommend you to use `do/catch` instead, to avoid crashes. Also note that if you go with AwaitKit, you will implicitly get a depencendy to PromiseKit as well.


### RxSwift / ReactiveCocoa

If promises and async/await doesn't appeal to you, you could take a look at observables. [RxSwift]({{page.rxswift}}) and [ReactiveCocoa]({{page.reactivecocoa}}) are two popular libraries for working with observables and streams of data. I had a hard time liking RxSwift, though (and haven't tried ReactiveCocoa) and wrote about my experience in a [blog post]({{page.rxswift-post}}) a while ago.


### ProcedureKit

I found [ProcedureKit]({{page.procedurekit}}) after writing the first version of post. It seems well designed and well maintained, with nice documentations and great examples. Not a lot of stars, but it's been going strong for several years, so it may be worth checking out.



## Building it yourself

What I dislike with blindly going for one of the libraries above, is that they can fundamentally change your architecture. Using them will greatly affect your code base if you let it, and will make your code heavily depend on 3rd party dependencies.

If you are willing to make that choice, make it a conscious choice and give them a try! However, if you want to keep your external dependencies down and not fundamentally change your architecture, you can come a long way with some protocols, extensions and implementations.

In the examples below, I will create a set of tiny protocols to get a modular way of composing operations and coordinating the execution of multiple operations. I will not use `Grand Central Dispatch` (GCD) or `NSOperationQueue`, but may improve the approach to use these technologies later. 

For now, however, the most important aspect for me of this post is to talk *design* and *composition*. How the actual coordination is implemented is an implementation detail, that can change later on while keeping the external interfaces intact. So for now, let's focus on the discussion and design.


## Operation types

In this post, we'll be talking about *plain operations*, *collection operations*, *item operations* and *batch operations*, as well as when we need them and how to compose and coordinate them. We'll begin by discussing the most basic operation - a plain, parameterless one.


## Basic operations

Let's start by defining a basic `Operation` protocol. This name conflicts with `Foundation.Operation`, so you may have to use `[MyLib].Operation` when referring to it, or only import Operation's library.

```swift
public protocol Operation {
    
    typealias OperationCompletion = (Error?) -> ()
    
    func perform(completion: @escaping OperationCompletion)
}
```

This protocol can be implemented by anything that can be "performed" without any parameters. It's basically just an abstract description of any kind of operation. Your operations can be as complex as you need them to be, as long as they can be performed without parameters and completes with an optional error.

With this in place, we can now describe a basic way of coordinating many operations, by defining an `OperationCoordinator` protocol:

```swift
public protocol OperationCoordinator {
    
    typealias OperationCoordinatorCompletion = ([Error]) -> ()
    
    func perform(_ operations: [Operation], completion: @escaping OperationCoordinatorCompletion)
}
```

An operation coordinator performs a set of operations and completes with a list of errors, which can be empty if all operations complete without error. Depending on the coordinator, these errors can be in order of execution or completely random.

We can now create coordinators that implement this protocol in various ways. Let's start with a concurrent one:

```swift
class ConcurrentOperationCoordinator: OperationCoordinator {
    
    func perform(_ operations: [Operation], completion: @escaping OperationCoordinatorCompletion) {
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

This coordinator triggers all operations at once and waits for all to complete, then calls the main completion with an unordered list of errors. 

Using it is very easy:

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

If concurrency is not an option, e.g. when the operation execution order matters, we can create a serial coordinator just as easily:

```swift
class SerialOperationCoordinator: OperationCoordinator {
    
    func perform(_ operations: [Operation], completion: @escaping OperationCoordinatorCompletion) {
        performOperation(at: 0, in: operations, errors: [], completion: completion)
    }
    
    private func performOperation(at index: Int, in operations: [Operation], errors: [Error?], completion: @escaping OperationCoordinatorCompletion) {
        guard operations.count > index else { return completion(errors.compactMap { $0 }) }
        let operation = operations[index]
        operation.perform { (error) in
            let errors = errors + [error]
            self.performOperation(at: index + 1, in: operations, errors: errors, completion: completion)
        }
    }
}
```

This coordinator will start by performing the first operation, then wait for it to complete before moving on to the next.

Since the serial coordinator implements the same protocol as the concurrent one, you can just switch out the implementation in the example above:

```swift
let coordinator = SerialOperationCoordinator()
coordinator.perform(operations) { errors in
    print("All done")
}
```

We now have two really basic ways to coordinate multiple operations. This approach is already really useful, but it gives us little granular control over how we can describe and compose various types of operations. Let's take this approach further and look at how we can operate on collections of items.

Before we do, I just want to emphasize that you don't have to use the coordinators directly. They can also be internal tools for other classes, where the operation concept is hidden from the external interface. Say that you have an `offline data syncer` with a function named `syncOfflineChanges()`. This gives a specific external protocol that talks offline syncing, but the implementation can still use operations and coordinators internally.



## Operating on a collection of items

While parameterless operations are great, since they can be just about anything, we could define several different kind of operations to get more intricate control.

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

These operations describe how to operate on a *collection*, *single items* and a *batches of items*. When you implement any of these protocols, you must implement its `perform` function and specify the `OperationItemType` with a typealias.

We now have more detailed protocols, but (so far) no real benefits. If we were to stop here, we would have nice protocols, sure, but we'd have to implement everything ourselves. Let's do something about this, by creating more specialized protocols, that provide coordination logic.


### Concurrent operations

To demonstrate how specialized protocols can provide us with logic, let's create a composite of `CollectionOperation` and `ItemOperation`, that provides us with a concurrent collection operation implementation:

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

This protocol implements `perform(onCollection:)` by performing the operation concurrently on every individual item in the collection. This means that you only have to implement `perform(onItem:)` when implementing this protocol.

Now we're getting somewhere! This protocol actually brings us some real power, and makes it a lot easier to implement a concurrent operation, since the coordination is already implemented.

Using the same approach, we can create a protocol that operates on batches instead of items:

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

This protocol also implements `perform(onCollection:)` as a protocol extension, but does so by chopping up the collection into batches. It then perfors the operation concurrently on every batch. Note that you must also define a `batchSize` in your implementation.

Since both protocols implement `CollectionOperation`, they are interchangeable. This means that you can replace `ConcurrentCollectionItemOperation` with `ConcurrentCollectionBatchOperation` to change how your implementations behave, given that the underlying operation supports batches.


### Serial operation

You can't use concurrent operations if the execution order matters, since a simple network delay could mess up the execution order. The best way to solve such issues is to design your system to support concurrency, but if that's not an option, you can use serial operations instead.

Let's create serial variants of the item and batch operations above, to show how easy this is to achieve. Let's start with a serial item operation:

```swift
public protocol SerialCollectionItemOperation: CollectionOperation, ItemOperation {}

public extension SerialCollectionItemOperation {
    
    func perform(onCollection collection: [OperationItemType], completion: @escaping CollectionCompletion) {
        perform(at: 0, in: collection, errors: [], completion: completion)
    }
}

private extension SerialCollectionItemOperation {
    
    func perform(at index: Int, in collection: [OperationItemType], errors: [Error], completion: @escaping CollectionCompletion) {
        guard collection.count > index else { return completion(errors) }
        let object = collection[index]
        perform(onItem: object) { error in
            let errors = errors + [error].compactMap { $0 }
            self.perform(at: index + 1, in: collection, errors: errors, completion: completion)
        }
    }
}

``` 

Just as the concurrent operation, this protocol implements `perform(onCollection:)`, but does so serially instead of concurrently. If you implement this protocol, you therefore only have to implement `perform(onItem:)`.

This protocol is completely interchangeable with `ConcurrentCollectionItemOperation`, since they have the same external interface. This means that if you have an implementation that performs an operation concurrently, you can make it serial just by letting it implement `SerialCollectionItemOperation` instead of `ConcurrentCollectionItemOperation`.

We can easily create a serial batch operation as well:

```swift
public protocol SerialCollectionBatchOperation: CollectionOperation, BatchOperation {
    
    var batchSize: Int { get }
}

public extension SerialCollectionBatchOperation {
    
    func perform(onCollection collection: [OperationItemType], completion: @escaping CollectionCompletion) {
        let batches = collection.batched(withBatchSize: batchSize)
        perform(at: 0, in: batches, errors: [], completion: completion)
    }
}

private extension SerialCollectionBatchOperation {
    
    func perform(at index: Int, in batches: [[OperationItemType]], errors: [Error], completion: @escaping CollectionCompletion) {
        guard batches.count > index else { return completion(errors) }
        let batch = batches[index]
        perform(onBatch: batch) { error in
            let errors = errors + [error].compactMap { $0 }
            self.perform(at: index + 1, in: batches, errors: errors, completion: completion)
        }
    }
}
```

The same goes here. This protocol implements `perform(onCollection:)`, but does so serially instead of concurrently. If you implement it, you therefore only have to implement `perform(onBatch:)`. It's interchangeable with `ConcurrentCollectionBatchOperation`.

This gives you a lot of flexibility. You can call all collection operations the same way, and can easily make them behave differently by replacing which protocols they implement.


## Final improvements

With these new operations in place, we can simplify the coordinators we implemented earlier. Looking at their code, it's obvious that they share a bunch of logic with the operations we just implemented. This means that we could describe them in another way:

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

The coordinators are basically just item operations, where the item type is `Operation`. This means that we can rewrite them as above, and reuse a lot of the power we implemented in the various collection operations.



## Conclusion

I hope you liked this post. If you try this approach, I'd love to hear how things worked out.

Please note that this post doesn't claim that its implementations is any better than [PromiseKit]({{page.promisekit}}), [AwaitKit]({{page.awaitkit}}), [RxSwift]({{page.rxswift}}) and [ReactiveCocoa]({{page.reactivecocoa}}). They are very popular, but sometimes using them is not an option. My advice is to take a look at them and make your own decision.

The implementation in this post is just vanilla Swift with some protocols and implementations. It does not introduce any new keywords that doesn't already exist in Swift, no async/await, no promises, no observables. It basically just provides a bunch of tiny protocols. It could probably be improved to use GCD and extended in a bunch of ways, but I hope that you enjoyed the discussions we could have by not walking down that path.

I have pushed the source code to my personal iExtra library (open source, but I mainly maintain it for myself). If you want to try it out, you can find it [here]({{page.source}}). You can also check out a [demo]({{page.demo}}) repository that just contains this code.

