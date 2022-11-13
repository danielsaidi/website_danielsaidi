---
title:  Rewriting Wally for iOS 16 and SwiftUI 4
date:   2022-09-19 00:00:00 +0000
tags:   apps swiftui spm testing storekit iap subscriptions

image:  /assets/blog/2022/2022-09-19/devices-leather.jpg
assets: /assets/blog/2022/2022-09-19/
tweet:  https://twitter.com/danielsaidi/status/1571764175050866689?s=20&t=vry4W_e_oLhEFQDusmHHuw

navigation-post: /blog/2022/08/08/getting-started-with-the-SwiftUI-navigation-split-view

realm:  https://realm.io
storekit-plus:  https://github.com/danielsaidi/StoreKitPlus
quick:  https://github.com/Quick/Nimble
wally:  https://wally.app
wally-appstore:   https://apple.co/3BRXoMP
---

I'm about to release a brand new version of [Wally]({{page.wally}}), which was the first app I ever made for iOS 10+ years ago. Wally 4 has been rewritten for iOS 16 and iPadOS 16 using SwiftUI 4. This post will go through some of the things involved in this major rewrite.

![A bunch of devices that run Wally 4]({{page.assets}}devices.png){:class="plain"}


## Background

I have written many apps over the years, but it's mostly been about exploring new technologies and to have a reason to learn new things. As soon as an app has become good enough, I tend to move on to other projects and only put work into fixing bugs or to add requested features. I tend to spend little to no time on marketing once an app is done and released.

While this may once again prove true with Wally 4, I really wanted to put time and energy into polishing it this time. I started migrating old code in the beginning of July, then took some summer time off before I resumed work in August. I then worked with the app part time until mid-September, and only missed the iOS 16 launch date because of a family trip (easily worth it).

With Wally 4 now ready for release, the work has resulted in a brand new foundation package for all my company apps, new versions of many of [my open-source projects]({{site.github_url}}), a Wally-specific package decoupled from the app, and a multi-platform app written in SwiftUI 4.

Let's go through some of the steps involved in getting here.


## Migrating old code

When I rewrote Wally last year, I added app-agnostic code to a framework target within the app project. This time, to really separate the domain code from the app, to make creating new future app versions easier, I created a Wally-specific Swift package and added it to the app as a local pacakge. This lets me work on things like assets, general views, styles etc. directly in the package, without having to open the app project, and make the app project only contain app-specific functionality.

Some benefits of working in a package instead of a project, is that it tends to compile faster, since there are generally less build settings and fewer external dependencies in packages compared to apps. This leads to faster build times, which minimizes the feedback loop when working with SwiftUI previews and unit tests. Since I aim to have little to no external dependencies in my packages (except my own open-source packages), I add 3rd party dependency-based functionality to the app project instead. 

For instance, the Wally package defines store protocols that describe how to store various app items, as well as some implementations that can store items in basic ways, such as memory-based debug stores, observable-syncing wrappers etc. I then add Realm-specific implementations to the app, since [Realm]({{page.realm}}) is a pretty huge dependency that takes time to fetch and build. In the future, I may even consider adding a second, Realm-specific package and move these parts from the app.

With the local package in place, I began moving code over from the old framework target, rewriting and improving it as I went along. If some old code fitter better in one of [my open-source projects]({{site.github_url}}), I moved it there. If it could be reused in any of my other apps, I moved it to my company package.

I also decided to replace [Quick & Nimble]({{page.quick}}) with XCTests to make the unit tests as plain as possible, with great support for new Swift features like async/await. This is something that I also have begun doing in my other projects, even though rewriting unit tests is far down on my todo list.


## Migrating old data

To avoid having to rewrite *everything* for this version, I decided to stick with [Realm]({{page.realm}}) instead of moving to CoreData, which I would like to do later. Keeping Realm let me keep my current schemas and add more types to the app with very little work. However, since I map Realm-specific objects to local models that are used in the app, the Realm objects can be kept as is while the local models can change drastically.

However, I had to consider other things regarding the data that is used by Wally. Since the new version lets users add more types than just cards, with some types being of the kind that can easily generate a lot of data, I realized that I had to do something about the exported files, since they were saved without compression. Added a compression step to the file export made the exported files much smaller, but it also meant that I had to add a decompress step when a file was being imported, plus additional routes for handling old and new files.

I therefore had to choose whether or not to implement backwards compatibility for the file import. Since Wally is meant to be used to backup physical things, to which the user should have access, I eventually decided to not implement backwards compatibility for the file support. This means that old file versions will no longer be supported by the new app version.


## Rewriting the app for iOS 16 and SwiftUI 4

Targeting iOS 16 and iPadOS 16 and using SwiftUI 4 let me get rid of a lot of custom code that I had to have in the old version, which means less code that needs testing. 

For instance, the `Transferable` protocol let me get rid of a lot of manual export/import code, although I can't use the new `ShareLink`. Using `async/await` instead of completion blocks also made my code a lot cleaner, which in combination with `Transferable` reduced the export/import code by a lot. 

I now use the [NavigationSplitView]({{page.navigation-post}}) with programmatic navigation, which results in a nice new layout on iPhone and iPad, although I am a bit concerned that this pattern may become the new hamburger menu (which it already has kind of become in Wally). I will try to reorganize stuff better in upcoming versions. Due to the app's customizable UI, where users can change the look of the app with skins, I struggled a bit with accent, tint and foreground colors to get the navigation view hierarchy to look great on both iPad and iPhone, but overall the new split and stack views have been a pleasure to use and work great.

I'm also using a bunch of other SwiftUI features, such as `searchable` to add item search in the various lists as well as new toolbars, drag & drop, context menus etc. I also decided to let the design differ a bit between iPhone and iPad, where iPhone gets a large primary button in a skeumorphic bottom toolbar and iPad instead uses the top navigation toolbar to provide a more desktop-like UI.

To conclude, I think it should come as no surprise to anyone who has been reading my blog, that I think building apps in SwiftUI is amazing. SwiftUI 4 takes this to a whole new level, and enables so many more things. There's little need for custom workarounds now, and the overall developer experience is just great. I really hope that many of you get the chance to use it in your apps as well.


## Adding support for more item types

While old versions of Wally only lets users store cards, like bank and loyalty cards, the new version adds support for a bunch of new item types, such as business cards, receipts, notes, photos etc.

Implementing this support meant looking at the item-specific logic in a new way. I could no longer have protocols like this (pseudo-code, but I hope you get the idea):

```swift
public protocol CardStore {

    func add(_ card: Card)
    func delete(_ card: Card)
    func getCard(withId: String) -> Card?
    func getCards() -> [Card]
}
```

Continuing to have these kinds of item-specific protocols would lead to a lot of duplicated code, since each new item type would require new implementations. This would lead to a lot of work to ensure that any future changes are done in all places, which in turn would lead to risk for inconsistencies, bugs etc.

I therefore put a lot of effort into having core protocols that for instance describe how to store an `item`, which let me constrain how types can be combined and what needs to be implemented by new types.

For instance, instead of having a `CardStore` protocol like above, I now have a `WalletItemStore` protocol that looks something like this:

```swift
public protocol WalletItemStore: AnyObject {

    associatedtype Item

    var itemCount: Int { get }

    func getItems() async throws -> [Item]

    func remove(_ item: Item) async throws

    func store(_ item: Item) async throws
}
```

This allows me to add extensions to this protocol, which then apply to all implementations. I can now also create base stores that implement the protocol in various ways, e.g. by storing items in-memory:

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

or keeping an `ObservableObject` in sync:

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

The contextual store uses something called the `decorator pattern`, to use another implementation of the same protocol and add its own functionality on top. This makes it possible for this store to inject a `Realm`-based store later, although that store is not available in the package where this store is defined.

I can then add item-specific protocols to increase readability and simplifying dependency configuration in the dependency container you use, without having to add any more functionality to the store:

```swift
public protocol CardStore: WalletItemStore where Item == Card {}
```

If I now want to provide my app with a card store, I can easily set one up in my dependency container, where I just use a static class that provides the app with abstract protocol implementations:

```swift
static let cardContext = CardContext()

static let cardStore = ContextualCardStore(
    baseStore:
        // MemoryCardStore(items: (0...2).map(Card.preview)),
        RealmCardStore(),
    context: cardContext)
```

As you can see, I can either resolve a memory card store when developing the app, or a Realm-specific one for production, then use that store within a contextual store that syncs the items with an observable class, which here is called `CardContext`.

Furthermore, I put a lot of care into the app as well, where each item type must specify which views to use to for instance list all items, edit the list, show item details etc. This let me navigate to item screens for all item types in the same way. I also created base views for each screen, that require each item type to provide a tiny bit of information to the base view, instead of having each item-specific view having to implement the same list, add, delete functionality over and over.

To conclude, these efforts mean that I will hopefully have a stable app that is easy to expand with more types, without having to add a lot of functionality for each new item type.


## Adding support for skins

Since the app is skeumorphic and imitates a leather wallet, I have for a long time wanted to add support for skins. This was made very easy with SwiftUI.

As you can see in the image below, the wallet can be skinned with different materials. Each material defines its own textures, stitches, colors etc.

![A bunch of devices that run Wally 4]({{page.assets}}devices.png){:class="plain"}

When a user selects a skin, the skin is persisted and will be used until the user selects another skin. To avoid serializing things like colors etc. each skin refers to an appearance, which isn't persisted. This lets us avoid problems like getting old colors in the case where a theme is updated, as well as avoiding to destroy the adaptive nature of colors that for instance support dark mode.

I'm looking forward to be able to add more features to Wally, that don't involve writing a lot of code, but instead have a more artistic nature. I really need to revisit those parts of myself more often.


## Adding a premium subscription

This new mejor version of Wally will include many new features, such as being able to store more item types in the wallet, changing the look and feel of the app by changing skins etc.

Wally started off as a paid app, but was made free many years ago, when I wanted to switch over to in-app purchases. I therefore decided to make all existing features in the app free, but make it possible to add premium features later. This never happened, though. Wally remained free and while I added more features over the years, I never implemented in-app purchases.

This year, I'm finally adding a premium subscription to the app, which unlocks premium items, skins etc. I am exited to try this out, and the skins will give me a way to add more features to the app in a fun way that involve more design and less code.

I use StoreKit 2 to implement the subscription logic, and use my [StoreKitPlus]({{page.storekit-plus}}) open-source project to be able to reuse a bunch of logic that I have already implemented before. I have also created a bunch of premium-specific SwiftUI views and screens that I will be able to reuse in my various apps, which is nice.


## Reusing functionality

During my work with the app, I have fixed bugs in and added new features to my various [open-source projects]({{site.github_url}}), as I encountered problems or needed new features in Wally. This means that this project has also been a great opportunity to revisit many of these projects and improve them.

Furthermore, finally getting around to create a foundation package for all my company apps will make it a lot easier to work on my own apps, and will make sure that they behave more similar, which in turn will make it less problematic for me to work on many apps at once.


## Releasing the app

After working on this major rewrite for over a month, I'm excited to finally have a release version in the hands of beta testers and waiting for a review approval. Although I missed the iOS 16 release window, I'm still happy to release it relatively shortly after.

However, the app review process has turned out to be pretty meticilous this time around. So far, the app has been rejected three times, over minor things. I try to be quick in fixing what needs fixing and do hope that the app will be out soon.

This time, I will also try to get some marketing done, although this is something that I most often tend to overlook. I will at least update the app's website, create an in-app event and try to get some posts out in social media, but other than that I am mostly happy with having once again solved a technical challenge in a way that satisfies me :)


## Conclusion

This Wally rewrite has been a really fun project, and something I hope lays a foundation for the future, where I can more evolve the app without feeling the urge to rewrite everything for every major update.

Wally 4 can be downloaded from the [App Store]({{page.wally-appstore}}) today. You can find more information about the app on it's [website]({{page.wally}}).

Overall, I am really excited about the future of app development on Apple's platforms. I'd love to hear what you think of the post and about Apple's tech stack in general, so don't hesitate to leave a comment.