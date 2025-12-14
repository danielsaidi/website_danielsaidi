---
title:  Upcoming Talk - Localizing Swift Packages with Xcode String Catalogs
date:   2025-12-14 07:00:00 +0000
tags:   conferences talks sdks spm

image:  /assets/blog/25/1214/image.jpg
image-show: 0

url:    https://www.meetup.com/cocoaheads-stockholm
post:   /blog/2025/12/02/a-better-way-to-localize-swift-packages-with-xcode-string-catalogs

bsky:   https://bsky.app/profile/danielsaidi.bsky.social/post/3ll6sndqbek2g
toot:   https://mastodon.social/@danielsaidi/114222023314551015
---


I'm giving a talk at [CocoaHeads Stockholm]({{page.url}}) tomorrow, December 15th, on how to use Xcode string catalogs to localize Swift packages, and how to build a shared translations package.


## Xcode String Catalogs

Xcode string catalogs were introduced at WWDC 23 as a replacement for `Localizable.strings`. It lets you manage all locales, as well as device and plural variations, with a single catalog file. 

Xcode will automatically generate keys as you add new keys to your source code and SwiftUI views, and will also keep track of stale and untranslated keys. 

While this is very convenient and a huge improvement from the old `.strings` file, it was still easy to break localizations by changing the key in code, either intentionally or by accident.


## Xcode 26 Improvements

Xcode 26 improves things by generating symbols for keys that are directly added to a string catalog. We can then refer to these auto-generated symbols like this:

```swift
Text(.myPackageDefinedKey)
```

These generated symbols remove the hassle of having to specify the key bundle, which was a huge pain and source of error when defining translations in a Swift package.

This also gives us compile-time safety, since removing keys in use will give us compile-time errors.

While this is a very big improvement to earlier year's limitations, there are still challenges in setting up a shared translation package, since these keys are internal. We'll address this in tomorrow's talk.


## Tomorrow's Talk

In tomorrow's talk, we'll look at these string catalog fundamentals, but will also cover how we can parse a string catalog and generate public keys for these internal keys.

With this in place, we can use a single Swift package to localize a whole set of packages and apps, which can be very useful in a large organization or in an app that consists of many modules. 

If you can't attend tomorrow's talk, I have written about this approach in [this blog post]({{page.post}}). It contains all the information you need, as well as links to external resources. 

The talk and slides will be available [here](/talks) after tomorrow. Feel free to check them out and let me know what you think.