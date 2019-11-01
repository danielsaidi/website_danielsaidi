---
title:  "App Store submission fails when Carthage uses SPM generated project"
date:   2019-10-29 12:00:00 +0100
tags:   catalina beta swift-ui
---

If you have a Swift Package Manager library and use `swift package generate-xcodeproj` to generate an Xcode project for the library, App Store will reject any apps that pull in the library with [Carthage][Carthage]. This post will show you how to make App Store submission work.


## Background

I have some open source libraries that support [CocoaPods][CocoaPods] and [Carthage][Carthage]. As Apple this WWDC announced that Swift Package Manager (SPM) is coming to iOS, I started experimenting with adding SPM support as well.

SPM doesn't require you to have an Xcode project. The package file and source files are enough. However, if you want to support Carthage, add a demo app etc. you may want to create a project alongside the package.

Since my libraries support Carthage and have demo apps, I decided to keep my old Xcode projects around. However, I soon noticed that these projects didn't play well with SPM, for instance when handling their dependencies to the library. I therefore decided to create new projects with `swift package generate-xcodeproj`.


## The problem

The project generation is a quick operation, that finishes in seconds and produces a shiny, new Xcode project. However, there are many problems the project's initial configuration. I will cover structure and configuration problems in another post, but hear me out on this one.

The generated projects work great with SPM. However, I soon started receiving reports that apps that use Carthage were rejected when being submitted to the App Store:

```
"This bundle Payload/Offgrid.app/Frameworks/XXX.framework is invalid. The Info.plist file is missing the required key: CFBundleVersion. Please find more information about CFBundleVersion at https://developer.apple.com/documentation/bundleresources/information_property_list/cfbundleversion"
```

If you look at the `Info.plist` that is linked in the library's build settings, the `CFBundleVersion` key is actually defined. You can find a more discussion about this problem [here](https://github.com/danielsaidi/Sheeeeeeeeet/issues/116).


## The solution

Instead of adjusting the `Info.plist`, which is correctly setup, select the project in the project navigator, select the library target, go to `Build Settings` and search for `current project version`. Instead of an empty value, enter `1`. This will make App Store submission work, even when using Carthage.


[Carthage]: https://github.com/Carthage
[CocoaPods]: http://cocoapods.org