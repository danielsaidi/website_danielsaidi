---
title:  Going open-source
date:   2021-09-23 07:00:00 +0100
tags:   swift open-source closed-source
---


In this post, I'll discuss how I try to continously extract functionality from my various app projects into public, open-source libraries. I'll reason around why I think this is good (and fun), the steps involved, some perhaps unexpected drawbacks, and when you should consider creating internal or closed-source libraries instead of public ones.


## Background

I have created many apps over the years, and before that backend systems, web apis and websites. There is nothing I enjoy as little as writing the same code twice. Perhaps copying code and watching the different copies diverge over time is even worse.

Today, the Swift Package Manager and GitHub makes it so easy to create open-source that I more or less do it out of habit when I create something that I think may be useful to others. Most of the libraries are just there for me and my own apps, but if they can help others, that's great.

There is a certain overhead to creating open-source libraries, if you decide to make them public. This includes describing your project in a readme (or several), handle release notes, document your code, plan releases, deprecations and breaking changes etc. However, although this may not always be fun (I think so), I think this will help you evolve your skills and let you try out things that you normally don't get to do at your day job...albeit you'll come out a little more wrinkly and gray-haired on the other side.


## My current projects

Before I go through the things I think are important when creating open source projects, let me start with listing the libraries that I actively maintain as open-source, to give you an idea.

* [BottomSheet](https://github.com/danielsaidi/BottomSheet) - A library for creating customizable bottom sheets in SwiftUI.
* [DeckKit](https://github.com/danielsaidi/DeckKit) - A SwiftUI library for creating deck-based cards.
* [KeyboardKit](https://github.com/KeyboardKit/KeyboardKit) - A SwiftUI library for creating custom keyboards for iOS and iPadOS.
* [MockingKit](https://github.com/danielsaidi/MockingKit) - My most favorite library, used to create real, dynamic test mocks in Swift.
* [Sheeeeeeeeet](https://github.com/danielsaidi/Sheeeeeeeeet) - A library for creating customizable action sheets in UIKit.
* [SwiftKit](https://github.com/danielsaidi/SwiftKit) - A library in which I put most of the general, reusable Swift things I create.
* [SwiftUIBlurView](https://github.com/danielsaidi/SwiftUIBlurView) - A library for SwiftUI 1 and 2, to create system blur views.
* [SwiftUIKit](https://github.com/danielsaidi/SwiftUIKit) - A library in which I put most of the general, reusable SwiftUI things I create.
* [SystemNotification](https://github.com/danielsaidi/SystemNotification) - A library for creating SwiftUI notifications that look like the iOS system ones.
* [Tutti](https://github.com/danielsaidi/Tutti) - An onboarding library for creating different onboarding experiences, hints and tutorials.

These are projects that I return to every now and then, although most of them are only worked on when something like this happens:

* I find a bug or need something new in my own apps
* Users post an issue (bug of feature request)
* Users send me a PR with improvements
* The library needs to be updated due to device or platform changes (new iOS, iPhone etc.)

Only a few of these libraries are frequently updated, but I use most of them in every app that I make.


## Reasons to go open-source

You may wonder why you'd like to move code out of your app into an open-source project. Here are some reasons that I come to think of top of mind. Please comment with more :)

* Reusability - you can reuse the same solution in many apps, thus harmonizing your work...and life.
* Reduce cognitive load - reusing he same utils and services in many apps means that they will start to behave more the same.
* Separate common logic from app-specific one - This is a big win. If your apps only contain app-specific logic and pull in common logic from app-agnostic libraries, they will be more focused.
* Less distractions - If you don't have to think about common logic when developing your apps, you will be less distracted. The same goes in your open-source projects. Once again: focus.
* Easier to unit test - Libraries are (in general) easier to test, since they encourage building smaller components in an abstract way. Sure, you *can* still build things in a non-testable way, but at least you will not have all that app-specific code to help you build spaghetti code or jenga towers.
* Faster to unit test - Libraries build faster than those big, bloated apps of yours, making unit testing faster and much more enjoyable.
* Improve your communication skills - If you go open-source, be ready to having to think about what your code really does, then document it for others to understand.
* Improve your planning skills - When you have an open-source library, you can't just change what you want, how you want, whenever you want to. You'll be forced to plan ahead.
* Sharing is caring - If you create something that can help others, wouldn't that be amazing?
* Sharing is fun - In my opinion, few things beat discussing problems with others, watch how others contribute to make what started out as your own little thing into a community project.

There are naturally so much more that I probably forgot to mention in the code above, and some of the bullets may not seem enjoyable at all. I'm just speaking from personal preference, and I absolutely love all these aspects of open-source development.


## Reasons to NOT go open-source

I want to emphasize that these are my personal opinions. I love open-source developement, but just keep in mind that you don't have to go OPEN-SOURCE to reuse your common logic in multiple projects. You can still create libraries that use open-source technologies, but keep them private.

If you use GitHub, this would mean that you create a repository, create a Swift Package (or your choice of dependency manager), add your code, write your tests etc. but keep the repository private. Then, as you pull in that library into your own apps, just use SSH instead of HTTPS.

This way, you can practice your open-source skills in private, and save all that documentation effort for when you have something that you think is really worth open-sourcing.

Other reasons to not go open-source can be:

* Lack of time - Maintaining open-source takes time. Make sure to not burn out. Read more below.
* Intellectual property - If you have created somethings truly unique, perhaps you should protect it?
* Work secrets - Make sure to not open-source work-related stuff or client secrets without considering it hard and getting the explicit approval.
* High volatility - If you are unsure of the current system design, concepts are changing etc. that may not be the best time to start documenting your code, spend time on readmes etc.

Another option, if you want to share the benefits with others or sell licenses to some sophisticated piece of software, without exposing your code, is to go closed source. I have written a [separate blog post](https://danielsaidi.com/blog/2021/02/15/distributing-closed-source-frameworks-with-spm) about how to work with closed-source software in Swift.


## Unexpected side-effects

When you go open-source, your first contacts with other developers will hopefully be fun and rewarding ones. If your repository starts to gain traction, you may find yourself to be the founder of a community, with a lot of activity, amazing work being made by others, perhaps even without your involvement etc.

However, this can quickly turn into a stressful situation and start to feel like a second job. Throw in a bunch of users who expect you to fix things and things can become even worse. I have the luxury of having a few libraries with a little(!) traction, and that's amazing, but I occasionally have users expecting me to urgently fix things when I am busy with other projects. Keeping the balance can be challenging.

First of all, set clear expectations on yourself. Why are you doing this, what are your motivations, how much time and energy are you willing to put into it? Once you have aligned your own expectations, you can start communicating it to others. Dedicate a section of the readme to yourself and what you expect of yourself and others. Clearly communicating this will make it easier for you to handle these situations and to clarify your intent to others.

Unexpected side-effects can be really positive ones as well. For instance, I started the KeyboardKit project without any other goal than helping other developers work around the limitations that I found while developing keyboard extensions for iOS. Over time, this has grown into a project that I spend quite a lot of time on, with license-based additions, a brand new app and several amazing clients. Open-source can open up many doors and, for real, change your life.


## Good practices

When moving things from private app projects to open source libraries, keep these things in mind:

* Only expose what's essential. If you make everything public, it will be harder to redesig later.
* Design with abstractions. Protocols lets you define *what* something does. The implementation is *how* it's done. Make *what* the center of your library, and hide the how as much as possible.
* Avoid hard couplings. Once you have your protocols and system design in place, avoid relying on concrete types. Use protocols for loose coupling and easier testing.
* Test, test, test. I seldom unit test UI things (SwiftUI previews are a great replacement), but always make sure to setup a nice unit test suite for my library logic. It's fun and help you verify that things don't break between releases. This is especially important when you start accepting pull requests.
* Automate things like versioning, releases etc. Fastlane is a great and simple tool for this.

Once again, I have probably overlooked a bunch of things. Please add more in the comment section.


## Conclusion

I'm not sure if this was at all enjoyable to read, or even readable for that matter. I just wrote down things as they popped up while writing, but it sure was a fun text to write. I hope you did enjoy reading it, though. and don't think I'm too far off. If you do, or if you just want to elaborate or discuss further, I'd love to hear your thoughts in the comment field below.

All the best

Daniel Saidi