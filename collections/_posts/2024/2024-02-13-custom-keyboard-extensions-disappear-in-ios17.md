---
title:  Custom keyboards disappear in iOS 17
date:   2024-02-13 06:00:00 +0000
tags:   ios keyboard

image:  /assets/blog/2024/240213/title.jpg

tweet:  https://x.com/danielsaidi/status/1757393769593274774?s=20
toot:   https://mastodon.social/@danielsaidi/111924367408233262
---

{% include kankoda/data/open-source.html name="KeyboardKit" %}
Some [{{project.name}}]({{project.url}}) users have reported that their custom keyboard extensions disappear in iOS 17. This article discusses some concerning findings after investigating this problem.

When this happens, the keyboard no longer shows up in System Settings or the keyboard switcher that appears when you press the 🌐 key in the keyboard.


## Bundle ID

The problem only seems to affect a small number of apps, and all investigated so far have had a bundle identifier that starts with `se.` (the Swedish top domain).

To investigate this, I created some test apps where bundle ID was the only difference, then added a custom keyboard extension to each app. This was the result:

* ✅ Bundle ID starts with `com.` - the keyboard shows up.
* ✅ Bundle ID starts with `eu.` - the keyboard shows up.
* 🚨 Bundle ID starts with `se.` - the keyboard doesn't show up.
* ✅ Bundle ID starts with `de.` - the keyboard shows up.
* ✅ Bundle ID starts with `da.` - the keyboard shows up.
* ✅ Bundle ID starts with `dk.` - the keyboard shows up.

As soon as I changed the bundle ID prefix from `se.` to e.g. `com.`, the keyboard appeared.

The `com.` prefix is by far the most common bundle ID prefix, while `eu.` is sometimes used within the European Union. This problem had been more severe if it affected these IDs too.

The `se.` prefix is the Swedish top domain, so some Swedish companies are thus affected if their company uses an `se.` domain instead of `com.`


## Affected platforms

As I investigated this problem, I noticed it on devices that used iOS 17.2. Upgrading to iOS 17.3.1 didn't solve it. I haven't tested on iOS 17.1, but will try to find such a device.

The problem hasn't appeared for me on iOS 15, nor on iOS 17.0. I'm currently updating an iOS 17.0 device to 17.3.1 to see if it starts appearing then.


## Conclusion

The problem only seems to occur it the keyboard bundle ID starts with `se.`. Since this only happens in iOS 17.2 and later, the problem may be related to new Sámi keyboards.

iOS 17.2 introduced new Sámi keyboard, of which some have an `se.` bundle ID prefix. It is therefore easy to see a potential connection to custom `se.` keyboards disappearing.

I have reported this critical problem to the Apple Feedback Assistant and requested a TSI (Technical Support Issue), but the feedback is still open and Apple returned the TSI. 

Please let me know if you also experience this problem, so that we can provide Apple with more information. If you want to refer to the Apple Feedback Assistant, use `FB13611131`.