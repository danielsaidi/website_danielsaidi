---
title:  "Coordinating async operations"
date:   2019-01-24 21:00:00 +0100
tags:	swift

source: https://github.com/danielsaidi/iExtra/tree/master/iExtra/Operations
promisekit: https://github.com/mxcl/PromiseKit
awaitkit: https://github.com/yannickl/AwaitKit
rxswift: https://github.com/ReactiveX/RxSwift
rxswift-post: http://danielsaidi.com/blog/2018/01/19/ditching-rxswift
reactivecocoa: https://github.com/ReactiveCocoa/ReactiveCocoa
---

Swift is an amazing language, but I still find that it lacks good native support for coordinating multiple async operations in a sophisticated way. In this post, I will look at existing libraries for handling this, then discuss a lightweight custom approach.

If you want to download the source code for the custom approach to have as a reference as you make your way through the post, you can find it [here]({{page.source}}).


## Existing alternatives

While languages like .NET and JavaScript has native support for async/await, Swift developers must rely on 3rd party libraries or build their own custom solution. Let's look at some open source libraries that aims to simplify working with async operations.

### PromiseKit

[PromiseKit]({{page.promisekit}}) is a nice library that gives you access to a nice `Promise` model. It will make your code look something like this:

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

I think that the promise-based chaining of async operations is nice, but find PromiseKit to be too talky. Furthermore, it still uses a block-based syntax, so you're still jumping in and out of blocks.

### AwaitKit

As an alternative to PromiseKit, [AwaitKit]({{page.awaitkit}}) extends PromiseKit with `async/await`. It will make the code above look something like this instead:


```swift
let result1 = try! await(makeRequest1())
let result2 = try! await(makeRequest2(result1))
doSomethingElse()
```

In my opinion, this is much nicer, since it removes the usage of blocks and makes the code appear to be synchronous. The only thing that strange with AwaitKit's sample code, is that they use `try!` as above. If you want to handle any error, just wrap the code above in a `do/catch` instead.

Keep in mind that if you decide to go with AwaitKit, you will implicitly get a depencendy to PromiseKit as well, since AwaitKit uses PromiseKit under the hood.

### RxSwift / ReactiveCocoa

If the promise model doesn't appeal to you, you could take a look at using observables. [RxSwift]({{page.rxswift}}) and [ReactiveCocoa]({{page.reactivecocoa}}) are two popular observable-based libraries, that you might want tp check out. I myself had a hard time liking RxSwift (I haven't tried ReactiveCocoa), but won't go into details here. If you want to read more about it, I wrote a post about it a while ago. You can read it [here]({{page.rxswift-post).


## A custom approach

What I do not like with the approaches above, is that they fundamentally change your architecture. Using async/await and rx will leak through your entire code base, which means that your code will heavily depend and rely on 3rd party dependencies.

If you are willing to make that investment and external dependencies it not a problem, I'd say go for it! However, if you want to keep your external dependencies down to a minimum and not deviate too much from "standard Swift", you can come a long way with encapsulation and some custom code.

In the examples below, I will combine custom operator protocols to get a lightweight, modular way of coordinating the execution of multiple async operations.

`Disclaimer` I am not yet happy nor done with the naming of these protocols, so if you read this and have other suggestions or know of already existing patterns that use this approach and use already names, please let me know.


### Collection operator

Let's start building a set of protocols that will help us operate on a collection of items. We'll start by defining a base protocol that describes how to operate on collections:

```swift
public protocol CollectionOperator: AnyObject {
    
    associatedtype CollectionType
    typealias T = CollectionType
    typealias Completion = ([Error?]) -> ()
    
    func performOperation(on collection: [T], completion: @escaping Completion)
}
```

There's nothing special about this protocol. When you implement it, just implement `performOperation(on:completion:)` and specify the `CollectionType` with a typealias.

However, it's also not that helpful yet. It's basically just a specification of one way to perform an operation on a collection. We need to do some more coding to get some real benefits out of this.

If we consider the concept of "performing an operation on a collection of items", there are several ways to do this, for instance:

1. Perform the operation on the entire collection
2. Perform the operation on individual items in the collection
3. Perform the operation on batches of items from the collection

The first case is already covered by the `CollectionOperator` protocol. Just implement it and you're good to go. For the other two cases, let's create more specialized versions of the protocol.


### Item operator

The task of "performing an operation on individual items in a collection" could be described with a protocol that extends `CollectionOperator`, as such:

```swift
public protocol ItemOperator: CollectionOperator {
    
    typealias ItemCompletion = (Error?) -> ()
    
    func performOperation(onItem item: T, completion: @escaping ItemCompletion)
}
```

When implementing this protocol, you must implement `CollectionOperator` as well as `performOperation(onItem:completion:)`, which should be called for every item in the collection. It's also very important to call the item completion block, since implementations of this protocol will most probably rely on it.


### Batch operator

The task of "performing an operation on batches of items from a collection" could be described with another protocol, that also extends `CollectionOperator`:

```swift
public protocol BatchOperator: CollectionOperator {
    
    typealias BatchCompletion = (Error?) -> ()
    
    var batchSize: Int { get }
    
    func performOperation(onBatch batch: [T], completion: @escaping BatchCompletion)
}
```

When implementing this protocol, you must implement `CollectionOperator` as well as `performOperation(onBatch:completion:)`, which will be called for every item batch. You must also specify a batch size, which will be used to split up the collection in batches. As with the `ItemOperator`, it's also very important to call the item completion block for this protocol as well.


### Specialized protocols

We now have three protocols, but still no real benefits other than describing the operations. If we were to stop here, we'd still have to implement everything ourselves. So let's go a little further.

We will therefore create even more specialized protocols that will implement the most critical part of the operator in various ways, namely the way the operator operates on the collection.


### Parallell item operator

The simplest specialization we can make of the protocols above, is to create a protocol that performs the operation on all items in parallel, like this:

```swift
public protocol ParallellItemOperator: ItemOperator {}

public extension ParallellItemOperator {
    
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

The protocol implements `performOperation(on:completion:)` as a protocol extension, which means that you just have to implement `performOperation(onItem:completion:)`. In other words, you just have to care about how each item is handled, not how the operations are executed.


### Parallell batch operator

Using the same approach as above, it's very simple to create another similar protocol that operates on batches of items instead of single items:

```swift
public protocol ParallellBatchOperator: BatchOperator {}

public extension ParallellBatchOperator {
    
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

This protocol is very similar to `ParallellItemOperator`, but instead of working on individual items, you have to specify a batch size and implement `performOperation(onBatch:completion:)`.

This protocol could be used to sync batches of data, where the order is irrelevant. For instance, you could use it to sync offline data, where the server does not care in which order the data is synced.


### Sequential item operator

If your operation is asynchronous and the order of the performed operations is important, you can not use parallell executions, since a network delay could cause a later operation to complete first.

The absolute best way to handle these problems is to design your solution so that the order execution is irrelevant, but if you can't do this, you should execute your operations sequentially.

Let's create another implementation of the `ItemOperator`, that performs an operation sequentially on every item in the collection, instead of in parallell:

```swift
public protocol SequentialItemOperator: ItemOperator {}

public extension SequentialItemOperator {
    
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

Just like `ParallellItemOperator`, this protocol already implements `performOperation(on:completion:)`. The big change here is that it waits for the previous operation to complete before it proceeds.

Since `ParallellItemOperator` and `SequentialItemOperator` has the same external interface, you can switch your execution model by simply switching out the protocol your operator class implements.


### Sequential batch operator

Using the same approach as above, it's very simple to create another similar protocol that operates on batches of items instead of single items:

```swift
public protocol SequentialBatchOperator: BatchOperator {}

public extension SequentialBatchOperator {
    
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

This protocol is very similar to `SequentialItemOperator`, but instead of working on individual items, you have to specify a batch size and implement `performOperation(onBatch:completion:)`.

This protocol could be used to sync batches of data, where the order of execution matters. For instance, you could use it to sync offline events, where the server must receive events in the correct order.

Just as with the item operators, `ParallellBatchOperator` and `SequentialBatchOperator` has the same external interface. You can thus switch your execution model by switching out the protocol your operator class implements.


### Examples

Let's look at how we could implement these protocols. For instance, let's build an imaginary image syncer that syncs images that has been taken while the user is offline:


```swift
private class ImageSyncer: ParallellItemOperator {
    
    typealias CollectionType = UIImage
    
    func performOperation(onItem item: UIImage, completion: @escaping ItemCompletion) {
        syncImagesInSomeWay(item) { error in
            completion(error)
        }
    }
}
```

You can then call this syncer as such:

```swift
let syncer = ImageSyncer()
let images = [offlineImage1, offlineImage2, ...]
syncer.performOperation(on: images) { errors in
    print("All done!")
}
```

The syncer will now sync all images in parallell, image by image, then print "All done!" when it finishes. 

If you, for some reason, want to perform this operation sequentially, you just have to change which protocol your syncer implements:

```swift
private class ImageSyncer: SequentialItemOperator {
    
    // The rest can be left unchanged :)
}
```

If you can sync images in parallell batches, your syncer can implement the `ParallellBatchOperator` instead:

```swift
private class ImageSyncer: ParallellBatchOperator {
    
    typealias CollectionType = UIImage
    
    func performOperation(onBatch batch: [UIImage], completion: @escaping BatchCompletion) {
        syncImagesInSomeWay(batch) { error in
            completion(error)
        }
    }
}
```

Since the syncer is still a collection operator, the external interface is unchanged, so you can still call this syncer as such:

```swift
let syncer = ImageSyncer()
let images = [offlineImage1, offlineImage2, ...]
syncer.performOperation(on: images) { errors in
    print("All done!")
}
```

If you, for some reason, want to perform this batch-based operation sequentially, you once again just have to change which protocol your syncer implements:

```swift
private class ImageSyncer: SequentialBatchOperator {
    
    // The rest can be left unchanged :)
}
```


## Conclusion

In this post, we have looked at some popular libraries for working with async operations in more sophisticated ways than just using completion blocks. 

However, we have also implemented a lightweight operator model from scratch, that can be used without affecting your code base in any way. There are no new keywords that doesn't already exist in Swift, no async/await, no promises, no observables. Just seven tiny protocols.

If you want to take a look at the source code, I have posted it as part of my private-ish iExtra library (open source, but I mainly maintain it for myself).

You can find the source code [here]({{page.source}}).


