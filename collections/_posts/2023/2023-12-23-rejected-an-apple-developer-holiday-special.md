---
title:  Rejected - An Apple Developer Holiday Special
date:   2023-12-22 06:00:00 +0000
tags:   swiftui indie

image:  /assets/blog/2023/231223/title.jpg
assets: /assets/blog/2023/231223/

toot:   https://mastodon.social/@danielsaidi/111628377914513976
tweet:  https://x.com/danielsaidi/status/1738449452921229663?s=20
---

{% include kankoda/data/app.html name="Emoji Picker" %}
This is a short Christmas story on the many frustrations of being a developer in the Apple ecosystem. But this time they may be correct. Happy holidays everyone!

![Blog header image]({{page.image}})

I've spent time on a new app, with a goal to learn many new SwiftUI features and make an accessible and easy to use app, that solves a single problem. Say hello to [{{app.name}}]({{app.url}}).

![EmojiPicker on macOS]({{page.assets}}emojipicker-macos.jpg)

I started looking into emojis when building the [KeyboardKit](https://keyboardkit.com) emoji keyboard. Over time, the emoji features grew quite capable, which led me to create a separate SDK - [EmojiKit](https://kankoda.com/emojikit).

When building SDKs, I want to eat my own dog food, so I started building an app that uses the SDK, to ensure that it works and that the design makes sense.

Emoji Picker aims to solve a single problem. To add the native emoji picker to more places. 

![The native macOS emoji picker]({{page.assets}}emojipicker-native.jpg){:width="400px"}

Emoji Picker lets you pick emojis in a simple, multi-platform app on iOS and macOS. It also lets you pick emojis from the macOS menu bar and from interactive widgets.

![EmojiPicker widgets]({{page.assets}}emojipicker-widgets.jpg)

I've hacked SwiftUI to implement quick search, to handle key presses, to move focus with the keyboard, etc. I also used the latest scroll APIs to implement category scroll.

The iOS and iPadOS app is basic, but works great with a keyboard, using modern SwiftUI to build a multi-platform app that uses the same code base on all platforms.

![EmojiPicker on iPad]({{page.assets}}emojipicker-ios.jpg)

During the project, I've also learned how to bind a keyboard shortcut to the menu bar, how to make the menu bar launch at startup, etc.

All in all, there are many tiny details to this very basic app. To avoid overworking it (fearing rejection), I ended development on the 1.0 yesterday, to get it out to beta testers.

Emoji Picker was approved for beta testing on macOS. It was yet to be approved for iOS, but I assumed that since it passed the macOS review, iOS should be a walk in the park. 

So I posted on Twitter & Mastodon to start looking for beta testers for this new, little app.

![Animated gif](https://media.tenor.com/TZiOh8PEPAwAAAAN/i-was-too-naive-and-innocent-gautam-gulati.png)

To my delight, many reached and wanted to test this app. I was still waiting for iOS to be approved, so I told them I would get back once it was approved for both platforms.

Then...Emoji Picker was rejected for iOS just a few hours later! The reason? "Guideline 5.2.5 - Legal - Intellectual Property" because it "mimics the Apple emoji catalog on iOS."

Apple are not wrong. Emoji Picker absolutely mimics the native (macOS) picker. It's very much the point. I wanted to put that experience in more places, to provide more options.

I eventually decided to proceed with the macOS beta, even if people won't be able to test it on all platforms. I hope this lets us iron out some details while I figure this out with Apple.

If you have experienced these guidelines, I'd love to hear about if you managed to solve it. 

Happy holidays!


## Update

Apple eventually approved the app for iOS as well. You can download it from the [macOS and iOS App Store]({{app.appstore}}).