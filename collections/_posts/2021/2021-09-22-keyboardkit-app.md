---
title:  Building the KeyboardKit app
date:   2021-09-22 07:00:00 +0100
tags:   apps
icon:   avatar

assets: /assets/blog/2021/210922/
---

{% include kankoda/data/open-source.html name="KeyboardKit" %}In this post, I'll discuss the development of my new [KeyboardKit app]({{project.app}}) for iOS and iPadOS. I'll go through my original vision, the actual outcome as well as a bunch of findings, problems etc.

![A bunch of devices running KeyboardKit]({{page.assets}}devices.png){:class="plain"}


## Background

Many years ago, an friend contacted me regarding creating a custom keyboard with her art. The project was a quick one, and my first experience with developing keyboard extensions.

Although the app was pretty basic, I learned a lot about custom keyboard development, and found the limited native APIs both fascinating and frustrating.

The app only took a few nights to build, launched as Goth Emoji and gave me my first (and only) semi-viral launch experience, with the app trending...for a few days.

![A device running GothEmoji]({{page.assets}}gothemoji.png){:class="plain"}

Little did I know that this simple app got me started on something that would actually change my life.

After the first release (and some bug fix versions), we decided to launch a second app with artwork that didn't fit the first app. As I started developing it, I wanted to reuse as much as possible from Goth Emoji.

As I started moving reusable code and components out of the app, I wanted to share my findings with other people who also struggled with keyboard extensions. And like that, [KeyboardKit]({{project.url}}) was born.


## KeyboardKit (the library)

KeyboardKit quickly became a fun project for me to spend time on. I pushed the first commit in August 2016 without realizing that this project would be with me for many years to come.

![A git log showing the first KeyboardKit commit]({{page.assets}}kk-first-commit.png)

The first releases were oriented around extensions and introduced the action model that is still around. I also created a UIKit demo that showed how to create a keyboard that mimics the native iOS keyboard.

I think I found a scalable system design from start, although it has changed a lot over the years. UIKit did however never fit the way I wanted to build the UI, with collection views lacking a lot of flexibility.


## SwiftUI

It wasn't until SwiftUI launched in 2019, that I knew that the technology I had waited for all these years had finally arrived. I soon added SwiftUI support to the library, but UIKit was still the primary technology. 

![The SwiftUI logo]({{page.assets}}swiftui.jpeg)

I also started adding observable objects and injecting observability, to make it easy to listen for changes in SwiftUI-based apps, but it wasn't until early 2021 and KeyboardKit 4.0, that I was confident enough to make SwiftUI a first class citizen, with UIKit still being around for the ones who used it. 

The upcoming 5.0 release will finally remove all UIKit-specific code and also remove a lot of Frankenstein code that has been added to serve both UI frameworks.


## Freelancing

As KeyboardKit improved, I started getting requests from developers and companies who wanted to hire me to develop their keyboard apps. This gave me enough confidence to start my own company in 2021.

Without KeyboardKit, I would probably still be an employee. It's quite amazing, how that basic little black and white app, created over the course of a few evenings, ended up actually changing my life.


## KeyboardKit Pro

As client projects started coming in, I decided to setup a commercial product with pro features, to make it easy for companies to add pro features to their apps, without having to pay me by the hour.

![The KeyboardKit Pro icon]({{page.assets}}pro.png)

KeyboardKit Pro unlocks an autocomplete engine and adds localized system keyboards for the keyboard locales specified in the core library. It requires a commercial license to be used.

KeyboardKit Pro uses an on-device license engine that I developed myself, which made it possible for me to easily enable and validate license keys, tiers and features for apps that used it.

With a few Pro licenses actively being used, its not a smash hit so far, but I hope to add more features to it once KeyboardKit 5 is out and the heavy UIKit luggage is gone.


## KeyboardKit (the app)

With all these pieces in place, I was finally able to get starting on a [KeyboardKit app]({{project.app}}), which I've wanted to build for so long.

I started building it in August, and have spent some evenings on it. I have explored new SwiftUI features, wrestled many beta problems and dug too deep a couple of times, but now it's finally done.

I've been extremely lucky to get amazing support by my beta testers, who have provied me with great feedback. Without them, the first version of the app would have been a lot rougher.


## Learnings, findings and pitfalls

The app was pretty easy to build, based on my experience of building many keyboard apps before this. Also, using KeyboardKit and KeyboardKit Pro helped me standardize many things.

I however became painfully aware how much truth lies in that the last 1% is more like 99%. Key concepts that I had established to myself proved confusing to testers, and the flexible architecture that powered it all caused the app to be bloated.

I had to simplify the app, in which keyboards define functionality, locales, keys, etc. while themes define colors, styles, borders, etc. Together with additional concepts like "keyboard slots", where you can assing keyboards to slots, add themes to keyboards, override keyboard themes with themes on a slot etc. the concept that is actually pretty basic in its code, became highly confusing to people who tested the app.

I decided to rename "keyboard slots" to "selected keyboards" and instead of showing all five from start, I started by showing no slots, then add one by one as the user selects more keyboards. This also helped me not show too many premium features at once (slot 2-5 are premium features).

The performance issues that caused the app to lock go into a memory eating loop were caused by me sending edited themes all the way down the view hierarchy. When editing the theme, renaming it etc. every change caused the entire view hiearchy to redraw, including some performance-heavy previews.

The solution was to make the previews only take what they need, instead of the full theme. This helped SwiftUI understand what had to be re-rendered as the theme changed. The difference was astonishing, with the app going from super slow to pretty damn fast. And I was able to keep my architecture intact.

As the app started becoming more usable, I started getting a lot of amazing beta feedback. A lot of work however remains in simplifying the app even more, changing some terminology and iterate. And iterate.


## App features

As the first version of the app is about to be released, let's sum up what actually made it to production.

Users can create custom keyboards and themes, but the customizations are currently limited to colors, shadows, borders etc. as well as audio and haptic feedback and 11 supported keyboard languages.

The app supports English (US - Default), Danish, Dutch, English (UK), Finnish, French, German, Italian, Norwegian, Spanish and Swedish. More locales will be added as they are added to KeyboardKit Pro.

Users can share keyboards and themes with others who have KeyboardKit, either by sharing directly from the app or generate a unique QR code that anyone can scan.

Users can also publish their creations, which will send them for review and a chance to be published in the app. The app contains a couple of examples to get people inspired.


## Conclusion

The development of the KeyboardKit app is the result of many projects coming together. Although the first version is a bit rough, I am proud to release it, and look forward to improving it.

You can read more about KeyboardKit [here]({{project.url}}), and the app [here]({{project.app}}). Grab the app from the App Store if you want to try it out. I would love to hear what you think.