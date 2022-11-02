---
title:  Going open-source
date:   2021-09-23 07:00:00 +0100
tags:   open-source closed-source swift spm
icon:   avatar
---

In this post, I'll discuss my experiences of working on various open-source projects, some of the steps and processes involved as well as some learnings.


## Background

I have created many apps over the years, and before that backend systems, apis and websites. There is nothing I enjoy as little as writing the same code twice. Perhaps copying code and watching the different copies diverge over time is even worse.

I therefore started creating public libraries pretty early on, where the first I remember was an ambitious tool for ASP (Active Server Pages) and later PHP. I started building it mid-2000, with an aim to bridge front-end and back-end by generating JavaScript entities from custom back-end types, that could trigger the same functionality in an async manner (remember AJAX?).

Wigbi (Watch It Grow By Itself, as it was called) wasn't open-source, but released as obfuscated code, since I intended to make it a commercial product. Still, building it for public use forced me to work on tasks outside of my comfort zone, such as documentation, configurability, semantic versioning, release management, bug handling etc. - there was so much involved than just coding.

Wigbi had tons of functionality and even a built-in CMS and theme engine, but never made it to a commercial product. One contributing factor was that I strived for perfection and didn't have a clear definition of done, which led me to decline showing Wigbi to potential buyers, since it "wasn't done yet". Instead of showing what I had, present a project plan and describe my vision etc. I just said "no".

To sum up, I learned a LOT from working on Wigbi, not just coding. It made me a much better developer, a less naïve product developer (although I still suck at sales) and most importantly - was so much fun!


## Today

Today, and open source is everywhere. Services like GitHub, GitLab and BitBucket make it simple to host and share code. Dependency managers like Swift Package Manager, Gradle and NPM etc. make it easy to distribute versioned open-source projects. DocC, Doxygen and GitHub Pages makes it super simple to host documentation. And so on. The ecosystem is thriving.

Although I eventually sunset Wigbi, I have kept building open-source projects ever since. Today, I more or less do it out of habit, when I create something that may be useful to others, or useful to myself in other projects. Reusability is my focus, but if what I create can help others, that's of course amazing.


## My projects

Let me list some of my current projects, to give you an idea of the kind of libraries that I create.

* [BottomSheet](https://github.com/danielsaidi/BottomSheet) - A library for creating customizable bottom sheets in SwiftUI.
* [DeckKit](https://github.com/danielsaidi/DeckKit) - A library for creating deck-based cards in SwiftUI.
* [KeyboardKit](https://github.com/KeyboardKit/KeyboardKit) - A library for creating custom keyboards in SwiftUI.
* [MockingKit](https://github.com/danielsaidi/MockingKit) - My most favorite library, used to create real, dynamic test mocks in Swift.
* [Sheeeeeeeeet](https://github.com/danielsaidi/Sheeeeeeeeet) - A library for creating customizable action sheets in UIKit.
* [SwiftKit](https://github.com/danielsaidi/SwiftKit) - A library in which I put general, reusable Swift code.
* [SwiftUIKit](https://github.com/danielsaidi/SwiftUIKit) - A library in which I put general, reusable SwiftUI code.
* [SystemNotification](https://github.com/danielsaidi/SystemNotification) - A library for creating customizable system notifications in SwiftUI.
* [Tutti](https://github.com/danielsaidi/Tutti) - An library for creating different onboarding experiences, hints and tutorials.

These are projects that I work on every now and then, although most of them are only revisited when I get an idea for a feature or improvement, have to fix a bug, get feedback or pull requests from the community or need to update the library due to external changes (e.g. new iOS version).


## My work structure

The way that code I create in an app ends up in an open-source library follows a pretty standard path.

I first of all try to only have app-specific code in my app target, which means that I from start create a framework target within the app target, often suffixed with the name `Kit`. This means that for the Wally app, I have a framework in the same project called `WallyKit`.

I then place everything that isn't app-specific in this framework. And here, with app-specific, I mean things that are specific to a certain app, not it's domain or business model. So, in Wally, which is all about keeping a digital copy of your wallet, the domain model with wallets and cards, database logic etc. would be placed in `WallyKit`, while localizations, views etc. would be placed in the app target. 

Furthermore, the framework must have no external dependencies, which means that implementations of e.g. the database layer often ends up in the app-specific target, since that's where I pull in dependencies like Firebase. This makes the framework solely focused on the app's domain model, and having no external dependencies makes testing very fast.

I also generally test the framework thoroughly, but often leave the code in the app target untested, since that logic is mostly view logic and extensions to types that are already tested in the framework. This means that as much logic as possible should be in the framework.

Once my work is done, either by launching a new app or a new version of an already existing app, there may be general value in both the app and the framework. If I have an open-source library that fits something that I've created, I can move that logic from the app project to the library. If I've created something general that can become it's own thing (as I did with [DeckKit](https://github.com/danielsaidi/DeckKit) after creating [Lunchrrrrr](https://apps.apple.com/se/app/lunchrrrrr/id1209779063?l=en)), I create a new open-source project.

I also sometimes extract logic to private repositories, that I only use in my own projects. This is great if you want to create standard things for your own apps, that is of little use to others, as well as if you're not up for all the work involved in open-source. More on that later.


## App-specific frameworks

You don't have to go open-source to benefit from many good practices that open-source also brings. Sometimes, it's enough to move code from your app target to a separate framework in the same project. 

Some benefits include:

* **Reusability** - reusing the same solution saves you time and effort.
* **More focus** - if an app only contains app-specific logic, it will be more focused.
* **Less distraction** - if an app only contains app-specific logic, you will be less distracted.
* **Faster to compile** - libraries tend to compile faster than big, bloated apps.
* **Easier to unit test** - libraries are in general easier to test, since they encourage building smaller components in an abstract way.
* **Less cognitive load** - reusing the same functionality means your projects will start to behave more the same, which means that you don't have to re-learn as much when you revisit old code.

You will also get to learn about things like access modifiers (what should be public, internal, private etc.), linking frameworks with your apps, importing etc. which will make you a better developer overall and helps preparing the code if you want to move it out of the app later.

Once you have code in a framework, you can easily take further steps and start creating your own local packages, e.g. which makes it even easier to reuse functionality across apps.


## Local packages

If you want to really separate the app from the framework, you can create local packages within the app project folder, then use SPM to add the local packages to your project.

Local packages further increase modularity and separation of concerns between the app and the package, compared to having the framework in the same project. It's a pretty new SPM feature, and one that I have started using more and more since it was released.

Another benefit with local packages and the modularity they bring, is that you can easily move the package somewhere else and start using it as a private package in more projects, and even make it open-source very easily.


## Open-source

However great the practice of extracting code into frameworks and local packages is, open-source brings additional benefits to the table, for instance:

* **Better code** - If you decide to go open-source, you probably also tend to put more time into making the more readable, more general etc.
* **Communication** - When you go open-source, you get to view your code from the outside as you describe for others what it does and how it works.
* **Planning** - when your code is used by others, you can't just change what you want, how you want, whenever you want, but have to plan ahead.
* **Collaboration** - few things (in my opinion) beat collaborating with and learning from other developers as you gath around exciting ideas and problems.
* **Sharing** - creating something that ends up helping others, is in my opinion truly amazing.

There are naturally so many more gains involved with open-source, but I think this gives you an idea of why I love working on open-source projects.


## Reasons to NOT go open-source

However much I love open-source, it's not for everyone. Some reasons to not go open-source can be:

* **Time** - maintaining open-source takes time, time that you perhaps not have.
* **Intellectual property** - if you create something truly unique, perhaps you should protect it?
* **Work secrets** - make sure to not open-source work-related stuff without proper approval.
* **High volatility** - a system that's frequently changing may not be the candidate for open-source.

It's worth repeating that there is a considerable overhead involved with open-source, including having to work with documentation, dev onboarding, version planning, release management, deprecations etc. However, this will evolve your skills and let you work on things that perhaps don't get to do otherwise. 

You will learn a lot.

With that said, you can still work in an open-source manner, while keeping the implementation details private or release it as closed-source. For instance, you can still work in an open-source way within your company, to reuse components across teams, increase collaboratione etc.


## Private projects

If you want to keep your libraries private while using GitHub, you can create a private repository with a Swift Package (or your choice of dependency manager), add your code, write your tests etc. but use SSH instead of HTTPS. This gives you granular control over who can use the library.

Private repositories are also a great way to start practicing your open-source skills or start working on a new project. It saves all the documentation effort for when you have something that you think is ready for realse. When you're ready, just make the repo public for the world to use.


## Closed-source

As I mentioned before, closed-source may be an alternative if you have created something that you want to share with others, while still keeping the implementation secret.

Going closed-source gives you an opportunity to build a business around your library or create new business opportunities for your company. For instance, if an app use a sophisticated piece of software, that software may have a business value in itself. Relasing it with a commercial licence can help you make business on that software alone.

If this sounds interesting, I have written a [separate blog post](https://danielsaidi.com/blog/2021/02/15/distributing-closed-source-frameworks-with-spm) about how to work with closed-source software in Swift and with the Swift Package Manager.


## Good practices

If you decide to go open-source, make sure to keep these things in mind:

* **Access control** - what should be public and what should be private.
* **System design** - what story should your library tell?
* **Only expose the essential** - things made public are harder to change.
* **Design with abstractions** - communicate *what* your library does, not *how* it does it.
* **Loose coupling** - avoid relying on concrete types to make your code flexible and easier to test.
* **Test, test, test** - a nice test foundation will help you verify that things don't break between releases.
* **Automate** - automate things like linting, versioning, release management etc. as much as possible.

Many of these bullets are blog posts in themselves. For instance, with good unit tests in place, you can hopefully replicate user reported bugs, fix them and be confident that the fix didn't break anything. Automation applies to both your own workflows as well as automating community workflows, like validating pull requests.


## License

Before releasing your open-source project, it's very important to pick a fitting license, like MIT, GPL etc. In fact, code without an explicit license is NOT open-source and by default copyright protected, with all right given to the author of the code. 

Without a license in place, the author can add a restrictive license any time in the future, and you will have to comply with that change or stop using the code.


## Conclusion

When you go open-source, your contact with other developers will hopefully be a fun and rewarding one. If your repository starts to gain traction, you may also find yourself to be the founder of a community, with a lot of activity and amazing work being made by others.

However, this can quickly turn into a second job. Throw in a bunch of users who expect you to fix bugs and new features and things may become too much. I have the luxury of having a few libraries with some traction, and that's amazing, but I occasionally have users expecting me to urgently fix things when I am busy with other projects. 

Keeping the work-life-hobby-coding balance can be challenging.

First of all, set clear expectations on yourself. Why are you doing this, what are your motivations and ambitions and how much time and energy are you willing to put in? You can then communicate this to others. Dedicate a section of the readme to yourself and what you expect of yourself and others. Communicating this will make it easier for you to handle these situations and for others to know what to expect from you.

Unexpected side-effects can be positive ones as well. For instance, I started a project with no other goals than to explore keyboard extensions for iOS. Over time, this has grown to a project where I have several amazing collaborators and clients, which made it possible for me to start my own company. 

Open-source can open up many doors and, for real, change your life.