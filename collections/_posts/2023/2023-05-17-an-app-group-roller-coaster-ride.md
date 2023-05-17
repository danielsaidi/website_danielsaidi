---
title:  An App Group roller coaster ride
date:   2023-05-17 06:00:00 +0000
tags:   swift swiftui

icon:   swift
assets: /assets/blog/2023/2023-05-17/

tweet:  https://twitter.com/danielsaidi/status/1658808366657937409?s=20
toot:   https://mastodon.social/@danielsaidi/110383970932692857

keyboardkit:    https://keyboardkit.com
oribiwriter:    https://oribi.se/en/apps/oribi-writer/
---

I've been struggling with a very random bug when using an App Group to sync data between an app and its keyboard extension. The reason turned out to be quite a nice combination of human error and Xcode being Xcode. Let's jump on the App Group roller coaster for a fun ride - enjoy!


## Background

A client of mine is using [KeyboardKit Pro]({{page.keyboardkit}}) to handle dictation in their [app]({{page.oribiwriter}})'s keyboard extension. Since a keyboard extension doesn't have access to the microphone, it must open the main app, which can then access the microphone, perform dictation and return to the keyboard when the operation is done.

In KeyboardKit Pro, the keyboard will use a deep link to open the main app, which starts dictation, writes the dictated text into a shared data container, then pops back to the keyboard, which gets the dictated text from the shared container and sends it to the text document.

The library offers tools that make this pretty complicated operation easy to manage, and I use it in many apps without any problems. It's actually a feature that I'm very proud over, since it solves a hard problem in an elegant and developer-friendly way.

In this app, however, the dictation feature didn't work well at all. The keyboard would consistently open the main app, but dictation would randomly not start.

To understand this problem, let's take a look at how data transfer works between app targets, what kind of app this is, how it was configured and then finally how all these factors were combined into a problem that was really tricky to understand, but once I understood it was really easy to solve.


## How to share data between an app and its extension

To share data between the main app and the keyboard extension, I use an `App Group` together with a `suiteName`-based `UserDefaults`. If you use your app group ID as `suiteName:` parameter, the user defaults instance will automatically sync the data between the two targets.

For this to work, the App Group must be registered for both targets, and both targets use the same suite name. KeyboardKit Pro has tools to do this in a reliable way, so it has never been a problem setting it up in any app before this.


## The problem

The first problem I faced was that the app is a `DocumentGroup`-based app. Unlike other `SwiftUI` apps, the `DocumentGroup` that lies at the heart of a document app is not a view. The only view you have is the one you define for your documents, which is only available if you have an open document.

Since `SwiftUI` uses the `onOpenURL` view modifier to detect deep links, my deep link-based approach didn't work here, since I didn't have a view to attach this modifier to. I also didn't have a view to reliably present the dictation screen from. 

To solve this, I added new dictation features so that the keyboard could write to the shared container to tell the app to start dictation. It could then trigger the deep link as usual, but instead of checking the deep link, the main app could instead check this new flag. I tried it on a test app and it worked great.

Unfortunately, it didn't work in this particular app, for reasons that I could just not understand. Every once in a while it worked, but the next time the data was just not available, which meant that dictation would not start. If I then restarted the app, the data was there...but not if I kept the app running and continuously checked the shared data container.

I just could not understand what happened. I tried adding explicit synchronization commands to the code, which worked sometimes but not always. I tried creating explicit user default instances, which worked sometimes but not always. I felt like I was missing something, which I was.


## The app setup

The app is actually two apps - the same code is used to build an App Store facing app and an educational variant that is used in schools.

To handle this, I have multiple build configurations to build debug and release builds of both apps. I also use custom settings to setup bundle ID, display name etc. for the two apps.

This meant that when adding this feature, I had to use two different app groups, which I define like this:

![App Group setup]({{page.assets}}app-groups.png){:width="600px"}

I also need to use two different deep links, one for each app:

![Deep Link setup]({{page.assets}}deep-links.png){:width="600px"}

I then have app-specific configurations so that the regular app used the first App Group and deep link, and the educational app used the second ones.

Turns out that I made a mistake in this configuration, which in combination with an Xcode bug caused this problem in a very hard to detect way.


## The Xcode problem

Just like with the app groups and deep links, I need to use separate names for the two apps. This is what it should look like:

![App name setup]({{page.assets}}app-name.png){:width="600px"}

However, for some reason, and for this app only, I have a problem where Xcode resets the app name to `Oribi Writer` for **both** apps, any time I change the version number. This means that even if I run the Oribi Writer Edu app, it will be displayed as Oribi Writer, at which I must manually reset the name.

Turns out that this Xcode bug (?) was a key part of the dictation problem. Although it was caused by a project configuration error, as we soon will see, it was much harder to understand due to this name bug.


## The problem explained

As I struggled with the dictation problem, I noticed that Xcode had once again changed the display name. As I reset the name for the hundred time, it suddenly hit me.

*The Oribi Writer keyboard randomly opened Oribi Writer Edu instead of Oribi Writer, and vice versa!*

Turns out that I made a mistake when setting up the deep links - both apps were configured with both deep links! This means that when both apps are installed on the same device, the same deep link will open a random app, since both apps are registered to use it.

This means that combined with the Xcode naming problem, Oribi Writer Edu would sometimes launch with the name Oribi Writer. But since the app groups are not shared between the two apps, data that is written by a keyboard extension will only be available to that extension's main app, not the other app.

All it took to fix was to setup a single deep link that used app-specific build configurations to define the ID and the scheme of the link:

![App name setup]({{page.assets}}deep-link.png){:width="600px"}

With this single deep link, the keyboard will always open its own main app, even when both apps are installed on the same device.


## Conclusion

This problem has had be struggling for a few days, and I'm sooooo relieved to finally understand what caused this strange behavior.  I hope you enjoyed this roller coaster ride. God knows I didn't :)