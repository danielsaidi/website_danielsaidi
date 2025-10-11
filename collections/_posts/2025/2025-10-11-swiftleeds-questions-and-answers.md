---
title:  SwiftLeeds - Questions & Answers
date:   2025-10-11 07:00:00 +0000
tags:   conferences sdks

assets: /assets/blog/25/1011/
image:  /assets/blog/25/1011/image.jpg
image-show: 0

talk: /talks/2025/10/08/best-practices-in-sdk-development
swiftleeds: https://swiftleeds.co.uk

emojikit: https://github.com/danielsaidi/emojikit
keyboardkit: https://keyboardkit.com
licensekit: https://kankoda.com/sdks/licensekit
vietnameseinput: https://kankoda.com/sdks/vietnameseinput

bsky: https://bsky.app/profile/danielsaidi.bsky.social/post/3m2wj6hnxbc24
toot: https://mastodon.social/@danielsaidi/115356272837729376
---

I recently did a talk on [Best Practices in SDK Development]({{page.talk}}) at [SwiftLeeds '25]({{page.swiftleeds}}). I got so much feedback and great questions that I decided to post the questions and answers here.


## Expanding the API Surface Area

**Question:** Do you feel pressure from your users to increase your API surface area - e.g. “please can you expose this private API”? Do you push back, or make stuff public when people ask?

Sometimes, and the same goes for adding new features. The way that I have (partially) solved it for [KeyboardKit]({{page.keyboardkit}})'s services is that I expose both the protocol and the standard implementation, which is open to inheritance. The user can then use the standard service implementation, implement the protocol from scratch, or inherit the standard implementation. The protocol only defines what is required by the SDK, while the standard base class has a lot of additional logic that can be used and overridden by the developer.


## Feature Flags

**Question:** What is your opinion on feature flags inside SDKs?

I think feature flags are great, but I mark all experimental features as (BETA), with the versioning strategy that beta features can break and be removed at any time. Use them at your own risk.


## Monetization

**Question:** How do you monetise private SDK? What are the main sales channels?

I have created a [license engine]({{page.licensekit}}) that can integrate with 3rd party services like Gumroad and Paddle, encode licenses into the binary, and create and parse encrypted license files. For [KeyboardKit]({{page.keyboardkit}}), I sell standard licenses via Gumroad and business and enterprise licenses via invoice. I have not done any active marketing or sales, so everything is so far inbound, where people and companies reach out to purchase a license. But the online subscription channel allowed me to remove myself as a bottleneck, and is completely automated. I have license feature tables and all required information up on the KeyboardKit website, to guide people to the best plan for their needs.


## Xcode Management

**Question:** Did you find a good way to open both the package of an SDK as well as a sample project that uses that package in Xcode?

If you have a demo project that pulls in the SDK as a local package, you can edit the package source code from the demo project. You can also create an Xcode project for the SDK and have that project open at the same time as another project that pulls in the package.


## Vibe Coding Competition

**Question:** How do you prevent people clone-vibe-coding your SDK and have you noticed any drop in sales since advance of coding LLMs?

I think SDKs like [KeyboardKit]({{page.keyboardkit}}) involve too much domain-specific knowledge and too many nitty gritty details to be easily vibe coded. But I can be wrong. Time will tell. When building a complex SDK, the challenge of long term stability, reliability and consistency, combined with the limited context of an LLM, puts a lot of responsibility on the SDK vendor, so it will be very interesting to see if vibe coded products will hold up over time, when no person has been involved in developing the foundation.


## Binary Package Dependencies

**Question:** What’s your view about having (binary or opensource) SDKs that use dependencies?

I wrote a [blog post](https://danielsaidi.com/blog/2025/05/02/adding-dependencies-to-binary-swift-packages) on how to add dependencies to a binary package, which is a bit tricky compared to open-source. Regarding whether to use dependencies or not, I think it's a case by case decision. [KeyboardKit]({{page.keyboardkit}}) should for instance not contain the [LicenseKit]({{page.licensekit}}) SDK, which is currently copied into the SDK with a sync script. My [VietnameseInput]({{page.vietnameseinput}}) SDK depends on LicenseKit with a proper dependency, which I think is fine it's completely standalone, and shouldn't be part of the public API. I would think one extra step before adding an external dependency, since your SDK will then be subject to the stability of an external library that you can't control. Perhaps forking it before adding it to the SDK?


## Changelogs

**Question:** How do you handle changelogs?

I had an entire set of slides regarding the release process, that I had to exclude. There I discussed the importance of automating all the boring, tedious, repetitive and error-prone parts of a release, while keeping some parts manual and personal. The changelog and release notes are two such things. I manually write down every change in a manual release notes document, and also write a release article on the product blog  for each minor and major version.


## Hyrum's Law

**Question:** Do you account for behavioural breaking changes, where the API remains stable but the underlying behaviour changes in a way that breaks the developer workflow? (aka the Hyrum's Law).

This is a tricky one, great question! I would definitely consider anything that changes the *expected* behavior as a breaking change. In that case, I'd probably add an optional configuration that defaults to the old behavior, and guide users to the new one. If the behavior change is due to correcting an incorrect behavior, like how KeyboardKit not always behaves like the system keyboard, I consider changing such incorrect behaviors to be bug fixes.


## Behaviour Breaking Changes #2

**Question:** Do you consider behaviour breaking changes (a colour change) or only API breaking changes?

When it comes to style changes, I'm not as concerned as breaking changes that causes the project to not build for a user. I am definitely changing the style and design of the keyboard, even in patch updates, but do my best to mention that in the release notes.


## Sherlock

**Question:** Are you concerned that Apple will sherlock your SDK?

Every year at WWDC. 😥


## Structs vs Protocols

**Question:** For user customization, is there any reason to prefer a struct instead of a protocol that SDK user can implement and set? (i.e. protocol AutocompleteConfiguration with all the variables)

I prefer to use structs for plain values, and protocols for things that can be implemented in various ways. So structs for configurations, styles, etc. and protocols for services and concepts. Developers can always map anything they need to that type, which rids the SDK of unnecessary complexity.


## Spelunking

**Question:** When you publish an SDK via Swift Package Manager, users will be able to browse freely around all of your SDK code. Is there a way to prevent them seeing some files/classes/code etc.?

The closed-source projects are distributed as XCFrameworks, using binary package targets. So they all have a public distribution repository with the public package, demo apps, etc. and a private repo for the source code. So the source code is not exposed.


## Regrets

**Question:** Do you have an example where you regretted to declare a class as open.

Absolutely, I have placeholder services in [KeyboardKit]({{page.keyboardkit}}) that are used until developers register their KeyboardKit Pro license to unlock new features. If we stick to the autocomplete feature, which is a Pro feature, a `DisabledAutocompleteService` is used when there's no license registered. That type, as well as it's initializer, are both public. For no reason. It's not like the user needs to create a disabled service. And every year I think that I will remember to fix that in the next major release...and now it's still public in KeyboardKit 10. You know what, I'll add a todo right now. Thank you! :)


## LLMs

**Question:** Does LLM vibe coding eating into your revenue?

Not yet 😅


## Non-exhaustive Enums

**Question:** Is there a way to specify a "non exhaustive" enum in Swift? (I know many Apple Enums *force* you to have an unknown/default clause to help with future expansion)

You can always add a `custom` case with associated values to let users expand an enum with custom values, but if you do, you must design your SDK to not rely on exhaustive checks for that enum. I do have a few of those enums in some SDKs, like the `KeyboardAction`, but I try to avoid it.