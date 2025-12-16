---
title: Localizing Swift Packages with String Catalogs
date: 2025-12-14

assets: /assets/talks/25/1215-cocoaheads/
image: /assets/talks/25/1215-cocoaheads/image.jpg
images:
  - img-1.jpg
  - img-2.jpg
  - img-3.jpg
  - img-4.jpg

location: CocoaHeads Sthlm
location-url: https://meetup.com/CocoaHeads-Stockholm

slides: https://github.com/danielsaidi/website_danielsaidi/releases/download/talks-2025/251208-CocoaHeads.zip
slides-screenshot: 1
# video: https://www.youtube.com/watch?v=D7NuxbnY1K0&list=PL-wmxEeX64YTpDbpfszWMV76oZZO3wxZH&index=14

post-1: /blog/2025/12/14/localizing-swift-packages-with-string-catalogs
post-2: /blog/2025/12/02/a-better-way-to-localize-swift-packages-with-xcode-string-catalogs
sps: http://github.com/danielsaidi/SwiftPackageScripts

tags: cocoaheads-sthlm slides swift l10n

abstract: |  
  This talk explores Xcode string catalogs and the drastic improvemenets introduced in Xcode 26. We will look at the key differences between app and package localization and common pitfalls.
  
  We'll also look at how we can use Xcode 26's new symbol generation to get compile-time safety, and how to set up a shared translation package that can be used by many packages and apps.
---

I gave this talk at [{{page.location}}]({{page.location-url}}) on how to localize Swift packages with String Catalogs and how to create a shared localization package that can be used by multiple apps and packages.

I have written about String Catalogs in Swift Packages in [here]({{page.post-1}}) and [here]({{page.post-2}}). See [SwiftPackageScripts]({{page.sps}}) for the scripts I use to generate public keys for the Xcode generated, package internal ones.