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

If you are willing to make that choice, give it a try! However, if you want to keep your external dependencies down to a minimum and avoid fundamentally change your architecture, you can come a long way with encapsulation and some custom code.

In the examples below, I will combine a couple of minimal protocols to get a lightweight, modular way of coordinating the execution of multiple async operations.

`Disclaimer` I am not yet happy nor done with the naming of these protocols. If you read this and have suggestions or know of already existing patterns or established names, please let me know.


### Collection operation

Basically, we want to build something that helps us coordinate how a bunch of async operations are executed. Here, I will look at that from the perspective of operating on a collection of "things" in a collection. This means that we could operate on basically anything, like strings, ints, objects, anoymous blocks etc.

Let's start creating a set of protocols that will help us operate on a collection of items. Let's define a protocol that describes how to operate on a collection:

```swift
public protocol CollectionOperation: AnyObject {
    
    associatedtype CollectionType
    typealias T = CollectionType
    typealias Completion = ([Error?]) -> ()
    
    func performOperation(on collection: [T], completion: @escaping Completion)
}
```

There's nothing special about this. When you implement this protocl, just implement `performOperation(on:completion:)` and specify `CollectionType` with a typealias. However, it's not that helpful either, since it just describes how to operate on a collection. We must do some more coding to get some real benefits out of this.


### Operating on a collection

If we consider the concept of "performing an operation on a collection of items", there are several ways to do this, for instance:

* Perform the operation on the entire collection
* Perform the operation on individual items in the collection
* Perform the operation on batches of items from the collection

The first case is already described by the `CollectionOperation` protocol, although it does not provide an implementation. For the other two cases, let's create some specialized versions of the protocol.


### Item operation

The task of "performing an operation on individual items in a collection" could be described with a protocol that extends `CollectionOperation`, as such:

```swift
public protocol ItemOperation: CollectionOperation {
    
    typealias ItemCompletion = (Error?) -> ()
    
    func performOperation(onItem item: T, completion: @escaping ItemCompletion)
}
```

When implementing this protocol, you must implement `CollectionOperation` as well as `performOperation(onItem:completion:)`, which is called for every item in the collection. It's very important to call the item completion block when the operation finishes for every item, since implementations of this protocol will rely on it to coordinate the operation.


### Batch operation

The task of "performing an operation on batches of items from a collection" could be described with another protocol, that also extends `CollectionOperation`:

```swift
public protocol BatchOperation: CollectionOperation {
    
    typealias BatchCompletion = (Error?) -> ()
    
    var batchSize: Int { get }
    
    func performOperation(onBatch batch: [T], completion: @escaping BatchCompletion)
}
```

When implementing this protocol, you must implement `CollectionOperation` as well as `performOperation(onBatch:completion:)`, which is called for every batch of items that is extracted from the collection. You must also specify a batch size. As with the `ItemOperation`, it's very important to call the item completion block when the operation finishes for every block.


### Still no implementations?

We now have three protocols, but still no implementations nor any real benefits. If we were to stop here, we'd just have three protocols that describe how to operate on collections, items and batches, but would still have to implement everything ourselves.

Let's do something about that! Let's create even more specialized protocols that implement the most critical part - how to coordinate the operation.


### Parallell item operation

The simplest specialization we can make of the protocols above, is to create a protocol that performs an operation on all items in parallel, like this:

```swift
public protocol ParallellItemOperation: ItemOperation {}

public extension ParallellItemOperation {
    
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

This protocol implements `performOperation(on:completion:)` in a protocol extension, which means that you just have to implement `performOperation(onItem:completion:)` when you implement `ParallellItemOperation`.

Now we're getting somewhere! This protocol actually gives us some real power. You now just have to care about how each item is handled, not how the operation is coordinated. When you use an implementation of this protocol, you just have to call `performOperation(on collection:completion:)` and don't have to care about how each item is handled. So the benefits go both ways.

Let's see if we can take this further.


### Parallell batch operation

Using the same approach as above, it's very simple to create a similar protocol that operates on batches instead of single items:

```swift
public protocol ParallellBatchOperation: BatchOperation {}

public extension ParallellBatchOperation {
    
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

This protocol could be used to sync batches of data, where the order is irrelevant. For instance, you could use it to sync offline data, where the server does not care in which order the data is synced.

This protocol also implements `performOperation(on:completion:)` in a protocol extension, which means that you just have to implement `performOperation(onItem:completion:)` when you implement `ParallellBatchOperation`.

Just like `ParallellBatchOperation`, you just have to care about how to handle each item, not how the operation is coordinated. Since both protocols implement `CollectionOperation`, they're also externally interchangable, which means that you trigger a parallell item operation and a parallell batch operation in the same way.

Let's see if we can take this even further.


### Sequential item operation

If your operation is asynchronous and the order of the performed operations is important, you can't use parallell operations, since a simple network delay could mess up the order of performed operations. The absolute best way to handle these problems is to design your solution so that the order of execution is irrelevant. However, if you can't do this, you must execute your operations sequentially instead of in parallell.

Let's create another `ItemOperation`, that performs an operation sequentially on every item in the collection:

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

This protocol also implements `performOperation(on:completion:)` in a protocol extension, which means that you just have to implement `performOperation(onItem:completion:)` when you implement `SequentialItemOperation`. It will wait for each item operation to complete before it proceeds with the next.

Now things are getting really interesting! Since `ParallellItemOperation` and `SequentialItemOperation` has the same external interface, you can switch your execution strategy by simply switching out the protocol your operation class implements. If you `MySyncer` class implements `ParallellItemOperation` and you realize that you must switch over to sequential execution, just replace `MySyncer: ParallellItemOperation` with `MySyncer: SequentialItemOperation`.


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

This protocol also implements `performOperation(on:completion:)` in a protocol extension, which means that you just have to implement `performOperation(onItem:completion:)` when you implement `SequentialBatchOperation`. It will wait for each batch operation to complete before it proceeds with the next.

We now have four different operations, that are externally called in the same way. Internally, two of them are item operations and two are batch operations. This is beneficial for each implementation, since you can switch from sequential to parallell execution by just changing which protocol the class implements.

Let's put this together in a short example.


### Examples

Let's look at how we could implement these protocols. Let's build an imaginary image syncer that syncs images that has been taken while the user is offline:


```swift
class ImageSyncer: ParallellItemOperation {
    
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

This will sync all images in parallell, then print "All done!" when it finishes. 

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

If your solution would support syncing images in parallell batches instead of individually, the syncer could implement `ParallellBatchOperation` instead:

```swift
private class ImageSyncer: ParallellBatchOperation {
    
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

We then implemented a lightweight operation model from scratch, which can be used to coordinate async operations without affecting your code style at all. It's just vanilla Swift with some convenient abstractions. There are no new keywords that doesn't already exist in Swift, no async/await, no promises, no observables. Just seven tiny protocols.

If you want to take a look at the source code, I have posted it as part of my private-ish iExtra library (open source, but I mainly maintain it for myself). You can find the source code [here]({{page.source}}).


