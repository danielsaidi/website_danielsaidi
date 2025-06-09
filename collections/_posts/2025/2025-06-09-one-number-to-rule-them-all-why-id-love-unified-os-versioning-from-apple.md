---
title:  "One Number to Rule Them All: Why I'd Love Apple's Unified OS Versioning"
date:   2025-06-08 07:00:00 +0000
tags:   swift apple

assets: /assets/blog/25/0609/
image:  /assets/blog/25/0609/image.jpg

bsky: https://bsky.app/profile/danielsaidi.bsky.social/post/3lr4ml4oeqk2d
toot: https://mastodon.social/@danielsaidi/114649443676606699
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

This misalignment creates confusion for developers, who have to do complex availability checks to use features that were released in later OS versions than the app's/package's deployment target:

```swift
@available(iOS 18.0, macOS 15.0, tvOS 18.0, watchOS 11.0, visionOS 2.0, *)
func useLatestPlatformVersionFeature() {
    // Platform features that arrived in the latest OS updates
}
```

Notice how we have to memorize which iOS version that corresponds to which macOS version, and hope that we get the mapping right.


## A Potentially Beautiful Future

With unified versioning, our availability checks could become elegantly simple:

```swift
// Clean availability checks for features that are announced at WWDC'25
@available(os 25, *)
func useLatestPlatformVersionFeature() {
    // Works across all Apple platforms with version 25+
}
```

Imagine the clarity: instead of wondering "Is this iOS 16 feature available on macOS 13 or 14?", we'd simply know that all platforms version 25 and above support the same baseline features.


## Where This Strategy Wouldn't Work

While this OS naming convention is simple and beautiful, it would just work for OS version numbers from this year and forward. For older OS versions, I guess we still need the misaligned checks.

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


## Looking Forward

Whether Apple actually implements unified OS versions or not remains to be seen, but the current OS version checks that we have to make are pretty bad.

As a developer who works with multi-platform software on a daily basis, I'm therefore hoping that the rumors are true. One number to rule them all sounds very appealing.

What do you think? Would unified OS versioning make your development life easier, or do you see any downsides that I'm missing?