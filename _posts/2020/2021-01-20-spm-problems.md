---
title:  "SPM Package Problems"
date:   2021-01-06 12:00:00 +0100
tags:   spm
---


However, since many people are still using [CocoaPods][CocoaPods] and [Carthage][Carthage] (we use all three at work) it's a good practice to support all three when you create an open source library. Therefore, this post will also cover how to add support for [CocoaPods][CocoaPods] and [Carthage][Carthage] as well.



I have several open source projects and was very excited when Apple announced that Swift Package Manager is coming to iOS. After adding SPM support to my projects, I can now say that it's great, but not yet perfect. In this post, I'll list some SPM problems that I have faced, and how I've solved or learned to live with them.

This post is based on personal experiences, that may very well be caused by that I am using SPM incorrectly. Feel free to leave comments with your own experiences and correct me whenever I've got something wrong or if a SPM bug has been fixed.


## No static resources

SPM currently doesn't support static resources, so if you have to embed `xib`s, images, fonts etc. into your project, you can't use SPM.


## Invalid .gitignore

If you create a Swift Package with `swift package init`, the generated `.gitignore` will exclude any Xcode projects you later add, which may come as a nasty surprise. [Read more here][gitignore].


## Invalid Xcode project

SPM doesn't require an Xcode project. You can just place source files in `Sources` and test files in `Tests` and SPM will happily consider it to be a package. However, if you want to add a demo app, add Carthage support etc. you need an Xcode project.

You can add an Xcode project to your package by running `swift package generate-xcodeproj` in your package root. This will generate a project for you. However, the generated project is invalid in a number of ways. Perhaps I am just doing it wrong, but you will have to fix many things.

For instance, the project will cause App Submission to fail, if the library is added to an app using Carthage. [Read more here][carthage].

xxxxxx


## Unit testing UIKit

If you create a new SPM package and add `UIKit` to it, you will not be able to run unit tests from the terminal. If you do, you will get this kind of errors:

xxxxx

To be able to run the tests, you have to create an Xcode project (see above) and run the tests through the project instead. You do this by

xxxxxx


## Package Synchronization

Xcode will automatically sync SPM dependencies every now and then, e.g. when you switch git branch. Even though it's handly, it's also dangerous, since it doesn't cancel ongoing sync operations.

This means that if you switch branch while a sync is ongoing, your `Package.lock` file can become invalid if a sync completes on another branch than it started on.


[carthage]: https://danielsaidi.com/blog/2019/10/29/app-store-submission-fails-with-carthage-for-spm-generated-projects
[gitignore]: https://danielsaidi.com/blog/2020/01/02/spm-gitignore
[SwiftUIBlurView]: https://github.com/danielsaidi/SwiftUIBlurView

We will also add support for two other dependency managers - CocoaPods and Carthage - and add support for Fastlane and Bitrise as well.