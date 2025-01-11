---
title:  Building the KeyboardKit app
date:   2021-09-22 07:00:00 +0100
tags:   apps
icon:   avatar

assets: /assets/blog/21/0922/
---

{% include kankoda/data/open-source name="KeyboardKit" %}In this post, I'll discuss my new [KeyboardKit app]({{project.app}}) for iOS & iPadOS. I'll go through my first vision, the actual outcome, as well as a bunch of findings, problems etc.

![A bunch of devices running KeyboardKit]({{page.assets}}devices.png){:class="plain"}


## Background

Many years ago, an friend asked me to create a custom keyboard with her art. The project was a quick one, and my first experience with developing keyboard extensions.

Although the app was pretty basic, I learned a lot about custom keyboard development, and found the limited native APIs both fascinating and frustrating.

Launching Goth Emoji gave me my first (and only) viral experience, with the app trending for a few days. Little did I know, this app started something that would change my life.

![A device running GothEmoji]({{page.assets}}gothemoji.png){:class="plain"}

After the first release, we decided to launch a second app with things that didn't fit the first app. As I started developing it, I wanted to reuse as much as possible from the first app.

As I started moving reusable code and components out of the app, I decided to share it with others who struggled with keyboard extensions. With that, [KeyboardKit]({{project.url}}) was created.


## KeyboardKit (the library)

KeyboardKit quickly became a fun project. I pushed the first commit in August 2016 without realizing that this project would be with me for many years to come.

![A git log showing the first KeyboardKit commit]({{page.assets}}kk-first-commit.png)

The first releases were oriented around extensions and introduced the action model that is still around. I also created a demo that showed how to create a native-like keyboard.

I found a scalable system design from start, although it has changed a lot over the years. UIKit did however never fit UI needs, with collection views lacking a lot of flexibility.


## SwiftUI

It wasn't until SwiftUI launched in 2019, that the technology I had waited for all these years finally arrived. I added SwiftUI support to the library, but UIKit was still the main technology. 

![The SwiftUI logo]({{page.assets}}swiftui.jpeg)

I also started adding observable objects and injecting observability, to make it easy to listen for changes in SwiftUI-based apps, but it wasn't until early 2021 and KeyboardKit 4.0, that I was confident enough to make SwiftUI a first class citizen.

The upcoming 5.0 release will finally remove all UIKit-specific code and also remove a lot of Frankenstein code that has been added to serve both UI frameworks.


## Freelancing

As KeyboardKit improved, I started getting requests from companies who wanted me to develop their keyboards. This gave me enough confidence to start my own company in '21.

Without KeyboardKit, I would probably still be an employee. It's quite amazing, how a basic black & white app, created in a few evenings, ended up actually changing my life.


## KeyboardKit Pro

As client projects started coming in, I decided to add a commercial plan with pro features, to make it easy for companies to get pro featureswithout having to pay me by the hour.

![The KeyboardKit Pro icon]({{page.assets}}pro.png)

KeyboardKit Pro unlocks an autocomplete engine and adds localized system keyboards for all supported locales. It requires a commercial license to be used.

KeyboardKit Pro uses an on-device license engine that I developed myself, which made it possible for me to enable and validate license keys, tiers and features for apps that used it.

With a few Pro licenses sold, its not yet a smash hit, but I hope to add more features to it once KeyboardKit 5 is out and the heavy UIKit luggage is gone.


## KeyboardKit (the app)

With all these pieces in place, I was finally able to get starting on a [KeyboardKit app]({{project.app}}), which I've wanted to build for so long.

I started building it in August, and have since then explored new SwiftUI features, wrestled with problems and dug too deep a couple of times, but now it's finally done.

I'm happy to have amazing beta testers, who have provided great feedback. Without them, the first version of the app would have been a lot rougher.


## Learnings, findings and pitfalls

The app was pretty easy to build, based on my experience of building many keyboard apps before it. Using KeyboardKit Pro helped me standardize many things.

I however became painfully aware how much truth lies in that the last 1% is more like 99%. Key concepts that I had established to myself proved confusing to testers, and the flexible architecture that powered it all caused the app to be bloated.

I had to simplify the app, in which a keyboard defines functionality, locales, keys, etc. while a theme defines colors, styles, borders, etc. Together with concepts like "keyboard slots", where you can assign a keyboard to a slot, apply themes, override themes, etc. the basic code concept became highly confusing to people who tested the app.

I also had performance issues that caused the app to freeze and go into a memory eating loop. As I wrestled these problems and the app started becoming faster and more usable, I started getting great beta feedback. A lot of work remains in simplifying the app even more, changing some terminology and iterate, iterate, but it's getting there.


## Features

In the first version of the app, users can create custom keyboards & themes, where theme customizations are currently limited to colors, shadows, borders etc. as well as audio and haptic feedback. 

The app currently supports English (US & UK), Danish, Dutch, Finnish, French, German, Italian, Norwegian, Spanish & Swedish. More locales will be added in the future, as they're added to KeyboardKit.

Users can share keyboards and themes, by sharing directly from the app or by generating a unique QR code that anyone can scan. Users can also publish their creations, which will send them for review and a chance to be published in the app. The app contains a couple of examples to get people inspired.


## Conclusion

The KeyboardKit app is the result of many projects coming together. While the first version is a bit rough, I am proud to release it and look forward to improving it.

You can read more about KeyboardKit [here]({{project.url}}), and the app [here]({{project.app}}). Grab the app from the App Store if you want to try it out. I would love to hear what you think.