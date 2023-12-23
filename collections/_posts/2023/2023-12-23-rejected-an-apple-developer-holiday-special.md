---
title:  Rejected - An Apple Developer Holiday Special
date:   2023-12-22 06:00:00 +0000
tags:   swiftui macos ios

image:  /assets/blog/231223/header.jpg
assets: /assets/blog/231223/

toot:   https://mastodon.social/@danielsaidi/111628377914513976
tweet:  https://x.com/danielsaidi/status/1738449452921229663?s=20
---

This is a short Christmas story on the frequent frustrations of being a developer on the Apple stack. But perhaps this time they're actually correct? Happy holidays everyone!

![Blog header image]({{page.assets}}header.jpg)

I've spent some time building a new, very basic app. The goal has been to use many new SwiftUI features to make an accessible and easy to use app, that solves a single problem well. Say hi to Emoji Picker.

![EmojiPicker on macOS]({{page.assets}}emojipicker-macos.jpg)

I started looking into emojis as part of building the [KeyboardKit](https://keyboardkit.com) emoji keyboard. Over time, all emoji features (categories, skin tones, etc.) grew quite capable, which recently led me to extract them into a separate SDK - [EmojiKit](https://kankoda.com/emojikit).

When building SDKs, I want to eat my own dog food, so I started playing around with an app that uses the SDK, to ensure that all features work and that the SDK make sense using, etc. Hence, Emoji Picker.

Emoji Picker aims to solve a single problem, to take the macOS emoji picker and put it in more places. 

![The native macOS emoji picker]({{page.assets}}emojipicker-native.jpg){:width="400px"}

Emoji Picker lets you pick emojis in a simple, multi-platform app on iOS and macOS. It also lets you put an emoji picker in the macOS menu bar, and even pick the most recent ones from interactive widgets.

![EmojiPicker widgets]({{page.assets}}emojipicker-widgets.jpg)

I've used (and hacked) #SwiftUI to implement features like quickly search by just typing, to handle key presses to move focus around. I also used the latest scroll APIs to implement continuous category scroll, but had to roll it back as they crash on iOS devices.

The iOS and iPadOS app is basic, but works great with a keyboard, using modern SwiftUI APIs. I've loved using SwiftUI to build a multi-platform app that uses the same code base on all platforms.

![EmojiPicker on iPad]({{page.assets}}emojipicker-ios.jpg)

Regarding platform-specifics, I've loved learning more about the macOS experience, and learned a great deal on how to bind a keyboard shortcut to the menu bar, how to make the menu bar launch at startup, etc. All with settings-panel options for the user.

All in all, there are many, many details to this very basic app. To avoid overworking it (knowing Apple's fickle behavior), I ended development on the 1.0 yesterday, to get it out to beta testers. Almost there.

Yesterday, Emoji Picker was approved for beta testing on iOS. It was yet to be approved by macOS, but I assumed that since it passed the rigorous iOS review process, macOS should be a walk in the park. 

So I posted on Twitter & Mastodon to look for beta testers.

![Animated gif](https://media.tenor.com/TZiOh8PEPAwAAAAN/i-was-too-naive-and-innocent-gautam-gulati.png)

To my delight, many reached and wanted to test this yet to be revealed app of mine. I was still waiting for macOS to be approved, so I told them I would get back once they could test it on both platforms.

And guess what? Emoji Picker was rejected on macOS, just a few hours later! The reason? (drum roll) "Guideline 5.2.5 - Legal - Intellectual Property". Here, Apple said that the app "mimic the Apple emoji catalog from iOS interface or behavior."

Apple are not wrong. Not at all. Emoji Picker very much mimics the native emoji picker. It's very much the point. I just wanted to put that native experience in more places and give people more options.

I've therefore decided to push out the iOS beta, even if people will only be able to test it on iOS. I hope this lets us iron out some details and squash some bugs while I talk to Apple on how to solve this issue.

If you have any experience in these guidelines, I'd love to hear about your experience and how you (if you) managed to solve it. Happy holidays!