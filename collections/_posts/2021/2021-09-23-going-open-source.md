---
title:  Going open-source
date:   2021-09-23 07:00:00 +0100
tags:   swift open-source closed-source
---


In this post, I'll discuss my journey with open-sorce and how I continously try to extract functionality from my various projects into public, open-source libraries. I'll reason around why I think this is good (and fun), the steps involved, some perhaps unexpected drawbacks, and when you should consider creating internal or closed-source libraries instead of public ones.


## Background

I have created many apps over the years, and before that backend systems, apis and websites. There is nothing I enjoy as little as writing the same code twice. Perhaps copying code and watching the different copies diverge over time is even worse.

I therefore started creating public libraries pretty early on, where the first I remember was an ambitious tool for ASP (Active Server Pages) and later PHP. I started building it in the early 2000s, with an aim to bridge front-end and backend by generating JavaScript entities from the corresponding backend types, that could trigger the same functionality in an async manner (AJAX, anyone?).

Wigbi (Watch It Grow By Itself, as it was called) wasn't open-source, but was released as obfuscated code, since I intended to build a product out of it. Still, building it for public use forced me to focus on tasks that I wasn't all that familiar with from just writing code. Documentation, versioning, configurability, bug reporting, building a web site - there was so much involved than just coding.

Wigbi had tons of functionality and even a built-in CMS, but never made it to a commercial product. One contributing factor was that I strived for perfection and didn't have a clear definition of done, which led me to decline showing Wigbi to a potential buyer, since it "wasn't done yet". Instead of showing what I had at the moment, present a project plan and describe my vision etc. I just said "no".

To sum up, I learned a LOT from working on Wigbi, not just coding. It made me a much better developer, a less naïve product developer and (most importantly) was so much fun!


## Today

Fast forward to today, and open source is everywhere. Services like GitHub and dependency managers (e.g. Swift Package Manager, Gradle, NPM etc.) have made it easy to create, maintain and distrivute open-source projects, host documentation etc.

Although I eventually ended Wigbi, I have kept working on open-source projects ever since. Today, I more or less do it out of habit, when I create something that may be useful to others. Most of the libraries are just there for me and my own apps, but if they can help others, that's great.

It's worth repeating that there is a considerable overhead to create open-source software, than just writing code. If you build software that you intent others to use, you will have to deal with documentation, onboarding developers, versioning, release management, deprecations, breaking changes etc. 

Although this may not always be fun per se, in my experience having to consider these parts will help you evolve your skills and let you work on things that you may not normally get to do at your day job. You will learn a lot...although you may come out a little more gray-haired on the other side.


## My current projects

Before we continue, let me list some of my open-source projects that I currently maintain, to give you an idea of the kind of software that I create.

* [BottomSheet](https://github.com/danielsaidi/BottomSheet) - A library for creating customizable bottom sheets in SwiftUI.
* [DeckKit](https://github.com/danielsaidi/DeckKit) - A library for creating deck-based cards in SwiftUI.
* [KeyboardKit](https://github.com/KeyboardKit/KeyboardKit) - A library for creating custom keyboards in SwiftUI.
* [MockingKit](https://github.com/danielsaidi/MockingKit) - My most favorite library, used to create real, dynamic test mocks in Swift.
* [Sheeeeeeeeet](https://github.com/danielsaidi/Sheeeeeeeeet) - A library for creating customizable action sheets in UIKit.
* [SwiftKit](https://github.com/danielsaidi/SwiftKit) - A library in which I put general, reusable Swift code.
* [SwiftUIKit](https://github.com/danielsaidi/SwiftUIKit) - A library in which I put general, reusable SwiftUI code.
* [SystemNotification](https://github.com/danielsaidi/SystemNotification) - A library for creating customizable system notifications in SwiftUI.
* [Tutti](https://github.com/danielsaidi/Tutti) - An library for creating different onboarding experiences, hints and tutorials.

These are projects that I work on every now and then, although most of them are only revisited when something like this happens:

* I get an idea for a new feature or improvement.
* I find a bug or something that needs fixing.
* Users post an issue (bug of feature request).
* Users send me a PR.
* The library must be updated due to device or platform changes (new iOS, iPhone etc.)

Only a few of these libraries are frequently updated, but I use most of them in every app that I make.


## Reasons to go open-source

You may wonder why you'd like to move code out of your app into an open-source project. Here are some reasons that I come to think of top of mind.

* **Reusability** - reusing the same solution saves you time and effort.
* **Reduce cognitive load** - reusing means your projects will start to behave more the same.
* **More focus** - if an app only contains app-specific logic, it will be more focused.
* **Less distraction** - if an app only contains app-specific logic, you will be less distracted.
* **Faster to compile** - libraries tend to compile faster than big, bloated apps.
* **Easier to unit test** - libraries are in general easier to test, since they encourage building smaller components in an abstract way.
* **Improve your communication skills** - If you go open-source, be ready to having to think about what your code really does, then document it for others to understand.
* **Improve your planning skills** - when your code is used by others, you can't just change what you want, how you want, whenever you want, but will have to plan ahead.
* **Improve your collaboration skills** - in my opinion, few things beat collaborating with others and gather around a complex problem.
* **Learn from others** - collaborating with others means learning from others.
* **Sharing is caring** - creating something that can help others truly is amazing.

There are naturally so many more gains involved with open-source, but I think this gives you an idea of why I love working on open-source projects.


## Reasons to NOT go open-source

However much I love open-source, you don't have to go *open-source* to reuse common logic or share your work with the world. Some reasons to not go open-source can be:

* **Time** - maintaining open-source takes time, time that you perhaps not have.
* **Intellectual property** - if you create something truly unique, perhaps you should protect it?
* **Work secrets** - make sure to not open-source work-related stuff without proper approval.
* **High volatility** - a system that's frequently changing may not be the candidate for open-source.

With that said, you can still work on project in an open-source manner, while keeping the implementation details private or release it as closed-source.


## Private projects

If you want to keep your libraries private while using GitHub, you can create a private repository with a Swift Package (or your choice of dependency manager), add your code, write your tests etc. but use SSH instead of HTTPS. This gives you granular control over who can use the library.

Private repositories are also a great way to start practicing your open-source skills or start working on a new project. It saves all the documentation effort for when you have something that you think is ready for realse. When you're ready, just make the repo public for the world to use.


## Closed-source

As I mentioned before, closed-source may be an alternative if you have created something that you want to share with others, while still keeping the implementation secret.

Going closed-source gives you an opportunity to build a business around your library or create new business opportunities for your company. For instance, if an app use a sophisticated piece of software, that software may have a business value in itself. Relasing it with a commercial licence can help you make business on that software alone.

If this sounds interesting, I have written a [separate blog post](https://danielsaidi.com/blog/2021/02/15/distributing-closed-source-frameworks-with-spm) about how to work with closed-source software in Swift and with the Swift Package Manager.


## Good practices

When moving things from private projects to open-source libraries, keep these things in mind:

* **Access control** - consider what should be public and what should be private.
* **Only expose what's essential** - the things you make public are harder to change later.
* **Design with abstractions** - have your library communicate *what* it does, not *how* it does it.
* **Loose coupling** - avoid relying on concrete types to make your library flexible and easier to test.
* **Test, test, test** - a nice test foundation will help you verify that things don't break between releases.
* **Automate** - automate things like linting, versioning, release management etc. as much as possible.

I have probably overlooked a bunch of things. Feel free to add more in the comment section below.


## Conclusion

When you go open-source, your contact with other developers will hopefully be a fun and rewarding one. If your repository starts to gain traction, you may also find yourself to be the founder of a community, with a lot of activity and amazing work being made by others.

However, this can quickly turn into a second job. Throw in a bunch of users who expect you to fix bugs and new features and things may become too much. I have the luxury of having a few libraries with some traction, and that's amazing, but I occasionally have users expecting me to urgently fix things when I am busy with other projects. 

Keeping the work-life-hobby-coding balance can be challenging.

First of all, set clear expectations on yourself. Why are you doing this, what are your motivations and ambitions and how much time and energy are you willing to put in? You can then communicate this to others. Dedicate a section of the readme to yourself and what you expect of yourself and others. Communicating this will make it easier for you to handle these situations and for others to know what to expect from you.

Unexpected side-effects can be positive ones as well. For instance, I started a project with no other goals than to explore keyboard extensions for iOS. Over time, this has grown to a project where I have several amazing collaborators and clients, which made it possible for me to start my own company. 

Open-source can open up many doors and, for real, change your life.