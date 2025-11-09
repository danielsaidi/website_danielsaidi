---
title:  Going open-source
date:   2021-09-23 07:00:00 +0100
tags:   conferences
icon:   avatar
---

In this post, I'll discuss my experiences of working on various open-source projects, some of the steps and processes involved as well as some learnings.


## The First

I have created many apps over the years, and before that backend systems, APIs and web stuff. There is nothing I enjoy as little as writing the same code twice.

I therefore started creating public libraries pretty early on. The first I put out for public use was an ambitious tool for ASP (Active Server Pages) and later PHP. Yeah. I'm old.

I started building it mid-2000, with an aim to bridge frontend and backend by generating its JavaScript entities from custom back-end types, that could trigger the same functionality in an async manner (remember AJAX?), providing app-like client features. It was pretty cool.

Wigbi (Watch It Grow By Itself) wasn't open-source, and since I intended to release it as a commercial product and neither PHP nor JavaScript't compiled into a binary, I had to look into fun things like obfuscated code.

Also, building it for public use forced me to work on tasks outside of my comfort zone, such as documentation, configurability, semantic versioning, release management, etc.

Wigbi never made it to a commercial product. One contributing factor was that I strived for perfection and didn't have a clear definition of done, which led me to decline presenting it to potential investors, since it "wasn't done yet".

Instead of showing what I had, which was a pretty great thing, and getting it into the hands of users, present a project plan and describe my vision to investors, etc. I just said no. It's definitively one of the biggest regrets (and learnings) as a young professional.

I learned a LOT from working on Wigbi, not just coding. It made me a better developer, a less naïve product builder (although I still suck at sales) and was so much fun!


## Current Projects

Today, open-source is everywhere. Services like GitHub, GitLab & BitBucket make it easy to host and share code. Package managers like Swift Package Manager, Gradle & NPM make it easy to distribute it. DocC, Doxygen & GitHub Pages let you host documentation. And so on. The ecosystem is thriving.

Although I eventually sunset Wigbi, I kept building open-source projects. I more or less do it out of habit, when I create something that may be useful to others, or useful to my other projects. Making something open is a good opportunity to put that extra quality gear in.

Today, I mostly do stuff for Swift and SwiftUI. You can check out my [open-source](/opensource) list for a list of some projects. Most are only revisited when I need something, get a feature request or bug report, or need to update the library due to external changes (e.g. new iOS version).


## A First Step

If you want to do open-source work, but are unsure where to start, you don't have to go all-in at first. You can start adopting an open-source mindset in your current projects to benefit from many good things that open-source brings.

For instance, you can start extracting general code that is not specific to your app from the app project to a local Swift package that lives next to the app project. This will let you start thinking about decoupling things, having to consider access scope (what should be public, internal, private, etc.), which will help you reach a better system design.

If you start separating code into more packages, e.g. domain logic into a model package, UI components into a UI package, you also get to consider module design, like how the app depends on the UI, which depends on the model, which should not depend on other packages. This will make you a better system designer.

There are many benefits with starting to extract code into packages or modules:

* **Reusability** - reusing code saves you time and effort.
* **More focus** - if an app only contains app-specific logic, it will be more focused.
* **Less distraction** - if an app only contains app-specific logic, you will be less distracted.
* **Faster to compile** - packages compile faster than big, bloated apps.
* **Easier to unit test** - packages are in general easier to test, with shorter feedback loop
* **Less cognitive load** - reusing functionality lets your projects behave more alike.
* **SwiftUI Previews** - packages are lighter, which makes them ideal for SwiftUI previews.

You will also get to learn things like how to link frameworks with apps, importing modules, etc. which will make you a better developer and help you prepare for open-source.


## Open-source

Once you have gotten into system design thinking and split your code into modules out of habit, it will be hard to go back to putting all code into a single app monolith.

You may then notice how decoupling things you create tend to make them more general, which may lead you to realize how your things can help others. Time to go open-source.

Making something open-source often brings additional benefits to the table, for instance:

* **Better code** - If you decide to go open-source, you probably also tend to put more time into making the more readable, better documented, etc.
* **Planning** - when your code starts being used by others, you can't just change what you want, how you want, whenever you want. You'll have to plan ahead.
* **Versioning** - when your code starts being used by others, you'll have to start thinking in versions, consider semantic versioning, etc.
* **Collaboration** - in my opinion, few things beat collaborating with others and learning from the community as you gath around exciting ideas and problems.
* **Sharing** - creating something that ends up helping others is truly amazing.

There are naturally so many more gains involved with open-source, but I think this gives you an idea of why I love working on open-source projects.


## Reasons to NOT go open-source

However much I love doing open-source stuff, it's not for everyone. Some reasons to not go open-source can be:

* **Time** - maintaining open-source takes time, time that you may not have.
* **Work secrets** - make sure to not open-source work-related stuff without approval.
* **Intellectual property** - if you create something valuable, maybe you should protect it?
* **High volatility** - something frequently changing may not suitable to make open-source.

There are overheads involved with open-source, including documentation, dev onboarding, versioning and version planning, release management, deprecations, etc. However, doing this kind of work will evolve your skills and let you work on things that you may not get to work with otherwise. You will learn a lot.

You can also still work in an open-source manner, while keeping the implementation details private or release it as closed-source. For instance, you can still work in an open-source way within your company, to reuse components across teams, increase collaboratione etc.


## Private projects

If you want to keep your code private but still using Swift Packages, GitHub, etc., you can create a private repository with a Swift Package (or your choice of dependency manager), add your code, write your tests etc. but use SSH instead of HTTPS. This gives you very granular control over who can use the library.

Private repositories are also a great way to start practicing your open-source skills. It saves you the documentation and versioning effort for when you have something that you think is ready for realse. When you're ready, just make the repo public for the world to use.


## Closed-source

As I mentioned before, closed-source may be an alternative if you have created something that you want to share with others, while still keeping the implementation secret.

Closed-source gives you an opportunity to monetize your work. For instance, if your app uses a sophisticated piece of software, that software may have a business value in itself. Relasing it with a commercial licence can help you monetize that software.

If this sounds interesting, I have written a [separate article](https://danielsaidi.com/blog/2021/02/15/distributing-closed-source-frameworks-with-spm) about how to work with closed-source software with the Swift Package Manager.


## Good practices

If you decide to go open-source, make sure to keep these things in mind:

* **System design** - what architecture first your project?
* **Access control** - what should be public and what should be private.
* **Only expose the essentials** - public things are harder to change later.
* **Design with abstractions** - design and expose the *what*, not the *how*.
* **Loose coupling** - avoid relying on concrete types to make your code flexible.
* **Automate** - automate linting, versioning, release management etc. to great extent.
* **Test, test, test** - a nice foundation helps you verify that things don't break over time.

With good unit tests in place, you easier replicate bugs, fix them and be confident that the fix didn't break anything. Automation will save you a lot of time, reduce the risk of human error, etc. All in all, these good practices are good practices everywhere, not just in open-source projects.


## License

Before releasing your open-source project, it's very important to pick a fitting license, like MIT, GPL etc. In fact, code without an explicit license is NOT open-source and by default copyright protected, with all right given to the author of the code.


## Conclusion

Going open-source will hopefully be a fun and rewarding experience. If your work starts to gain traction, you may even find yourself having a small community of amazing people that contribute amazing things.

However, open-source can turn into a second job. Throw in people who expect you to fix bugs and new features into the mix and things may become too much. Keeping the work-life balance can be challenging.

I try to set clear expectations on myself. Why am I doing this, what are my motivations and ambitions and how much time & energy am I willing to put in?

For instance, if a new open-source project doesn't have a demo app because I don't think it makes sense, or I wait for it to gain traction, I mention that in the readme. If I receive an issue or a PR that I am not able to handle atm, I try to reply and explain.

Another exciting thing with working on open-source is how it can start evolving in ways you could not forsee or plan. For instance, I started a project with the goal to explore keyboard extensions for iOS. Over time, this has grown to a commercial project where I have several amazing collaborators and clients, which made it possible for me to start my own company. 

Open-source can truly change your life.