---
title:  "DevSum 2012: Day 2 Summary"
date:    2012-05-28 21:28:00 +0100
categories: conferences
tags: 	conference devsum
---


The second and last day of DevSum featured some great sessions. Some were really
challenging as well, especially for a parental leave douchebag like myself :)


## Keynote - Aral Balkan - A Happy Grain of Sand

Boy, what a great session!

According to his personal site, Aral is a "designer, developer, author, teacher,
entrepreneur and performer; a Renaissance Geek with a passion for designing user
experiences and a desire to make the world a better place through technology and
oratory".

Should be interesting, right? :)

Aral started his talk with a coordinated introduction. As someone who lives for
creating things that make people happy, he then presented some examples from his
daily life that makes him...well, sad:

* A hotel toilet, where a flush button outside of the natural grouping, makes it
really hard to understand how to flush. **Proximity implies relation** and **out
of sight = out of mind.**

* A hotel toilet (another one), where the flush button and toilet fulfill their
purposes, but where the toilet lid hides the flush button once it opens, making
it hard to see how to flush. **An experience is as strong as its weakest part.**

Various examples of elevator button panels, including the pun Schindler's lift.
He also went into a long description of his washing machine, which together with
the dishwasher most often have horrible user interfaces.

According to Aral, *we do not need more things, we need things that work better*.
He compared the Swedish Arlanda Express ticket machine (which is terrible) with
Oslo's solution, which is beautiful and just consist of a card swipe and a small
digital screen.

**Sometimes the best UI is no UI.**

This session was full of great examples of good and bad design. I will not stack
up on great quotes, but rather advice you to see a presentation with Aral if you
get the chance.



## Scott Allen - Modern Javascript

This was a highly interesting session, where Scott talked about great JavaScript
features and also discussed **scope, constructor functions, prototypes, closures, 
getters/setters, modules** etc. 

The talk was almost entirely code-oriented and thus hard to summarize...but make
sure to watch it if you stumble upon the video.



## Steve Sanderson - Building mobile web applications with Node.js

This talk focused on Node.js and how to get started with it, and on to mastering
it etc. Steve started from (basically) nil and used Node to create various types
of applications and APIs, using extensions like Express to simplify it, handling
static content, templating etc.

Steve improved a mobile web application with responsive design, viewport settings
etc. He then demonstrated how to use the seemingly great Opera Mobile Emulator to
simulate various device types, including limiting the bandwidth of the device and
using cache manifest files to improve caching.

Although a bit overwhelming (I want this session on vid), it was very interesting.
Steve took many of these things that I have been meaning to look at for some time
now and presented them in a way that makes me want to use them now, now, NOW!



## Johan Lindfors - Windows Phone Best Practices – part 1 / 2

This was the first part of two, in which Johan and Michael talked about Windows
Phone development. Johan covered the following areas:

* Design / development
* MVVM
* Testability
* Localization
* Security
* Design

### Design

Regarding design, Metro apps can (mostly) be defined as minimalistic, text-based
and living. So, how can you make your apps behave like the native apps that flow
smoothly and look stunning?

Johan first mentioned the **Windows Phone design grid**, which is 24, 25, 12, 25,
12 ... 25, 24 grid on which the Metro UI is built. Use it and you will find that
your apps will "feel right".

Johan then talked about **design data**, which replaces real data with mock data
during the design phase. With design data, developers and designers can see data
in the UI when developing and designing.

Before animations, Johan recommended the **Windows Phone Commands VS Extension**,
which lets you download and run apps on the simulator. No roms are needed - apps
can be downloaded directly from the marketplace.


### Animations

Johan mentioned three toolkits that simplify working with animations on Windows:

* Silverlight toolkit for WP (resource demanding)
* WP7 contrib (based on adding base classes to your views)
* Metro in motion, which is a series of blog posts.

Johan used MiM in this talk, and it looked great.


### MVVM

Johan compared several frameworks for working with Windows Phone and *MVVM*:

* the built in framework in Visual Studio
* MVVM Light
* MVVM Excalibur
* Prism
* DYI (Do It Yourself - requires more code, but gets you exactly what you need).

Johan then talked about keeping the framework simple, using the singleton pattern
instead of dependency injection, how to handle communication between the view and
the view model etc. He also told us that the **SmartObservableCollection** might
help us out.


### Testability

Unit testing on mobile devices can sometimes be tricky. Johan talked a bit about
how to solve this, including using portable class libraries, and also adviced us
to embed the required tools to make CI work.


### Localization

Johan added some Swedish and English texts in resource files, then showed us how
to translate strings, making the application translate all translatable textual
content as soon as a user selects another language. With localization, a general
advice is to design text boxes 40% larger than they would be with English texts,
in order to support longer texts.


### Security

Johan talked about security, including how you can download .XAP files from the
marketplace, change the extension to .zip, unzip them and get access to whatever
the file contains. Feels like a call for source code obfuscating.


### Summary

A very(!) thorough session, that I cannot help would have been even more awesome
if I had actually gotten around to developing for Windows Phone. Then, a session
like this would have been extremely helpful. Now, it just rocked ;)



## Michael Björn - Windows Phone Best Practices – part 2 / 2

Michael continued the Windows Phone talk and focused on prestanda, communication,
background agents, push notifications and synchronization.


### Prestanda

Michael showed us how the fact that Windows Phone has **two** UI threads - a UI
thread and a compository thread - can be used to achieve non-blocking animations.
He demoed this with different progress bars - the built in one, an external one
and another native one that animates in the very top of the screen.

He also demonstrated moving stuff around and how textures can cause the phone to
redraw small or large parts of the screen, depending on how you manage it. Some
other general advices were that:

* If you have a long-running operation, use a Thread.Sleep(1) in the operation
to avoid blocking the compository thread.

* In order to avoid memory leaks when working with textures, disable timers that
run in views.

* In order to reduce startup time (long startup times can get your app refused),
consider how you load images, coordinates etc.


### Communication

When it comes to picking between the `WebClient` and the `HttpWebRequest` classes,
Michael strongly advocated the latter. Since the WebClient class runs on the UI
thread, using it may come back to haunt you later on. HWR takes a bit more setup,
but is worth it.

Also, instead of having many calls with little data, it is better to bundle the
calls into one. Otherwise, the phone will open and close the connection for each
call, which makes the antenna toggle a lot and drain battery life in no time.



### Background agents

Since WP lacks the possibility to create multi-threading apps, you must resort to
background agents. However, the two types of available agents (periodic tasks and
resource intensive tasks) have great limitations and are non-reliable. You should
never resort to background processes and use other means to achieve critical tasks.


### Push notifications

There are not that many WP apps out there today that use push notifications, but
it is quite easy to setup. Since push notifications can affect your live tiles,
using them can make your app feel a lot more alive, even when it's not being used.



### Synchronization

Today, the WP framework only has support for one synchronization framework - the
`Sync Framework`. Michael did not talk much about this, but mentioned, that when
stuff is synced for your app, the content will be placed in the isolated storage.



## Morten Nielsen - From Continuous Integration to Continuous delivery

This talk focused heavily on the specific challenges for Morten's company, where
the release processes caused updating customer installations to take a day or so.

Per customer.

With CI and CD, they managed to drastically reduce this time...which is the kind
of effects you tend to get by automating tasks :) 

To sum it up, using Hudson, Morten and his team now have builds that are:

* Fast to create and apply
* Deterministic
* Logged / audited
* Unsupervised

My advice to Morten is to focus less on very company (especially team) specific
examples. I expected a CI/CD session and got a case study. Nice, but not what I
was after. 



