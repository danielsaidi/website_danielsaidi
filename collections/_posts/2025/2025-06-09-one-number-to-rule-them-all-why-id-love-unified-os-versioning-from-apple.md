---
title:  "One Number to Rule Them All: Why I'd Love Apple's Unified OS Versioning"
date:   2025-06-08 07:00:00 +0000
tags:   swift apple

assets: /assets/blog/25/0609/
image:  /assets/blog/25/0609/image.jpg

bsky: https://bsky.app/profile/danielsaidi.bsky.social/post/3lr5sfscy4227
toot: https://mastodon.social/@danielsaidi/114652103712648073
---


Apple's rumored plans to unify all platform OS versions and potentially jump to version 25 (or 26) for iOS, iPadOS, macOS, tvOS, watchOS, *and* visionOS, might seem like a cosmetic change. But as someone who wrestle with availability checks across Apple's ecosystem on a daily basis, I can't help to get excited about this change.


## The Current Numbering Nightmare

Right now, tracking feature availability across Apple platforms feels like juggling different calendars, where we're currently dealing with:

- iOS 18
- iPadOS 18 (not really a thing in code, though)
- macOS 15 (Sequoia)
- tvOS 18
- watchOS 11
- visionOS 2

This misalignment creates pains for multi-platform developers who have to do complex availability checks to use features that were released in later OS versions than the deployment target:

```swift
@available(iOS 18.0, macOS 15.0, tvOS 18.0, watchOS 11.0, visionOS 2.0, *)
func useLatestPlatformVersionFeature() {
    // Platform features that arrived in the latest OS updates
}
```

Notice how we have to memorize which iOS version that corresponds to which macOS version, and vice versa for all the platforms that we have to support.


## A Potentially Beautiful Future

With unified versioning, our availability checks could become elegantly simple:

```swift
// Clean availability checks for features that are announced at WWDC'25
@available(os 25, *)
func useLatestPlatformVersionFeature() {
    // Works across all Apple platforms with version 25+
}
```

Imagine that instead of wondering "Is this iOS 16 feature available on macOS 13 or 14?", we'd simply know that all platforms version 25 and above support the same baseline features.


## Where This Strategy Wouldn't Work

While this naming convention is simple and wonderful, it would just work for OS versions from this year and forward. For older OS versions, I guess we will still need the misaligned checks.

It will also be interesting to see how they will handle misaligned feature releases in future versions, since we'll need individual checks for features that release different years for different OS versions.

```swift
// Will it look like this if watchOS gets a feature one year later?
@available(iOS 25, macOS 25, tvOS 25, watchOS 26, visionOS 25, *)
func useLatestPlatformVersionFeature() { ... }

// ...or will we be able to do this?
@available(os 25, watchOS 26, *)
func useLatestPlatformVersionFeature() { ... }
```

Say what you will about Apple, but they usually do a pretty good job of designing these kind of tools, so I'm not too worried about this.


## Reasons to Worry

With Apple being Apple, and with naming not being their best skill (remember "The New iPad"?) I'm a little worried about what these unified versions will be called.

There's namely a rumor that they'll use the *next* year as the version number, instead of the year of release. This would means that features they announce att WWDC this year would belong "OS 26".

I truly hope this is not the case, but I am more than ready to being proven wrong and having to live with a bad taste in my mouth for the rest of my programming career.


## What I'm Hoping for 

My hope is that Apple will go with a developer-internal version number that is the same as the year of announcement/release, with a public "fun" name for public marketing.

This would be a great opportunity for Apple to leave the California-themed macOS names behind, and instead find a new concept for the future of their uniform platform strategy.

So perhaps we'll get a unified suite of platform versions that are numbered "OS 25", while Apple can market them as "iOS Aurora", "macOS Aurora", etc. that use a new, cool naming theme.

One can only hope.


## Looking Forward

Whether Apple actually implements unified OS versions or not remains to be seen, but the current OS version checks that we have to make are pretty bad.

As a developer who works with multi-platform software on a daily basis, I'm therefore hoping that the rumors are true. One number to rule them all sounds very appealing.

What do you think? Would unified OS versioning make your development life easier, or do you see any downsides that I'm missing?