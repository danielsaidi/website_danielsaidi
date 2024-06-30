---
title: Building a multi-platform app in a protocol-driven way
date:  2020-02-11 00:00:00 +0100
tags:  watchos swiftui
icon:  swiftui

redirect_from: /blog/2020/02/11/apple-watch
---

SwiftUI and Combine makes it easy to build apps for watchOS. In this post, we'll take a look at the work involved in building a watchOS app in SwiftUI.


## Architecture

When I started my current job, the app was a giant monolith with all code in the app target.

I therefore established a long-term plan of taking the architecture to a state where it could power all parts of the Apple ecosystem, incl. iOS, watchOS, tvOS, macOS, CarPlay, etc.

Some key parts in this work has been to break the app up into frameworks, decoupling its components with protocol-driven development, unit testing as much as possible, etc. 

This has involved extensive planning to keep the product evolving and make it meet the needs of the business, while constantly refactoring its architecture.


## Frameworks

When I started this, all code was kept in the main app project. This caused all parts of the app to have full access to all other parts, which increased coupling, made the code hard to maintain and made unit testing painful. Actually, the unit tests didn't even work.

I started to move code from the app into external, decoupled and well-tested frameworks. We started with the audio player and now have 10+ frameworks that handle various parts the domain, API integrations, persistency, UI, test tools, etc.

Having most logic in external frameworks has been a huge productivity boost, since it lets us write new features and refactor old ones with short feedback loops, which is critical to make unit testing effortless. It also makes it hard to accidentally implement bad coupling.

Having the frameworks also meant that the new watchOS app already had a lot of its logic implemented, by just adding dependencies to the frameworks. Creating a new watchOS app that had support for user authentication, playing audio, etc. took less than half a day.

However, going from 90% to 100% took additional work, when you run into parts of a new platform that don't behave as expected.


## Protocol-driven development

Protocol-driven development is somewhat of a cliché in the community, but was critical to make our architecture flexible enough to be used in various scenarios and contexts.

Protocols let you focus on *what* you want to solve, instead of *how*, and lets you implement the *how* in different ways. It reduces coupling types and makes unit testing a lot easier. We use this approach together with dependency injection to get a lot of flexibility.

Our app lets users listen to audiobooks and read e-books When fetching a book from the api, we have a `BookService` protocol that defines how to do this, and an `ApiBookService` that implements this protocol by fetching the book from the API.

However, we can have many more implementations of this protocol and use the [decorator pattern](https://en.wikipedia.org/wiki/Decorator_pattern) to wrap implementations within eachother to achieve different results. For instance, we can wrap our API-specific service in a `CachedBookService` to get automatic caching. 

Being able to wrap a service within another lets us compose our logic in very flexible ways.

So while the main iOS app has offline support, the watch can cleaner service setup since it doesn't have offline support. However, both apps still speak the same language, since they use the same protocols. The code is identical, but the implementation can be very different.

Without protocol-driven development, it would have been a lot more difficult to implement a watch app with the same architecture as the main app.


## Unit testing

All code that goes into our frameworks must be well tested. We test most logic, parsing etc. which means that we can often put our unit tests in a certain state to replicate a problem.

We can then use our unit tests to fix a problem while being confident that our decoupled code makes it hard to accidentally create side-effects when we fix a bug.

By reusing logic in these frameworks, new apps get access to well-tested code out of the box. We reuse as much as possible, and extend existing code instead of rewriting things.


## The last 10%

Having this architecture in place made it easy for us to get a watchOS app up and running in hours. However, "up and running" is not production ready. There were architectural gaps that we had to fill to get the app ready for shipping.

The watch app is based on SwiftUI & Combine, which are two new technologies that are only available in iOS 13 & watchOS 6. Since our main app supports iOS 11, we had no support for these technologies in our architecture, when we started watch development.

Here, the protocol-driven approach proved essential. For instance, we needed a way to keep our UI in sync with our stores. This is trivial in SwiftUI and Combine, using published properties that drive the UI. However, our existing stores had no support for this.

We solved this by creating a new `ObjectContext` protocol with a single `object` property. We then created a `StandardObjectContext` that implemented `ObjectContext` with a regular `object` property, as well as an iOS 13+ `CombineObjectContext` with a published `object`.

With this new context concept, the team could start building the app using global contexts, while I could focus on finding a way to inject these contexts into our existing architecture.

I eventually found a way to create contextual store decorators that wraps any stores of the same kind and syncs its content with an injected object context. This means that we could enhance regular stores with SwiftUI/Combine features, building upon a well-tested base.
 
When this was done, we could extend the watch stores by adjusting the dependency setup in the watchOS app. No code in the app itself had to change.

Preparing the architecture to support SwiftUI and Combine was a challenge, but having the protocols and unit tests in place was a huge help.


## Wrapping up the app

We decided to keep the first version of the app as plain and simple as possible and release the most basic features until we learned enough to put more time into the app.

Things we wanted to learn before moving on included:

* How do you release a standalone watchOS app?
* Will Apple approve the app in its most basic form?
* How will users login on the watch, which lacks typing?
* Can the watch app share login session with the main app?
* How do we develop in SwiftUI and Combine for the watch?
* Are the any watch-specific limitations that we need to discover?

We therefore decided to limit ourselves to two root views: a list of the user's current books (replaced with a login screen if not logged in) and a main settings screen.

The book list lists book covers with a progress indicator, that shows how many percent of the book that a user has finished. We first had a details page for book-specific information and actions (e.g. play), but decided to replace with immediately playing a book.

We use global Combine-based contexts to drive the UI. For instance, a `LoginContext` tells the app if a user is logged in or not. We can then inject this context into our authentication service and have the service keep the context in sync.


## Result

The final app is a true MVP, from which we've learned so much. Its hard to stick to the plan and not add features you know users will request from day one, but it's better than putting a lot of work into an app that can't be released. 

It's going to be so much fun to be able to enhance the app as we learn more about, instead of ramming in a bunch of half-baked features at once.

I can really recommend looking into watchOS development in SwiftUI and Combine. It's great fun and so much easier than it used to be before.