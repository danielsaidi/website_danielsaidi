---
title:  "Create an SPM Package"
date:   2021-01-07 12:00:00 +0100
tags:   spm swift-ui cocoapods carthage fastlane bitrise
---

In this post, we'll create a package for the Swift Package Manager. We will also add support for two other dependency managers - CocoaPods and Carthage - and add support for Fastlane and Bitrise as well.

The project will be a tiny library that adds more gestures to `SwiftUI`. You can find the finished library [here][Project].


## Background

I have several open source projects and was very excited when Apple last WWDC announced that Swift Package Manager was coming to iOS. [CocoaPods][CocoaPods] and [Carthage][Carthage] are great dependency managers, but have problems that SPM has potential to solve, since it's integrated into Xcode.

However, since many people are still using [CocoaPods][CocoaPods] and [Carthage][Carthage] (we use all three at work) it's a good practice to support all three when you create an open source library. Therefore, this post will also cover how to add support for [CocoaPods][CocoaPods] and [Carthage][Carthage] as well.


## Xcode Project

SPM doesn't require an Xcode project, but will require one to run `UIKit`-based unit tests and to support [Carthage][Carthage]. We will therefore create an Xcode project, but will have to adjust it, since it will be incorrectly configured.

I have written a separate post about these and will refer to it every now and then throughout this post. You can find the post [here][Problems].


## Step 1 - Create a package

In the terminal, navigate to where you keep your projects and create and navigate to a new folder for our project: 

```bash
mkdir SwiftUIGestures
cd SwiftUIGestures
```

You can now create an SPM package by typing `swift package init`. This will  








[Carthage]: https://github.com/Carthage
[CocoaPods]: http://cocoapods.org

[Project]: https://github.com/danielsaidi/SwiftUIGestures
[Problems]: https://danielsaidi.com/blog/2020/01/06/spm-problems