---
title:  Rewriting Wally for iOS 16 and SwiftUI 4
date:   2022-09-19 00:00:00 +0000
tags:   apps swiftui spm testing storekit iap subscriptions

image:  /assets/blog/22/0919/devices-leather.jpg
assets: /assets/blog/22/0919/

tweet:  https://twitter.com/danielsaidi/status/1571764175050866689?s=20&t=vry4W_e_oLhEFQDusmHHuw

navigation-post: /blog/2022/08/08/getting-started-with-the-SwiftUI-navigation-split-view

realm:  https://realm.io
storekit-plus:  https://github.com/danielsaidi/StoreKitPlus
quick:  https://github.com/Quick/Nimble
wally:  https://wally.app
wally-appstore:   https://apple.co/3BRXoMP
---

I'm about to release a brand new version of [Wally]({{page.wally}}), which was the first app I ever made for iOS 10+ years ago. Wally 4 has been rewritten for iOS 16, using SwiftUI 4.

![A bunch of devices that run Wally 4]({{page.assets}}devices.png){:class="plain"}


## Background

While I have been maintaining Wally for many years now, I really wanted to put time into polishing it this time. I started migrating old code in the beginning of July, then took some summer time off before I resumed work in August.

The work has resulted in a brand new foundation package for all my company apps, new versions of many [open-source projects]({{site.urls.github}}), a WallyKit package decoupled from the app, and a multi-platform app written in SwiftUI 4.


## Migrating old code

When I rewrote Wally last year, I added app-agnostic code to a framework target within the app project. This time, to really separate the domain code from the app, I created a Wally-specific Swift package and added it to the app as a local pacakge. 

Having a separate package lets me work on things like assets, views, styles, etc. within the package, without having to open the app project. The app only contains app-specific code.

The package will compile faster than the app, since there are less build settings and fewer external dependencies. This leads to faster build times, which minimizes the feedback loop for SwiftUI previews and unit tests. 

Since I aim to have little to no external dependencies in my packages (except my own), I only add 3rd party dependencies to the app, or specific dependency-based packages. 

For instance, the WallyKit package defines protocols that describe how to store items, as well as some store implementations, such as memory- and context-based stores. 

I then add Realm-specific stores to the app, since [Realm]({{page.realm}}) is a pretty huge dependency that takes time to fetch and build. In the future, I may add a Realm-specific package as well.

With the local package in place, I began moving code from the framework target, rewriting and improving it as I went along. If some old code fitter better in [my open-source projects]({{site.urls.github}}), I moved it there. If it could be reused in my other apps, I moved it to my company package.

I also replaced [Quick & Nimble]({{page.quick}}) with XCTests to make unit tests as plain as possible, with support for new Swift features like async/await.


## Migrating old data

To avoid having to rewrite *everything* for this version, I decided to stick with [Realm]({{page.realm}}) instead of moving to CoreData, which I would like to do later. 

Keeping Realm let me keep my current schemas and add more types to the app with little work. Since I map Realm-specific objects to app-specific ones, the Realm objects can be kept as is while the local models can change drastically.

I however had to consider other things regarding the app's data. Since this new version will support more types, where some uses two images, I had to consider export compressions to reduce the size of the exported files. 

Adding compression to the file export operation made the exported files much smaller, but forced me to handle decompression for new files and ignoring it for old files during import.


## Rewriting the app for iOS 16 and SwiftUI 4

Targeting iOS 16 and SwiftUI 4 let me get rid of a lot of custom code that I had to have in the old version, which means less code that needs testing. 

For instance, the new `Transferable` protocol let me get rid of a lot of manual export/import code. Using `async/await` instead of completion blocks also made my code cleaner, which in combination with `Transferable` drastically simplified the export/import code. 

I now use the [NavigationSplitView]({{page.navigation-post}}) with programmatic navigation, which results in a nice, new layout on iPhone & iPad. I will try to organize the menu better in upcoming versions. 

Due to the app's customizable UI, where users can change the look of the app with skins, I struggled a bit with accent, tint and foreground colors to get the navigation view hierarchy to look great, but overall it has been a pleasure to use and works great.

I'm also using a bunch of other SwiftUI features, such as `searchable`, new toolbars, drag & drop, context menus, etc. SwiftUI is maturing a lot each year!

I also decided to let the design differ between iPhone & iPad, and use a main skeumorphic primary button on iPhone, and the top toolbar to provide a more desktop-like UI on iPad.

To conclude, I think it should come as no surprise to anyone who has been reading my blog, that I think building apps in SwiftUI is amazing. SwiftUI 4 takes this to a whole new level, and enables so many more things. The overall developer experience is just great!


## Adding support for more item types

While old versions of Wally only lets users store cards, like bank and loyalty cards, the new version adds support for new item types, likebusiness cards, receipts, notes, and photos.

Implementing this meant looking at the item-specific logic in a new way. I could no longer have protocols like this (pseudo-code, but I hope you get the idea):

```swift
public protocol CardStore {

    func add(_ card: Card)
    func delete(_ card: Card)
    func getCard(withId: String) -> Card?
    func getCards() -> [Card]
}
```

Continuing to have these kinds of item-specific protocols would lead to a lot of duplicated code, since each new item type would require new implementations.

I therefore put a lot of effort into core protocols that describe how to store any kind of `item`, which let me constrain how types can be combined and what needs to be implemented.

For instance, instead of having a `CardStore` protocol, I now have a `WalletItemStore` one that looks something like this:

```swift
public protocol WalletItemStore: AnyObject {

    associatedtype Item

    var itemCount: Int { get }

    func getItems() async throws -> [Item]

    func remove(_ item: Item) async throws

    func store(_ item: Item) async throws
}
```

This allows me to add protocol extensions that apply to all implementations. I can create base stores that implement the protocol in various ways, e.g. by storing items in-memory:

```swift
public class MemoryWalletItemStore<Item: WalletItem>: WalletItemStore {

    public init(items: [Item] = []) {
        self.items = items
    }

    private var items: [Item]

    public var itemCount: Int {
        items.count
    }
    
    public func getItems() async throws -> [Item] {
        items
    }

    public func remove(_ item: Item) async throws {
        items.removeAll { $0.id == item.id }
    }

    public func store(_ item: Item) async throws {
        try await remove(item)
        items.append(item)
    }
}
```

or keeping an type-specific `ObservableObject` in sync:

```swift
public class ContextualWalletItemStore<
    Item: WalletItem,
    BaseStore: WalletItemStore,
    Context: WalletItemContext>: WalletItemStore where BaseStore.Item == Item, Context.Item == Item {

    public init(
        baseStore: BaseStore,
        context: Context
    ) {
        self.baseStore = baseStore
        self.context = context
        Task(operation: initializeContext)
    }

    private let baseStore: BaseStore
    private let context: Context

    public func getItems() async throws -> [Item] {
        try await baseStore.getItems()
    }
    
    public func remove(_ item: Item) async throws {
        try await baseStore.remove(item)
        await removeFromContext(item)
    }
    
    public func store(_ item: Item) async throws {
        try await baseStore.store(item)
        await addToContext(item)
    }
}

...
```

The contextual store uses the `decorator pattern`, to wrap another implementation of the same protocol and add its own functionality on top. This makes it possible for this store to inject a `Realm`-based store later, although that store is not available in the store package.

I can then add item-specific protocols to increase readability and simplify the dependency configuration, without having to add any more functionality to the store:

```swift
public protocol CardStore: WalletItemStore where Item == Card {}
```

To provide my app with a card store, I can easily set up one in my dependency container, where I just use a static class that provides the app with abstract protocol implementations:

```swift
static let cardContext = CardContext()

static let cardStore = ContextualCardStore(
    // baseStore: MemoryCardStore(items: (0...2).map(Card.preview)),
    baseStore: RealmCardStore(),
    context: cardContext
)
```

I can now resolve a memory card store, a Realm-specific store, or any custom store I like. The contextual store doesn't care, since its only concern is updating the context.

Furthermore, I put a lot of care into the app, where each item type must specify the views to use to when listing items, editing the list, displaying an item, etc. This let me add many different item types with very little extra code.

To conclude, these efforts mean that I will hopefully have a stable app that is easy to expand with more types, without having to add a lot of functionality for each new item type.


## Adding support for skins

Since the app is skeumorphic and imitates a leather wallet, I have for a long time wanted to add support for skins. This was made very easy with SwiftUI.

As you can see in the image below, the wallet can be skinned with different materials. Each material defines its own textures, stitches, colors etc.

![A bunch of devices that run Wally 4]({{page.assets}}devices.png){:class="plain"}

When a user selects a skin, it's persisted and used until the user selects another skin. To avoid serializing things like colors etc. each skin refers to a non-persisted appearance.

Separating the theme from its appearance lets us avoid problems like getting old colors if a theme is updated, and avoid destroying adaptive nature of colors by serializing them.

I'm looking forward to be able to add more features to Wally, that don't involve writing a lot of code, but instead have a more artistic nature.


## Adding a premium subscription

This new version of Wally will include many new features, such as being able to store more item types in the wallet, changing the look and feel of the app by changing skins etc.

This year, I'm finally adding a premium subscription, which unlocks premium items, skins, etc. I am exited to try it out, and skins gives me a way to add new features with little code.

I use StoreKit 2 to implement the subscription logic, and use my [StoreKitPlus]({{page.storekit-plus}}) open-source project to reuse a bunch of logic. I have also created a bunch of premium-specific SwiftUI views and screens that I will be able to reuse in my various apps.


## Reusing functionality

During my work with the app, I have fixed bugs in and added new features to my various [open-source projects]({{site.urls.github}}), as I encountered problems or needed new features. This means that this project has also been a great opportunity to revisit and improve many other projects.

Furthermore, finally getting around to creating a foundation package for all my apps will make it a lot easier to work on my own apps, and will make sure that they behave more similar, which in turn will make it less problematic for me to work on many apps at once.


## Releasing the app

I'm excited to finally have a release version in the hands of beta testers and am waiting for review approval. Although I missed the iOS 16 release window, I'm still happy to release it relatively shortly after.

The app review process has however turned out to be pretty meticilous this time around. So far, the app has been rejected three times, over minor things. I try to be quick in fixing what needs fixing and do hope that the app will be out soon.


## Conclusion

This Wally rewrite has been a really fun project, and something lays a foundation for the future, where I can evolve the app without having to rewrite everything all the time. It can be downloaded from the [App Store]({{page.wally-appstore}}) today. You can find more information on it's [website]({{page.wally}}).

Overall, I am really excited about the future of app development on Apple's platforms. I'd love to hear what you think of the post and about Apple's tech stack in general.