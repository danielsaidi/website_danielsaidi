---
title:  An App Group roller coaster ride
date:   2023-05-17 06:00:00 +0000
tags:   swift swiftui

assets: /assets/blog/23/0517/
image:  /assets/blog/23/0517.jpg
image-show: 0

tweet:  https://twitter.com/danielsaidi/status/1658808366657937409?s=20
toot:   https://mastodon.social/@danielsaidi/110383970932692857
---

I've been struggling with a random bug when using an App Group to sync data between an app and a keyboard extension. The explanation: a combination of human error and Xcode.

{% include kankoda/data/open-source.html name="KeyboardKit" version="0.7.0" %}


## Background

A client of mine is using [KeyboardKit Pro]({{project.pro}}) to add dictation to their keyboard. Since keyboard extensions can't access the microphone, it must open the app and perform dictation there.

The keyboard uses a deep link to open the main app, which starts dictation, writes the text into a shared data container, then returns to the keyboard, which applies the dictated text.

The library has tools that make this operation easier to manage. In this app, it however did not work well. The keyboard did open the main app, but dictation randomly didn't start.

To understand this problem, let's first take a look at how data sharing works, how the app is configured and finally how all these factors were combined into a tricky problem.


## How to share data between an app and its extension

To share data between an app and a keyboard extension, you can use an `App Group` and a `UserDefaults` instance that uses that app group as `suiteName`.

For it to work, the app group must be registered for both targets, and both targets must use the same ID as `suiteName` when setting up the user defaults instance.


## The problem

The first problem was that the app is a `DocumentGroup`-based app. Unlike `WindowGroup`, the `DocumentGroup` at the heart of a document app is not a view. The only view you have is the one you define for your documents, which only is available when you open a document.

Since KeyboardKit uses deep links to start dictation and `SwiftUI` uses the `onOpenURL` view modifier to handle deep links, I was in trouble. I didn't have a view to apply `onOpenURL` to, and didn't have a view to reliably present the dictation screen from. 

To solve this, I added a new shared property that the keyboard could use to tell the app to start dictation. It could now trigger the deep link as usual, but instead of checking the deep link, the app could now check this new flag. I tried it on a test app and it worked great.

Unfortunately, it didn't work well in this app. It did work, but the flag was randomly not set, which meant that dictation would not start. If I restarted the app, the data was there...but not if I kept the app running and continuously checked the shared data container.

I tried adding explicit synchronization, but it didn't fix the random problems. I did also try creating explicit `UserDefaults` instances, with the same randomly failing result.


## The app setup

The app in question is actually two apps - the same code is used to build an App Store facing app and an educational variant that is used by schools. 

To handle this, I have multiple build configurations to build debug and release builds of both apps. I also use custom settings to setup bundle ID, display name etc.

This meant that when adding this feature, I had to use two different app groups, which I define like this:

![App Group setup]({{page.assets}}app-groups.png){:width="600px"}

I also had to add separate deep links for the two apps:

![Deep Link setup]({{page.assets}}deep-links.png){:width="600px"}

Spoiler alert! Turns out I made a mistake in this configuration, which in combination with an Xcode bug caused this problem in a way that was very hard to detect. Let's take a look.


## The Xcode bug

The two app variants have different display names, that are configured in `Build Settings`:

![App name setup]({{page.assets}}app-name.png){:width="600px"}

However, for some reason (and for this app only), Xcode randomly resets this name for **both** apps. This means that even if I run the edu app, it can be displayed as Oribi Writer. 

When this happens, I must reset or revert the change.


## The configuration bug

As I struggled with the dictation problem, I noticed that Xcode had once again changed the display name. As I reset it and resumed hunting the dictation bug, I suddently noticed it.

*The non-edu keyboard randomly opened the edu app, and vice versa!*

Turns out that I had accidentally added both deep links to both build configurations, which means that any of the two deep link would therefore randomly open any of the two apps.

Since app groups are not shared between apps, data written by one keyboard extension is only available to its app. So when the wrong app launched, nothing happened.

All it took to fix this was to set up a single deep link that used an app-specific build config to define the ID and the scheme of the link:

![App name setup]({{page.assets}}deep-link.png){:width="600px"}

With this single deep link, the keyboard will now always open its own main app, even when both apps are installed on the same device.


## Conclusion

This problem had me struggling for a few days, and I'm so relieved to finally understand it.  I hope you enjoyed this roller coaster ride. God knows I didn't :)