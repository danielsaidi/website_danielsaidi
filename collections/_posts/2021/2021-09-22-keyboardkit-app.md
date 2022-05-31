---
title:  Building the KeyboardKit app
date:   2021-09-22 07:00:00 +0100
tags:   article general
icon:   avatar
assets: /assets/blog/2021/2021-09-22/

goth:   https://gothemoji.danielsaidi.com
metal:  https://metalemoji.danielsaidi.com
keyboardkit:    https://getkeyboardkit.com
---


In this post, I'll discuss the development of my new [KeyboardKit](https://app.getkeyboardkit.com) app for iOS and iPadOS. I'll go through my original vision, the actual outcome as well as a bunch of findings, problems etc.


![A bunch of devices running KeyboardKit]({{page.assets}}devices.png)


## Background

Many years ago, an artist friend contacted me regarding wanting to create a custom keyboard with her own artwork. The project was a quick one, and my first experience with developing keyboard extensions.

Although the app was basic, I learned a lot, and found the pretty restricted keyboard api's that Apple provides you with both fascinating and frustrating. The basic text document proxy api lacks a lot of functionality and Apple's rules regarding keyboard extensions (main app must have functionality, the keyboard must be functional without full access etc.) were tricky to work around.

The app only took a few nights to build, launched as [Goth Emoji]({{page.goth}}) and gave me my first (and only) semi-viral launch experience, with the app trending...for a few days.

![A device running GothEmoji]({{page.assets}}gothemoji.png)

Little did I know that this simple app got me started on something that would actually change my life.

After the first release (and some quick subsequent bug fix versions), we decided to launch a second app with artwork that didn't fit the goth context. As I started developing [Metal Emoji]({{page.metal}}), I did so with the mindset of reusing as much as possible from Goth Emoji.

As I started moving reusable code and components out of the app to a shared container, I realized that I should probably share my findings with other people who also struggled with keyboard extensions. 

And like that, [KeyboardKit]({{page.keyboardkit}}) was born.


## KeyboardKit (the library)

KeyboardKit quickly became a fun side project for me to spend time on. I pushed the first commit in August 2016 without realizing that this project would be with me for many years to come.

![A git log showing the first KeyboardKit commit]({{page.assets}}kk-first-commit.png)

The first releases were oriented around convenience extensions, laying out the foundation of the action model that is still around and creating a UIKit-based demo that demonstrated how to create a keyboard that mimics the native iOS system keyboards.

Model-wise, I think I found a scalable design from start, although it has transformed over the years. UI-wise, however, UIKit never fit the way I envisioned building this kind of software, with collection views and stack views proving a real challenge. They demo keyboard was clunky and far from pixel perfect, and I mostly used the library to create keyboards like Goth and Metal emoji.


## SwiftUI

It wasn't until SwiftUI launched in 2019, that I know the technology I had waited for had finally arrived. I quickly started adding SwiftUI support to the library, although UIKit was still the primary technology. I started adding observable objects and injecting observability here and there, to make it easy to listen for changes in SwiftUI-based apps.

![The SwiftUI logo]({{page.assets}}swiftui.jpeg)

It wasn't until early 2021 and the release of KeyboardKit 4.0, that I was confident enough to promote SwiftUI to a first class citizen, with UIKit still being around for the ones who used it. The upcoming 5.0 will remove all UIKit-specific components and clean up a lot of Frankenstein code that were added to serve both UI frameworks.


## Freelancing

As KeyboardKit improved, I started getting requests from developers and companies who wanted to hire me to develop their keyboard apps. This eventually led to me having enough confidence to start my own company in 2021, going from a position as Mobile Architecture Lead and iOS Tech Lead at BookBeat, to working on many exiting keyboard-related projects.

This is were the "changed my life" came in. Without KeyboardKit, I would probably still be working as an employee. KeyboardKit gave me a lot of contacts and confidence to start my own things. This small little black and white app ended up actually changing my life. Isn't it amazing?


## KeyboardKit Pro

As client projects started coming in, I decided to offer license-based pro features. Having such a product would make it very for companies to add pro features to their apps, without having to pay me by the hour. Instead, they could sign up for a license and get pro features in a closed-source binary package.

![The KeyboardKit Pro icon]({{page.assets}}pro.png)

For instance, KeyboardKit Pro contains an autocomplete engine and adds localized system keyboards for the keyboard locales specified in the core library. By just adding KeyboardKit Pro, developers could easily localize their keyboards in 10+ languages and add autocomplete to the mix.

KeyboardKit Pro also contained an on-device license service that I developed myself, which made it possible for me to easily enable and validate license keys, tiers and features for apps that used it.

With a few Pro licenses actively being used, its not a smash hit so far, but I hope to add more features to it once KeyboardKit 5 is out and the heavy UIKit luggage is gone.


## KeyboardKit (the app)

With all these pieces in place, I was able to get starting on the KeyboardKit app, which I've wanted to do for so long. It just hasn't been feasible until now, when all my library efforts, pro features and SwiftUI can be combined into something great.

I started developing this app on my spare time in August, and have spent some evenings and nights on it. I have happily explored the new features in SwiftUI 3, wrestled a bunch of beta problems and dug myself into a deep well a couple of times, but now the app is finally out. ...or at least, a first version of it.

I've been extremely lucky to get amazing support by my closed beta testers, who have provied me with insights and experties. Without them, the first version of the app would have been a lot rougher.

Bardi was kind enough to "unbox" the app while I watched on video link. That amazing one hour session made me redesign many concepts that just worked in my own head. Dev has supported the KeyboardKit project for many years. Yagiz, Emin, Khoa, Paul, Thomas, Giovanni, Drew and many others provided me with outstanding feedback based on their expertise, and finally, Marina has sent me sooo much amazing feedback, that I have many future updates planned before even pressing the 1.0 release button.

From the bottom of my heart, thank you all so, so much!


## Learnings, findings and pitfalls

Developing the app has been pretty smooth sailing and was based on my experience of building many keyboard apps before this. Also, using KeyboardKit and KeyboardKit Pro helped me standardize much.

As the development started to reach a first version, I however became painfully aware that the last 1% was actually 99%. Key concepts that I had established to myself proved confusing to testers, the grand app architecture that allowed me to easily add new features proved to have massive performance issues and my mental model of what the app will eventaully be, caused the first release to be bloated.

First, I had to simplify the app. The app is based on keyboards and themes, where keyboards define the functionality, locale, keys, settings etc. of the keyboard, and themes define things like colors, borders, shadows etc. Although I have big plans for the future versions of the app, the first version is limited to applying themes and enabling/disabling a few options.

So, as I established additional concepts like "keyboard slots" and let you assing keyboards to slots, add themes to keyboards, override keyboard themes with themes on the slot etc. the concept that is actually pretty basic in its code, became highly confusing to people who tested the app.

Instead of "keyboard slots", I decided to call it "selected keyboards" and instead of showing all five slots from the get go, I started by showing no slots, and added one by one as the user created and selected keyboards. This also helped me reducing the cheap look of smacking four premium buttons (slot 2-5 are premium features) in the face to new users who opened the app for the first time. Instead, the premium slots show up as you start using the app.

The severe performance issues that caused the app to lock and SwiftUI to go into a memory consuming loop were not caused by my architecture, as I first feared, but rather that I sent a theme all the way from the screen down the view hierarchy. When editing the theme, renaming it etc. every little change to the theme caused the entire view hiearchy to redraw, including some pretty heavy previews.

The solution to these performance issues were to redesign the previews to take only the model they needed to render correctly. This helped SwiftUI understand which views that had to be re-rendered as the theme changed. The difference was astonishing, with the app going from super slow to pretty damn fast. And I was able to keep my flexible architecture intact.

As the app started becoming usable, I started getting a lot of amazing beta tester feedback. Although things now works, a lot of work remains in simplifying the app even more, changing some terminology and iterate, iterate, iterate until it's as good as I want it to be.


## KeyboardKit 1.0

As KeyboardKit 1.0 is now about to be released, let's just sum up what actually made it to production.

I wanted users to be able to create custom keyboards and themes, which is currently possible. However, the scope of these customizations is currently limited to colors, shadows, borders etc. as well as audio and haptic feedback and 11 supported keyboard languages.

KeyboardKit currently supports the following keyboard languages: English (US - Default), Danish, Dutch, English (UK), Finnish, French, German, Italian, Norwegian, Spanish and Swedish. Adding more locales will be possible as they are added to KeyboardKit Pro.

Users can share keyboards and themes with others who have KeyboardKit, either by sharing directly from the app or generate a unique QR code that anyone can scan.

Users can also publish their creations, which will send them for review and a chance to be published in the app. The app contains a couple of examples to get people inspired, and I hope that this will in time turn into a fun part of the app.


## Conclusion

The development of the KeyboardKit app is the result of many projects coming together. Although the first version is a bit rough, I am proud to release it, and look forward to improving it.

You can read more about the Keyboard Kit library [here](https://getkeyboardkit.com), the Keyboard Kit app [here](https://app.getkeyboardkit.com). Grab the app from the App Store if you want to try it out. I would love to hear what you think.