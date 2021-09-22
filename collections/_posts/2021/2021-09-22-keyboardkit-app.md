---
title:  Building the KeyboardKit app
date:   2021-09-22 07:00:00 +0100
tags:   swiftui
assets: /assets/blog/2021/2021-09-22/
---


In this post, I'll discuss the development of my new [KeyboardKit](https://app.getkeyboardkit.com) app for iOS and iPadOS. I'll go through my original vision, the actual outcome as well as a bunch of findings, problems etc.


![A bunch of devices running KeyboardKit]({{page.assets}}devices.png)


## Background

Many years ago, an artist friend contacted me regarding wanting to create a custom keyboard with her own Goth artwork. The project was a quick one, and my first experience with developing custom keyboard extensions.

Although the app was basic, I learned a lot, and found the pretty restricted context both fascinating and frustrating. The basic text document proxy api lacks a lot of functionality and Apple's rules regarding keyboard extensions (main app must have functionality, the keyboard must be functional without full access etc.) were tricky to work around.

The app only took a few nights to build, launched as [Goth Emoji](https://gothemoji.danielsaidi.com) and gave me my first (and only) semi-viral launch experience, with the app trending for a few days. It was all my friends repute, but I was happy nevertheless.

![A device running GothEmoji]({{page.assets}}gothemoji.png)

Little did I know that this simple app got me started on something that would actually change my life.

After the first release (and some quick subsequent bug fix versions), we decided to launch a second app with artwork that didn't fit the goth context of the first app. As I started developing the "sequel", [Metal Emoji](https://metalemoji.danielsaidi.com), I did so with the mindset of reusing as much as possible.

As I started moving reusable code and components out of the app to a shared container, I realized that I should probably share my findings with other people who also struggled with keyboard extensions.

And like that, the KeyboardKit library was born.


## KeyboardKit (the library)

KeyboardKit quickly became a fun side project for me to spend time on. I pushed the first commit in August 2016 without realizing that this project would be with me for many years to come.

![A git log showing the first KeyboardKit commit]({{page.assets}}kk-first-commit.png)

The first releases were oriented around convenience extensions, laying out the foundation of the action model that is still around and creating a UIKit-based demo that demonstrated how to create a keyboard that mimics the system keyboards...kind of.

Model-wise, I think I found a pretty scalable design from start, although it has transformed over the years. UI-wise, however, UIKit never fit the way I envisioned building this kind of software, with collection views and stack views proving a real challenge. They demo keyboard was clunky and far from pixel perfect, and I mostly used the library to create keyboards like Goth and Metal emoji.


## SwiftUI

It wasn't until SwiftUI launched in 2019, that I know the technology I had waited for had finally arrived. I quickly got started on adding SwiftUI support to the library, although UIKit was still the primary technology. I started adding observable objects and injecting observability here and there, to make it easy to listen for changes in SwiftUI-based apps.

![The SwiftUI logo]({{page.assets}}swiftui.jpeg)

It wasn't until early 2021 and the release of KeyboardKit 4.0, that I finally was confident enough to promote SwiftUI to a first class citizen, with UIKit still being around for the ones who used it. The upcoming 5.0 will remove all UIKit-specific components and clean up a lot of Frankenstein code that were added to serve both UI frameworks.


## Freelancing

As KeyboardKit slowly improved, I started getting more and more requests from developers and companies who wanted to hire me to develop their keyboard apps. This eventually led to me having enough confidence to start my own company in 2021, going from a position as Mobile Architecture Lead and iOS Tech Lead at BookBeat, to working on many exiting keyboard-related projects.

This is were the "changed my life" came in. Without KeyboardKit, I would probably still be working as an employee. KeyboardKit gave me a lot of online contacts and confidence to start my own things. This small little black and white app ended up actually changing my life. Isn't it amazing?


## KeyboardKit Pro

As client projects started coming in, I realized that an easy way for me to provide customers with easily configurable pro features, were to extend the core library with a license-based addition. Having such a product would make it very easy for people to add pro features to their apps, without them having to pay me by the hour. Instead, they could just sign up for a tiered license and get pro features in a closed-source binary package.

![The KeyboardKit Pro icon]({{page.assets}}pro.png)

The first few releases of KeyboardKit Pro contained a real (but non-predictable) autocomplete engine and extended support for the keyboard locales specified in the core library. By just adding KeyboardKit Pro, developers could easily localize their keyboards in 10+ languages and add autocomplete to the mix.

KeyboardKit Pro also contained an on-device license service that I developed myself, which made it possible for me to easily enable and validate license keys, tiers and features for apps that used it.

With a few Pro licenses actively being used, its not a smash hit so far, but I hope to add more features to it once KeyboardKit 5 is out and the heavy UIKit luggage is gone.


## KeyboardKit (the app)

Finally, with all these pieces in place, I was able to get starting on the KeyboardKit app, which I have been meaning to build for so long. It just hasn't been feasible to do so until now, when all my library efforts, pro features and SwiftUI can be combined into something great.

I started developing this app on my spare time in August, and have spent quite a few evenings and nights on it. I have happily explored the new features in SwiftUI 3, wrestled a bunch of beta problems and dug myself into a deep well a couple of times, but now the app is finally out. ...or at least, a first version of it.

I have been extremely lucky in getting so much amazing support by my closed beta testers, who have provied me with their insights and experties. Without them, the first version of the app would have been a lot rougher.

Bardi was kind enough to "unbox" the app while I was watching on video link. That amazing one hour session made me redesign som many concepts that just worked in my own head. Dev has always been supportive and supported the KeyboardKit project for many years. Yagiz, Emin, Khoa, Paul, Thomas, Giovanni, Drew and many others have provided me with outstanding, sincere feedback based on their expertise, and finally, Marina has sent me sooo much amazing feedback, that I have many future updates planned before even pressing the 1.0 release button.

From the bottom of my heart, thank you all so, so much!


## Learnings, findings and pitfalls

The development of the app has been pretty smooth sailing, and was based on my experience from building several keyboard apps before this. Also, using the KeyboardKit and KeyboardKit Pro libraries helped me standardize much.

As the development started to reach a first version, I however became painfully aware that the last 1% was actually 99%. Key concepts that I had established to myself proved confusing to testers, the grand app architecture that allowed me to easily add new features proved to have massive performance issues and my mental model of what the app will eventaully be, caused the first release to be bloated with features that currently doesn't make sense, given how basic the app is.

First of all, I had to simplify the app. The app is based on keyboards and themes, where keyboards define things like the functionality, behavior, keys, feedback etc. of the keyboard, and themes define things like colors, borders, shadows etc. Although I have big plans for the future versions of the app, the first version is limited to applying themes and enabling/disabling a few options.

So, as I established additional concepts like "keyboard slots" and let you assing keyboards to slots, add themes to keyboards, override keyboard themes with themes on the slot etc. the concept that is actually pretty basic in its code, became highly confusing.

To fix this, I had to change things quite a bit. Instead of "keyboard slots", I called the same thing "selected keyboards" and instead of showing the five slots from the get go, I started by showing no slots, and added one by one as the user created and selected keyboards. This also helped me reducing the cheap look of smacking four premium buttons (slot 2-5 are premium features) in the face to new users who opened the app for the first time. Instead, the premium slots show up as you start using the app.

The severe performance issues that caused the app to lock and SwiftUI to go into a memory consuming loop were not caused by my architecture, as I first feared, but rather that I sent a theme all the way from the screen down the view hierarchy. When editing the theme, renaming it etc. every little change to the theme caused the entire view hiearchy to redraw, including some pretty heavy previews.

The solution to these performance issues were to redesign the previews to take only the model they needed to render correctly. This helped SwiftUI to understand which views that had to be re-rendered as the theme changed. The difference was astonishing, with the app going from dead (!) slow to pretty damn fast. And I was able to keep my flexible architecture intact. Hooray!

As the app finally became usable, I started getting a lot of amazing beta tester feedback. Although things now works, a lot of work remains in simplifying the app even more, changing some terminology and iterate, iterate, iterate until it's as good as I want it to be.


## KeyboardKit 1.0

As KeyboardKit 1.0 is now about to be released, let's just sum up what actually made it out into prod.

I wanted users to be able to create custom keyboards and themes, which is currently possible. However, the scope of these customizations is currently limited to colors, shadows, borders etc. as well as audio and haptic feedback and 11 supported keyboard languages.

KeyboardKit currently supports the following keyboard languages: English (US - Default), Danish, Dutch, English (UK), Finnish, French, German, Italian, Norwegian, Spanish and Swedish. Adding more languages will be a main focus during Q4.

Users can share keyboards and themes with others who have KeyboardKit, either by sharing directly from the app or generate a unique QR code that anyone can scan.

However, as I want users to own their data completely, and currently don't have any iCloud file support, the data is actually encoded into the QR code as of now. This requires me to limit the scope of the themes and keyboards, until I find a better way of achieving global, public sharing.

Users can also publish their creations, which will send them for review and a chance to be published in the app. The app contains a couple of examples to get people inspired, and I hope that this will in time turn into a fun part of the app.


## Conclusion

The development of the KeyboardKit app is the result of many projects coming together. Although the first version is a bit rough, I am very proud to release it, and am looking forward to improving it over time.

I can’t wait to see what people will create with KeyboardKit, and hope that all of you who do keep providing me with sincere feedback, so that we together can make this into something really good.

You can read more about the Keyboard Kit library [here](https://getkeyboardkit.com), the Keyboard Kit app [here](https://app.getkeyboardkit.com) and grab the app from the app store if you want to try it out. I would love to hear what you think.

Love

Daniel Saidi