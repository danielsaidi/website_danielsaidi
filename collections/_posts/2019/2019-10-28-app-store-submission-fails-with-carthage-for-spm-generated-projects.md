---
title: App Store submission fails with Carthage and SPM
date:  2019-10-29 12:00:00 +0100
tags:  xcode spm
icon:  swift
---

If you use `swift package generate-xcodeproj` to generate an Xcode project for a SPM package, App Store will reject any apps that adds this library with [Carthage][Carthage]. This post will show you how to make App Store submission work.


## Background

I have some open-source libraries that support [CocoaPods][CocoaPods] and [Carthage][Carthage] and was excited when Apple announced SPM for iOS, since it can solve many of the problems that are involved with these tools.

SPM doesn't require you to have an Xcode project. However, if you want to support Carthage, add a demo app etc. you have to create a project alongside the package.

Since the libraries already supported CocoaPods and Carthage and had demo apps, I decided to keep the old projects around. This worked great at first, since it just required me to move source and test files to new folders. I published the updates and everything worked great with all dependency managers.

However, I soon noticed that the projects didn't play well with SPM, e.g. when keeping in sync with their library. I therefore decided to create new projects with `swift package generate-xcodeproj`.


## The problem

The project generation is quick, finishes in seconds and produces a new Xcode project. This may seem fine at first, but there are many problems with this project and its configuration.

Shortly after recreating the projects and pushing out the changes, I started receiving reports that apps that used Carthage to add the libraries were rejected when they were submitted to the App Store:

```
"This bundle Payload/Offgrid.app/Frameworks/XXX.framework is invalid. The Info.plist file is missing the required key: CFBundleVersion. Please find more information about CFBundleVersion at https://developer.apple.com/documentation/bundleresources/information_property_list/cfbundleversion"
```

I had a look at the libary `Info.plist`, but found that the `CFBundleVersion` key was actually defined in this file. The error message is therefore incorrect. 

You can find a more detailed discussion about this problem [here][Discussion].


## The solution

Instead of adjusting the `Info.plist`, which is correct, select the project in the project navigator, select the library target, go to `Build Settings` and search for `current project version`. 

Instead of an empty value, enter `1`. This will make App Store submission work once again, even when using Carthage.


[Carthage]: https://github.com/Carthage
[CocoaPods]: http://cocoapods.org

[Discussion]: https://github.com/danielsaidi/Sheeeeeeeeet/issues/116