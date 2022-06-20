---
title:  Introducing StoreKitPlus
date:   2022-06-24 08:00:00 +0000
tags:   article storekit open-source

image: /assets/blog/headers/storekitplus.png

storekitplus: https://github.com/danielsaidi/StoreKitPlus
---

In this post, let's take a look at a new library that I just released. [StoreKitPlus]({{page.storekitplus}}) adds extra functionality for working with StoreKit 2, like extensions, observable state, services, etc. and aims to make using StoreKit much easier, especially in SwiftUI-based applications.

![StoreKitPlus logo]({{page.image}})


## Background

StoreKit 2 is a huge improvement compared to the old StoreKit APIs. Gone are the many notifications, transaction states etc. that you had to listen for. The new APIs are very simple to use and behave great.

However, I have found some things missing when using this new framework. One thing is an easy way to observe store-specific state, so that store state can drive the UI in a SwiftUI application. Other things are the possibility to mock the StoreKit integration, persisting product and purchase information and to set up a local representation of the real StoreKit products etc.

As such, I've created a tiny layer on top of StoreKit 2. It adds observable state, an abstract store service protocol, a concrete store service implementation as well as protocols for validating transactions and specifying local product representations. StoreKitPlus is easy to start using and can be extended with your own, custom logic, should you need to. 

Let's take a look at what it contains.


## Getting products

To get products from StoreKit 2, you can use the `Product.products` api:

```swift
let productIds = ["com.your-app.productid"]
let products = try await Product.products(for: productIds)
```

However, if you need to do this in an abstract way, for instance if you need to mock the functionality in a unit test suite, extend the core functionality, etc., you can use the `StoreService` protocol, which has a `getProducts()` function:

```swift
let productIds = ["com.your-app.productid"]
let products = try await service.getProducts()
```

The `StandardStoreService` implementation communicates directly with StoreKit and syncs the result to a provided, observable `StoreContext`. Read more on this context further down.



## Purchasing products

To purchase products with StoreKit 2, you can use the `Product.purchase` api:

```swift
let result = try await product.purchase()
switch result {
    case .success(let result): try await handleTransaction(result)
    case .pending: break
    case .userCancelled: break
    @unknown default: break
}
return result
```

However, if you need to do this in an abstract way, as described above, the `StoreService` protocol has a `purchase(_:)` function:

```swift
let result = try await service.purchase(product)
```

If you use the `StandardStoreService` implementation, it communicates directly with StoreKit and syncs the result to a provided, observable `StoreContext`.



## Restoring purchases

To restore purchase with StoreKit 2, you can use the `Transaction.latest(for:)` api and then verify each transaction to see that it's purchased, not expired and not revoked.

This involves a bunch of steps, which makes the operation pretty complicated. To simplify, you can use the `StoreService` `restorePurchases()` function:

```swift
try await service.restorePurchases()
```

If you use the `StandardStoreService` implementation, it communicates directly with StoreKit and syncs the result to a provided, observable `StoreContext`.



## Syncing store data

To perform a full product and purchase information sync with StoreKit 2, you can fetch all products and transactions from StoreKit, then set your local state to reflect this information.

This involves a bunch of steps, which makes the operation pretty complicated. To simplify, you can use the `StoreService` `syncStoreData()` function:

```swift
try await service.syncStoreData()
```

If you use the `StandardStoreService` implementation, it communicates directly with StoreKit and syncs the result to a provided, observable `StoreContext`.



## Observable state

StoreKitPlus has an observable `StoreContext` that can be used to observe the store-specific state for a certain app.

```swift
let productIds = ["com.your-app.productid"]
let context = StoreContext()
let service = StandardStoreService(
    productIds: productIds,
    context: context
)
```

The context lets you keep track of available and purchased products, and will even cache the IDs of the product and purchased products, which lets you use this information even if the app is later offline. 

A context instance can be injected when creating a `StandardStoreService` to make the service keep track of changes as the user uses the service to communicate with StoreKit. This means that the context will be automatically kept in sync when the user uses the service in your app.



## Local products

If you want to be able to provide a local representation of your StoreKit product collection, you can use the `ProductRepresentable` protocol.

The protocol is just an easy way to provide identifiable product types, that can be matched with the real product IDs, for instance:

```swift
enum MyProduct: CaseIterable, String, ProductRepresentable {

    case premiumMonthly = "com.myapp.products.premium.monthly"
    case premiumYearly = "com.myapp.products.premium.yearly"

    var id: String { rawValue }
}
```

You can now use this collection to initialize a standard store service:

```swift
let products = MyProduct.allCases
let context = StoreContext()
let service = StandardStoreService(
    products: products,
    context: context
)
```

You can also match any product collection with a context's purchased product IDs:

```swift
let products = MyProduct.allCases
let context = StoreContext()
let purchased = products.purchased(in: context)
```

Just make sure that your local product types use the same IDs as the real products. Also note that some operations require that you provide a real StoreKit `Product`. 



## Conclusion

As you can see, using the StoreKitPlus library is very easy and just adds a bunch of convenience utilities on top of StoreKit 2. I will add more functionality when I see the need, or when other developers request more functionality. Until then, the library will be kept intentionally tiny.

If you want to git StoreKitPlus a try, you can test it [here]({{page.storekitplus}}). I'd love to hear your thoughts on this, so don't hesitate to comment, leave feedback etc.

