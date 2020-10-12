---
title: Generic Swift protocols
date:  2019-04-05 21:00:00 +0100
tags:  swift
icon:  swift
---

For years, I've been struggling with combining generics and protocols in Swift. In this post, I'll describe how I finally made sense of it all.


## The problem with generic protocols

I use protocols extensively and have had the need for generic ones many times over the years. However, I have always run into so many problems, that I every time have ended up with a non-generic approach.

For instance, when I last year set out to build a set of generic persistency stores, I started with describing the various types stores as generic protocols with associated types, for instance:

```swift
protocol ObjectStore {
    
    func deleteContent()
}

protocol SingleObjectStore: ObjectStore {
    
    associatedtype ObjectType
    
    func getObject() -> ObjectType?
    func store(_ object: ObjectType)
}
```

The real stores are a bit different, but let's keep it simple here. With these protocols in place, I then implemented them, using `typealias` to specify the associated types, and wrote unit tests for all stores.

Everything went great. I was really happy about finally being able to replace all old stores with these generic ones. So I tried to do just that, by creating a store property in my app.

If you want to create an `ObjectStore` instance, you specify it just as you would with any other type:

```swift
var store: ObjectStore
```

However, the same is not true for the generic `SingleObjectStore`:

```swift
var store: SingleObjectStore
```

If you do this, your code will fail to compile with this message: `Protocol 'SingleObjectStore' can only be used as a generic constraint because it has Self or associated type requirements`.

If you try to solve it like this:

```swift
var store: SingleObjectStore<Int>
```

you will see another error: `Cannot specialize non-generic type 'SingleObjectStore'`.

If you now panic and try to solve the problem like this:

```swift
var store<Int>: SingleObjectStore?
```

you will get this error: `'<' is not a prefix unary operator`. 

So, how do you do this? The solution always turns out to be retreating to non-generic protocols (but that's not a solution, that's defeat) or...type erasure.


## Type erasure

Every time I ran into these problems (coming from .NET, I never gave up on generics), the solution always turned out to be type erasure, where you use concrete types to wrap generic protocols. However, I never got it to work, mostly because it just seems like a way to work around Swift's current shortcomings. It never struck me as a clean solution, so I just avoided it.

This time, however, I was determined to make it work, and actually managed to do so. I will show you how, using simplified versions of the protocols I built. I will not go into details on type erasure, since there are so many posts that do this. More information about this is just an online search away :)


## A working example

Let's go back to our stores, where the single object store is intended to store a single objects:

```swift
protocol ObjectStore {
    
    func deleteContent()
}

protocol SingleObjectStore: ObjectStore {
    
    associatedtype ObjectType
    
    func getObject() -> ObjectType?
    func store(_ object: ObjectType)
}
```

Let's create a simple single object store that stores codable objects in user defaults:

```swift
class CodableSingleObjectStore<ObjectType: Codable>: SingleObjectStore {
    
    init(storageKey: String) {
        self.storageKey = storageKey
    }
    
    let storageKey: String
    
    var defaults: UserDefaults {
        return .standard
    }
    
    func deleteContent() {
        defaults.removeObject(forKey: storageKey)
    }
    
    func getObject() -> ObjectType? {
        guard let data = defaults.data(forKey: storageKey) else { return nil }
        return try? JSONDecoder().decode(ObjectType.self, from: data)
    }
    
    func store(_ object: ObjectType) {
        guard let encoded = try? JSONEncoder().encode(object) else { return }
        defaults.set(encoded, forKey: storageKey)
    }
}
```

If we'd like to create a concrete instance of this type, we just have to do this:

```swift
var store: CodableSingleObjectStore<MyType>
...
store = CodableSingleObjectStore<MyType>(storageKey: "key")
```

However, this still doesn't work...and never will, at least not in Swift 5:

```swift
var store: SingleObjectStore
...
store = CodableSingleObjectStore<MyType>(storageKey: "key")
```

We *could* use `CodableSingleObjectStore` and be done, but dependencies to conrete types is not a good thing to have in your code. At the same time, we can't have dependencies to the abstract protocol, due to reasons already discussed...so how do we do this?

This is where type erasure comes in. With type erasure, we create a concrete type that wraps an abstract one and uses its functionality, then use that wrapper everywhere instead of the protocol. This means that we still will have dependencies to a concrete type, but that type can be "filled" with anything.

The Swift naming convention for these type erasured types is `AnyX`, so we'd be creating a type erasured single object store like this:

```swift
class AnySingleObjectStore<ObjectType>: SingleObjectStore {
    
    init<T: SingleObjectStore>(_ store: T) where T.ObjectType == ObjectType {
        self.deleteContentClosure = store.deleteContent
        self.getObjectClosure = store.getObject
        self.storeObjectClosure = store.store
    }
    
    let deleteContentClosure: () -> ()
    let getObjectClosure: () -> (ObjectType?)
    let storeObjectClosure: (ObjectType) -> ()
    
    func deleteContent() {
        deleteContentClosure()
    }
    
    func getObject() -> ObjectType? {
        return getObjectClosure()
    }
    
    func store(_ object: ObjectType) {
        storeObjectClosure(object)
    }
}
```

If we'd like to create a concrete instance of this type, using the codable store we created earlier, we just have to do this:

```swift
var store: AnySingleObjectStore<MyType>
...
store = AnySingleObjectStore(CodableSingleObjectStore<MyType>(storageKey: "key"))
```

The `AnySingleObjectStore` object type will be used to ensure that we don't inject a store with a different object type, which means that we can be sure that we store the correct type in our store.

The important part to finally remember, is to always use `AnySingleObjectStore<MyType>` from now on, instead of using the `SingleObjectStore` protocol. Hopefully, this will change in future versions of Swift, so that type erasure is either implicitly implemented by the compiler, or removed altogether. 


## Conclusion

Type erasure is still a bit nasty, but the solution above gets the job done. If you have any ideas on how to get rid of the closures in `AnySingleObjectStore` and keep an instance of the store instead, I'd love to hear about it.