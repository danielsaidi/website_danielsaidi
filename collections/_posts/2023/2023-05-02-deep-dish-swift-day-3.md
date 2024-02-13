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

[Tunde]({{page.tunde}}) talked about the new navigation APIs in SwiftUI 4, first touching on the drawback of the old APIs and how the new `NavigationStack` APIs drastically improve navigation.

If you are new to the `NavigationStack`, Tunde has some nice examples on how to structure the code, define navigation routes, handle programmatic navigation, deep links and more. 

This was a well structured and fun talk, although I would have preferred more in-depth content in the last part of the talk, instead of the personal content and channel marketing.

Fully agree on his definition of screens and views btw. 😀


## Ellen Shapiro

### Server-Side Swift And GraphQL: A Match Made in Heaven or Hell?

[Ellen]({{page.ellen}}) talked about Server-Side Swift And GraphQL. I really liked how she started with her intense dislike for complicated technological descriptions that can be expressed in a more inclusive way, and then held true by explaining what a server is, in a basic and fun way.

Ellen went through various technologies like Vapor, Fluent and GraphQL, what they are, what they do and how they can be used. This part was great, starting with the GraphQL type system (nullable by default, unlike Swift), operations (Query, Mutation, Subscription) and then taking a deep dive into the code. 

If you're interested in Vapor in and GraphQL in general and how they integrate in particular, this talk has a lot of good content.


## Zach Brass

### DeckUI: Coding your next presentation... in Swift?

[Zach]({{page.zach}}) talked about DeckUI - a custom DSL that lets you create presentations in Swift. While I use impress.js with Markdown to get as versatile and portable presentations as possible, I'm sure this can be a good alternative for some SwiftUI developers.

Using DeckUI lets you work in a similar way when creating presentations, which minimizes context switching. Also, you're able to use SwiftUI views like `MapView`.

The talk then transitioned into DSLs, which is a term that I'm not overly keen on when devs use about the stuff they build (discussion for another post?). The result builder section was nice, and I appreciated that Zach ended his talk with a few reasons why to *not* use DeckUI.


## Vince Davis

### Live Activities and Dynamic Island Made Simple

[Vince]({{page.vince}}) talked about Live Activities and the Dynamic Island, with a TestFlight app that let the audience try out a live activity. 

Vince went through how to set up a live activity in a very pedagogical way, then how to define a stale date to force the activity to update and how to use app intents to start live activities from outside the app.

Having not worked with live activities, this talk was interesting to me. I wish I had recorded it, since I didn't have time to write down most of the things Vince talked about. If you want inspiration, Vince suggested checking out Uber, Lyft & Flighty for some great activities.


## Mikaela Caron

### 13 Tips to Write Code like a Swiftie

[Mikaela]({{page.mikaela}}) talked about writing code like a Swiftie. She touched on many topics, like casing (PascalCasing, camelCasing etc.), naming conventions, argument labels, type aliases, documentation, access control, code structure, enums, namespaces etc. and many other great things to consider - all in a fun Taylor Swift context where I truly felt my age.

Mikaela's talk was very fun and impressively thorough, and I just loved how she highlighted incorrect and correct usages in a clear way, showing how to do things and how *not* to do it.

The talk was a bit opinionated, which is quite common in these kind of talks. I have equally opinionated views that contradict some recommendations in this talk. For instance, I find it a bit fun that a U.S. person advocates using U.S. and not U.K. naming.

I also think the protocol naming convention that was advocated in this talk and that is often used by the community (Calmable, Loveable...gosh, Taylor Swift was everywhere) is not a silver bullet. For instance, I tend to name my protocols differently, e.g. `MovieService` and not `MovieServiceable`. I also don't like using enums as namespaces, structs ftw.

The code structure part was probably the most opinionated part, where I think an absolute way to order and group your code depends on many different factors, such as the size of the file. But I've been highly opinionated in this myself, so I 100% see where it comes from.

I will however die on the hill where initializers are placed first...but will probably change my mind in a year or so. That's the beauty of opinions, they can change. 

Overall this was an amazing talk and I can really recommend that you watch it if you get a chance. Just make sure to listen to some Taylor Swift before you do.


## Simon B. Støvring

### Documenting Your Project With DocC

[Simon]({{page.simon}}) talked about building rich documentation with DocC. It covered things like structure, articles and tutorials to how to host your documentation.


## Ben Proothi

### Machine Learning in Swift: Practical, Trendy, or Both?

[Ben]({{page.ben}}) talked about the future or Machine Learning and how to use Swift as a performance boost over Python. It was a fun and very professional talk. 

I'm blown away by the fact that Ben is just 17 years old. The future looks bright, Ben!