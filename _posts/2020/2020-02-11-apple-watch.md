---
title:  "Building an Apple Watch app in SwiftUI"
date:   2020-02-11 00:00:00 +0100
tags:   watchos swiftui combine
---

SwiftUI and Combine makes it amazingly easy to build apps for watchOS. However, if the app is part of a larger system, you may have to adjust your architecture. In this post, we'll take a look at the work involved in building BookBeat's SwiftUI/Combine-based app for Apple Watch.


## Architecture

From the day I started at BookBeat, I've had a long-term plan of taking the iOS architecture to a state where it could power all parts of the Apple ecosystem, from iOS and watchOS to tvOS and macOS. This has involved much planning, juggling refactorings with new features and keeping the product evolving, while constantly refactoring and refining its underlying architecture.

Some key parts in this process has been to break up the architecture into smaller frameworks, decoupling components with protocol-driven development, unit testing as much as possible etc. Let's go through these separately.


## Frameworks

When I started at BookBeat, all code was kept in the main app project. This causes all parts of the system to have full access to all other parts, which increased coupling, made the code hard to maintain and made unit testing painful.

Since then, continuously moving out code from the app to external, decoupled and well-tested frameworks has been a key focus. We started with the audio player and now have 10+ frameworks that handle our domain model, domain logic, api integrations, persistency, UI etc.

Having most logic in external frameworks has been a huge productivity boost, since it lets us write new features and refactor old ones with short feedback loops, which is critical to make unit testing effortless. It also makes it hard to accidentally implement bad coupling.

Having the frameworks also meant that the new watchOS app already had 90% of its logic implemented, by just adding dependencies to the frameworks. Creating a new watchOS project and have it display the user's book in a list, playing any book the user tapped, took less than half a day.

However, it's a long way to go from 90% to 100%, when the existing architecture isn't designed with new tools and technologies in mind. Please read on.


## Protocol-driven development

This term is somewhat of a cliché in the Swift community, but it has been critical to make our architecture flexible enough to be used in various scenarios and contexts.

Protocols let you focus on *what* you want to solve, instead of *how* you solve it and lets you implement the *how* in many different ways. It reduces coupling between concrete types and makes unit testing a lot easier. We use this approach together with dependency injection to get a lot of flexibility.

For instance, say that we want to fetch a book from the api. For this, we have a `BookService` protocol that defines how to do this. We then have an `ApiBookService` that implements this protocol by fetching the book from the api. However, we can create more implementations of this protocol and use the [decorator pattern](https://en.wikipedia.org/wiki/Decorator_pattern) to wrap implementations within eachother to achieve different results. For instance, we can have a `CachedBookService` that uses a cache to avoid roundtrips to the server. Being able to wrap one service within another lets us compose our logic in very flexible ways.

For instance, while the main app has offline support etc. which require a certain setup, the watch can have a much cleaner setup since it currently doesn't have offline or download support and doesn't need the same configuration as the main app. However, both apps still speak the same language, since they use the same protocols. The code is therefore identical, but the underlying logic can be very different.

Without protocol-driven development, it would have been a lot more difficult to implement a watch app with the same architecture as the main app.


## Unit testing

All code that goes into our frameworks must be extensively tested. We test logic, parsing etc. which means that we can (often) put the unit tests in a certain state and replicate problems that we find, then use our tests to fix the problem while being confident that our tests and decoupled code makes it hard to accidentally create side-effects when we fix a bug.

By reusing logic in these frameworks, the watch app gets access to well-tested code out of the box. We therefore want to reuse as much logic as possible, and extend it with new capabilities instead of rewriting things from scratch.


## The last 10%

As I previously mentioned, having this architecture in place made it easy for us to get a watch app up and running within hours. However, "up and running" is not production ready. There were gaps in our architecture that we had to fill.

The watch app is based on SwiftUI and Combine - two new technologies that are only available in iOS 13 and watchOS 6. Since our main app supports iOS 11, we had no support for these new technologies in our architecture, when we started the watch development.

Here, the protocol-driven approach proved essential. For instance, we needed a way to keep our UI in sync with our stores. This is trivial in SwiftUI and Combine, using published properties that drive the UI. However, our existing stores had no support for this.

We solved this by creating a new `ObjectContext` protocol with a single `object` property. We then created a `StandardObjectContext` that implemented `ObjectContext` with a regular `object` property, as well as a `CombineObjectContext` that had a published `object` property.

With this new context concept, the team could start building the app using global contexts, while I could focus on finding a way to inject these contexts into our existing architecture.

I eventually found a way to create contextual store decorators that wraps any stores of the same kind and syncs its content with an injected object context. This means that we could enhance regular stores with SwiftUI/Combine features, building upon already well-tested logic. When this was done, we could extend the stores in the watch by just adjusting the dependency configuration. No code in the app had to change.

Preparing the architecture to support SwiftUI and Combine was a challenge, but having the protocols and unit tests in place was a huge help.


## Wrapping up the app

We decided early on to keep the first version of the app as plain and simple as possible, releasing the most basic features until we learned enough to put more time into the app.

Things we wanted to learn before moving on included:

* How do you release a standalone watchOS app that has a corresponding iOS app?
* Will Apple approve the app in its most basic form?
* How do users login on the watch?
* Can the watch app share login session with the main app?
* How to develop in SwiftUI and Combine for the watch
* Does our architecture actually work on the watch?
* Are the any watch-specific limitations we need to discover?

We therefore decided to limit ourselves to two root views - a list of the user's current reads (replaced with a login screen if the user is not logged in) and a settings screen with some nested screens.

The book list is a basic list with square book covers (cropping out any vertical or horizontal overflow) with a progress indicator. We first had a details page that featured book-specific information and actions (e.g. play), but honored the mvp approach and replaced this screen with immediately playing a book when its tapped in the list.

We then use the global Combine-based contexts I mentioned before to drive the UI, which means that we e.g. have a `LoginContext` that tells the app whether or not the user is logged in. We can then inject this context into our authentication service and have the service keep the context in sync. The same goes for a bunch of object contexts that we inject into our stores.


## End result

The final app is a true MVP, from which we've learned so much. Its hard to stick to the plan and not add features you know users will request from day one, but it's better than putting a lot of work into an app that can't be released. It's also going to be so much fun to be able to enhance the app as we learn more about, instead of ramming in a bunch of half-baked features at once.

I can really recommend looking into watchOS development in SwiftUI and Combine. It's great fun and so much easier than it used to be before.