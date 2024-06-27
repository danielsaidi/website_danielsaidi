---
title:  Introducing StoreKitPlus
date:   2022-06-24 08:00:00 +0000
tags:   open-source storekit iap subscriptions

image: /assets/headers/storekitplus.png
tweet: https://twitter.com/danielsaidi/status/1541295027208556544?s=20&t=KLgrRJR_DDdJ70DjpNTB5Q
---

{% include kankoda/data/open-source.html name="StoreKitPlus" %}In this post, let's take a look at [{{project.name}}]({{project.url}}), which adds extra functionality for working with StoreKit 2 to make it easier to use StoreKit in SwiftUI.

![StoreKitPlus logotype]({{page.image}})


## Background

StoreKit 2 is a huge improvement to StoreKit 1. Gone are many notifications, transaction states etc. that you had to handle. The new APIs are very simple to use and behave great.

However, I have found some things missing, like an easy way to observe store-specific state and let it drive SwiftUI updates, the possibility to mock the StoreKit integration, persisting product & purchase information, setting up local product representations, etc.

As such, I've created [{{project.name}}]({{project.url}}) as a tiny layer on top of StoreKit 2. It adds observable state, an abstract store service protocol, a concrete store service implementation as well as protocols for validating transactions and specifying local product representations. 

StoreKitPlus is easy to start using and can be extended with custom logic, if you need to. Let's take a look at what it contains.


## Getting products

To get products from StoreKit 2, you can use the `Product.products` api:

```swift
let productIds = ["com.your-app.productid"]
let products = try await Product.products(for: productIds)
```

However, if you need to do this in an abstract way, e.g. to mock the functionality in a test, extend the core functionality, etc., StoreKitPlus's `StoreService` has a `getProducts()`:

```swift
let productIds = ["com.your-app.productid"]
let products = try await service.getProducts()
```

The `StandardStoreService` implementation communicates directly with StoreKit and syncs the result to an observable `StoreContext`. Read more on this context further down.



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

If you need to do this in an abstract way, `StoreService` has a `purchase(_:)` function:

```swift
let result = try await service.purchase(product)
```

Just like with getting products, `StandardStoreService` communicates directly with StoreKit and syncs the result to a provided, observable `StoreContext`.



## Restoring purchases

To restore purchase with StoreKit 2, you can use the `Transaction.latest(for:)` api and verify each transaction to see that it's purchased, not expired and not revoked.

This involves a bunch of steps, which makes it pretty complicated. To simplify, you can use the `StoreService`'s `restorePurchases()` function:

```swift
try await service.restorePurchases()
```

Just like before, `StandardStoreService` communicates directly with StoreKit and syncs the result to a provided, observable `StoreContext`.



## Syncing store data

To perform a full product and purchase information sync with StoreKit 2, you can fetch all products and transactions from StoreKit, then set your local state to reflect this information.

This involves a bunch of steps, which makes it pretty complicated. To simplify, you can use the `StoreService` `syncStoreData()` function:

```swift
try await service.syncStoreData()
```

Just like before, `StandardStoreService` communicates directly with StoreKit and syncs the result to a provided, observable `StoreContext`.



## Observable state

StoreKitPlus has an observable `StoreContext` that provides observe state for your app.

You can inject a store context into a store service, to have the service automatically update the context, which drives your UI to automatically update when anything changes:

```swift
let productIds = ["com.your-app.productid"]
let context = StoreContext()
let service = StandardStoreService(
    productIds: productIds,
    context: context
)
```

The context lets you keep track of available & purchased products, and will cache the IDs of products & purchased products, which can be mapped to local product representations if the app is later offline.

To observe a context, your app can just inject the context instance into the view hierarchy:

```swift
struct MyApp: App {

    var body: some Scene {
        WindowGroup {
            ContentView()
                .environmentObject(context)
        }
    }
}
```

Any view can then resolve it to update whenever anything in the context changes:

```swift
struct MyView: View {

    @EnvironmentObject
    private var context: StoreContext

    var body: some View {
        ...
    }
}
```

The context will now be automatically kept in sync when the user uses the service in your app, which will automatically update the UI in the app.



## Local products

You can use the `ProductRepresentable` protocol to provide a local representation of your StoreKit product collection, 

This protocol is an easy way to provide product representations that can be matched with your real products, for instance:

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

While communication with the StoreKit APIs requires real products, this lets you display a product collection and its purchase state while the app is offline.



## Conclusion

StoreKitPlus is easy to use and adds a bunch of convenience utilities on top of StoreKit 2. I will add more functionality if I see the need, otherwise it will be kept intentionally tiny.

If you want to git StoreKitPlus a try, you can test it [here]({{project.url}}). I'd love to hear your thoughts on it, so don't hesitate to comment, leave feedback etc.

