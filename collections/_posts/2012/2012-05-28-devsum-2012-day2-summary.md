---
title: DevSum 2012 - Day 2 Summary
date:  2012-05-28 21:28:00 +0100
categories: conferences
tags:  conferencess
icon:  avatar
---

This is a short summary of the second day of the DevSum 2012 conference in  Stockholm, Sweden.


## Keynote - Aral Balkan - A Happy Grain of Sand

Boy, what a great session!

Aral started his talk with a coordinated introduction. As he lives to create things that make people happy, he presented some examples from his daily life that make him sad:

* A hotel toilet, where a flush button outside of the natural grouping, makes it hard to understand how to flush. **Proximity implies relation** and **out of sight  = out of mind**.

* Another hotel toilet, where the flush button and toilet fulfill their purposes, but the lid hides the flush button once it opens. **An experience is as strong as its weakest part.**

Various examples of elevator button panels followed, including the pun Schindler's lift. He also touched upon washing machines and dishwashers, which often have horrible UIs.

Aral thinks *we do not need more things, we need things that work better* and compared the Swedish Arlanda Express ticket machine (which is terrible) with Oslo's much better one - a beautiful panel that just consist of a card swipe and a small digital screen.

**Sometimes the best UI is no UI.**

This session was full of great examples of good and bad design. I will not stack up on great quotes, but rather advice you to see a presentation with Aral if you get the chance.



## Scott Allen - Modern Javascript

This was a highly interesting talk, where Scott discussed JavaScript features like **scope, constructor functions, prototypes, closures,  getters/setters, modules** etc. 

The talk was almost entirely code-oriented and thus hard to summarize, but make sure to watch it if you happen to find the video.



## Steve Sanderson - Building mobile web applications with Node.js

This talk focused on Node.js and how to get started, then on to mastering it. Steve started from nil and used Node to create various types of applications and APIs, using extensions like Express to simplify it, handling static content, templating etc.

Steve improved a mobile web app with responsive design, viewport settings etc. and then showed how to use the Opera Mobile Emulator to simulate various device types, including limiting the bandwidth of the device and using cache manifest files to improve caching.

Although overwhelming, it was very interesting. Steve took many things that I have been meaning to look at and made me want to do it NOW!



## Johan Lindfors - Windows Phone Best Practices – part 1/2

This was the first of two parts, where Johan and Michael talked about Windows Phone. 

Johan covered the following areas:

* Design / development
* MVVM
* Testability
* Localization
* Security
* Design

### Design

Metro apps can (mostly) be defined as minimalistic, text-based and living. So, how can you make your apps behave like the native apps that flow smoothly and look stunning?

Johan first mentioned the **Windows Phone design grid**, which is 24, 25, 12, 25, 12 ... 25, 24 grid on which the Metro UI is built. Use it and you will find that your apps will "feel right".

He then talked about **design data**, which can replace real data with mock data during the design phase. It lets developers & designers use data when developing and designing.

Before animations, he recommended the **Windows Phone Commands VS Extension**, which lets your simulator download and run apps from the marketplace. No roms needed.

### Animations

Johan mentioned three toolkits that simplify working with animations on Windows:

* Silverlight toolkit for WP (resource demanding)
* WP7 contrib (based on adding base classes to your views)
* Metro in motion, which is a series of blog posts.

Johan used MiM in this talk, and it looked great.

### MVVM

Johan compared several frameworks for working with Windows Phone and *MVVM*:

* The built in framework in Visual Studio
* MVVM Light
* MVVM Excalibur
* Prism
* DYI (Do It Yourself - requires more code, but gets you exactly what you need).

Johan suggested keeping it simple and use the singleton pattern instead of dependency injection. He also talked about how to handle communication between the view and the view model etc. and how the **SmartObservableCollection** can help us out.

### Testability

Unit testing on mobile devices can be tricky. Johan talked about how to solve this using portable class libraries, and also adviced us to embed the required tools to make CI work.

### Localization

Johan added Swedish and English texts in resource files, then showed how to translate the app. With localization, a general advice is to design text boxes 40% larger than they would be with English texts, in order to support longer texts.

### Security

Johan talked about security and how to download .XAP files from the marketplace, change the extension to .zip, unzip them and get access to whatever the file contains. Feels like a call for source code obfuscation.

### Summary

This was a very(!) thorough session that I cannot recommend enough. It would have been even better if I did some Windows Phone development. :)


## Michael Björn - Windows Phone Best Practices – part 2/2

Michael continued the Windows Phone talk with a focus on performance, communication, background agents, push notifications and synchronization.

### Performance

Michael showed us how Windows Phone's **two** UI threads - a UI thread and a compository one - can be used for non-blocking animations. He showed it with different progress bars - a built in one, an external one and a native one that animates in from the top of the screen.

He also showed how using textures can cause the phone to redraw small or large parts of the screen, depending on how you manage it. Some other general advices were that:

* For long-running operation, use `Thread.Sleep(1)` to avoid blocking the comp. thread.
* To avoid memory leaks when working with textures, disable timers that run in views.
* To reduce startup time, consider how you load images, coordinates etc.

### Communication

When picking between `WebClient` and `HttpWebRequest`, Michael strongly advocated the latter. Since `WebClient` runs on the UI thread, using it may come back to haunt you later on. `HttpWebRequest` takes a bit more setup, but is worth it.

Also, instead of having many calls with little data, it is better to bundle the calls into one. Otherwise, the phone will open and close the connection for each call, which makes the antenna toggle a lot and drain the battery.

### Background agents

Since Windows Phone doesn't support multi-thread apps, you must resort to background agents. However, the two available agent types (periodic tasks & resource intensive ones) are limited and non-reliable. Never rely on background processes for important tasks.

### Push notifications

Few WP apps use push notifications, but it is quite easy to setup. Since push notifications can affect your live tiles, using them can make your app feel a lot more alive.

### Synchronization

WP only has one synchronization framework - the `Sync Framework`. Michael didn't talk that much about it, except that when stuff is synced, content is placed in the isolated storage.


## Morten Nielsen - From Continuous Integration to Continuous Delivery

This talk focused heavily on specific challenges for Morten's company, where the release processes for every individual customer took a day or so.

With Continuous Integration to Continuous Delivery, they managed to drastically reduce the release time (which is the expected effect of automating tasks :) 

Using Hudson as the main tool, Morten and his team now have builds that are:

* Fast to create and apply
* Deterministic
* Logged / audited
* Unsupervised

I prefer talks that focus less on very company and team specific examples. I expected a CI/CD session and got a case study. Not what I was after. 