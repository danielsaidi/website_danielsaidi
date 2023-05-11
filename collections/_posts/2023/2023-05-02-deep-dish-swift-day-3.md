---
title:  Deep Dish Swift - Day 3
date:   2023-05-02 06:00:00 +0000
tags:   conference

image:  /assets/headers/deepdish.png

tweet:  
toot:   

tunde:  https://twitter.com/tundsdev
ellen:  https://twitter.com/designatednerd
zach:   https://twitter.com/zhbrass
vince:  https://twitter.com/vincedavis
mikaela:    https://twitter.com/mikaela__caron
simon:  https://twitter.com/simonbs
ben:    https://twitter.com/benproothi
---

Deep Dish Swift flew by and suggendly, the third and final day was here...way to soon. I'm impressed by the scale of this first edition of Deep Dish Swift, and really hope there will be a second installment next year. Let's see what Day 3 had in store.

![DeepDish Swift logo]({{page.image}})


## Tunde Adegoroye

### My NavStack brings all the boys to the yard

[Tunde]({{page.tunde}}) talked about the new navigation APIs in SwiftUI 4, first touching on the drawback of the old APIs and how the new `NavigationStack`-based APIs drastically improve the navigation experience.

If you are new to the `NavigationStack`, Tunde has some nice examples on how to structure the code, define navigation routes, handle programmatic navigation, deep links and much more. It was a very well structured and fun talk, although I would have preferred more in-depth, topic-related content in the last part of the talk, instead of the personal content and channel marketing.

Fully agree on his definition of screens and views btw. 😀


## Ellen Shapiro

### Server-Side Swift And GraphQL: A Match Made in Heaven or Hell?

[Ellen]({{page.ellen}}) talked about Server-Side Swift And GraphQL. I really liked how she started with her intense dislike for complicated technological descriptions that can be expressed in a more inclusive way, and then held true to that by explaining what a server is, in a basic (perhaps a bit too basic) and approachable way.

Ellen then went through technologies like Vapor, Fluent and GraphQL, what they are and do and how they can be used. This section was very nice, starting with the GraphQL type system (nullable by default, opposite to Swift), operations (Query, Mutation, Subscription) and then taking a deep dive into the code. If you are interested in Vapor in and GraphQL in general and how they integrate in particular, this talk has a lot of good content.


## Zach Brass

### DeckUI: Coding your next presentation... in Swift?

[Zach]({{page.zach}}) talked about DeckUI, which is a custom DSL that lets you create presentations in Swift. While I use impress.js with plain Markdown to get as versatile and portable presentations as possible, I'm sure this can be a good alternative for some SwiftUI developers who want to work in a similar way when creating their presentations, to minimize context switching. Also, you're able to use SwiftUI views like `MapView`.

The talk then transitioned into DSLs, which I think is a term that I'm not to keen about when developers use about the stuff they build (discussion for another post?). The result builder section was pretty nice, and I truly appreciated that Zach concluded the talk with a list of reasons why to *not* use DeckUI.


## Vince Davis

### Live Activities and Dynamic Island Made Simple

[Vince]({{page.vince}}) talked about Live Activities and the Dynamic Island, with a TestFlight app that let the audience try out a live activity. Vince went through how to set up a live activity in a very pedagogical way, then how to define a stale date to force the activity to update and how to use app intents to start live activities from outside the app.

Having not worked with live activities, this talk was full of good to knows for me. This was the one talk I wish was recorded, since I was fully focused on the talk and didn't have time to write down most of the things Vince talked about. If you want some live activity inspiration, Vince suggested that we check out Uber, Lyft, Flighty for examples of great activities.


## Mikaela Caron

### 13 Tips to Write Code like a Swiftie

[Mikaela]({{page.mikaela}}) talked about writing code like a (Taylor) Swiftie. In her talk, she touched on many topics, like for instance casing (PascalCasing, camelCasing etc.), naming conventions, argument labels, type aliases, documentation, access control, code structure, enums, namespaces etc. and many other great things to consider - all in a fun Taylor Swift context, where I truly felt my age.

Mikaela's talk was very fun and impressively thorough, and I just loved how she highlighted incorrect and correct usages in a very clear way. showing examples of both how to do things and how not to do it.

The talk was a bit opinionated in parts, which is far from common in these kind of talks. I have equally opinionated views that contradict many recommendations given in this talk. For instance, I find it a bit fun that a U.S. person advocates using U.S. and not U.K. naming. To me, it depends on the domain. 

I also think the protocol naming convention that was advocated in this talk and that is often used by the community (Calmable, Loveable...gosh, Taylor Swift was everywhere) is not a silver bullet. For instance, I tend to name my protocols in a different way, e.g. `MovieService` instead of `MovieServiceable`. I also don't like using enums as namespaces, but that's just a nitpick.

The code structure part was probably the most opinionated part, where I think an absolute way to order and group your code depends on many different factors, such as the size of the file. But I've been highly opinionated in this regard myself, so I see where it is coming from.

I will still die on the hill where the initializers are placed first, but will probably change my mind in a year. That's the beauty of opinions, they are allowed to change. 

Overall this was a great talk and I can really recommend that you watch it if you get a chance. Just make sure to listen to some Taylor Swift before you do.


## Simon B. Støvring

### Documenting Your Project With DocC

[Simon]({{page.simon}}) talked about building rich documentation with DocC. It covered everything from structure, articles and tutorials to how to generate and host your documentation, so make sure to watch this talk if you are interested in this topic.


## Ben Proothi

### Machine Learning in Swift: Practical, Trendy, or Both?

[Ben]({{page.ben}}) talked about the future or Machine Learning and how to use Swift to get a performance boost over Python. A fun and very professional talk. I'm blown away by the fact that Ben is just 17 years old. I would have been impressed if he was a seasoned, experienced developer, but this is almost uncanny.

The future looks bright, Ben!