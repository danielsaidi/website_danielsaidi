---
title:  Custom keyboard extensions disappear in iOS 17
date:   2024-02-13 06:00:00 +0000
tags:   ios keyboard

image:  /assets/blog/2024/240213/title.jpg

tweet:  https://x.com/danielsaidi/status/1757393769593274774?s=20
toot:   https://mastodon.social/@danielsaidi/111924367408233262
---

{% include kankoda/data/open-source.html name="KeyboardKit" %}
Some [{{project.name}}]({{project.url}}) users have reported that their keyboard extensions have disappeared in iOS 17. This article discusses some concerning findings after investigating this problem.

When this happens, the keyboard no longer shows up in System Settings or the keyboard switcher. Components like the KeyboardKit `KeyboardStateLabel` can still detect the state of the keyboard, but it doesn't show up anywhere in iOS.


## Bundle ID

The problem only seems to affect a small number of apps, and all investigated so far have had a bundle identifier that didn’t start with `com.`.

To investigate this, I created some test apps where bundle ID was the only difference, then added a custom keyboard extension to each app. This was the result:

* ✅ Bundle ID starts with `com.` - the keyboard shows up.
* ✅ Bundle ID starts with `eu.` - the keyboard shows up.
* 🚨 Bundle ID starts with `se.` - the keyboard doesn't show up.
* ✅ Bundle ID starts with `de.` - the keyboard shows up.
* ✅ Bundle ID starts with `da.` - the keyboard shows up.
* ✅ Bundle ID starts with `dk.` - the keyboard shows up.

The `com.` prefix is by far the most common bundle ID prefix, while `eu.` is sometimes used within the European Union. This problem had been more severe if it affected these IDs too.

The `se.` prefix is however the Swedish top domain, so some Swedish companies thus use it in their bundle ID.

I created many `se.` test apps, and never once did the keyboard appear. If I changed the ID to start with `com.`, `eu.`, or something else, the keyboard appeared.


## Locale?

Since `se` is both the Swedish top domain, as well as the Swedish locale identifier’s region code, I first suspected that the problem could be locale-related.

I therefore tried changing the prefix from `se.` to `de.` (German) as well as `da.` and `dk.` (Denmark), but the keyboard worked perfectly for all these cases.

So far, I've only seen this problem with apps that have a bundle ID that starts with `se.`. 


## Affected platforms

As I investigated this problem, I noticed it on devices that used iOS 17.2. Upgrading to iOS 17.3.1 didn't solve it. I haven't tested on iOS 17.1, but will try to find such a device.

The problem hasn't appeared for me on iOS 15, nor on iOS 17.0. I'm currently updating an iOS 17.0 device to 17.3.1 to see if it starts appearing then.


## Conclusion

The problem seems to be related to the bundle ID, but I've only seen it with apps that have a bundle ID that starts with `se.`. Since this is a critical bug for any apps it affects, I hope to find a way to solve or work around it soon.

I have reported this to the Apple Feedback Assistant, and will probably use a support ticket to get someone on the line. 

Please let me know if you also experience this problem, so that we can provide Apple with more information. If you want to refer to the Apple Feedback Assistant, use `FB13611131`.