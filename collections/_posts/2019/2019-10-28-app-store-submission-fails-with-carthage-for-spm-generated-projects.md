---
title: App Store submission fails with Carthage and SPM
date:  2019-10-29 12:00:00 +0100
tags:  swift xcode spm cocoapods
icon:  swift
---

When you use `swift package generate-xcodeproj` to generate an Xcode project for a Swift Package, App Store will reject any apps that adds this library with [Carthage][Carthage].


## Background

I have some open-source projects that support [CocoaPods][CocoaPods] and [Carthage][Carthage] and was excited when Apple announced SPM for iOS, since it can solve many problems involved with this.

For instance, SPM doesn't require you to have an Xcode project. However, if you want to support Carthage, add a demo app etc. you have to add a project alongside the package.

Since my libraries already support CocoaPods & Carthage and had demo apps, I decided to keep the old projects. This worked great at first, since it just required me to move some files to new folders. Everything worked great with all dependency managers.

However, I noticed that the projects didn't play well with SPM, e.g. when keeping in sync with their library. I therefore decided to create new projects with `swift package generate-xcodeproj`, to get a fresh start.


## The problem

The SPM project generation finishes in seconds and produces a new Xcode project. This may seem fine at first, but there are many problems with this project and its configuration.

Shortly after recreating these projects, I started receiving reports that some apps that used Carthage to add the libraries were rejected when they were submitted to the App Store:

> This bundle Payload/Offgrid.app/Frameworks/XXX.framework is invalid. The Info.plist file is missing the required key: CFBundleVersion. Please find more information about CFBundleVersion at https://developer.apple.com/documentation/bundleresources/information_property_list/cfbundleversion

I had a look at the `Info.plist` and found that the `CFBundleVersion` was actually defined in this file. The error was thus incorrect. You can find more information about this [here][Discussion].


## The solution

To fix this problem, select the project in the project navigator, select the library target, go to `Build Settings` and search for `current project version`. 

Instead of an empty value, enter `1`. This will make App Store submission work once again, even when using Carthage.


[Carthage]: https://github.com/Carthage
[CocoaPods]: http://cocoapods.org

[Discussion]: https://github.com/danielsaidi/Sheeeeeeeeet/issues/116