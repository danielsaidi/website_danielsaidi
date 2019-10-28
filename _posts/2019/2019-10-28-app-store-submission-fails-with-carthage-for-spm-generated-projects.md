---
title:  "App Store submission fails when using SPM generated projects with carthage"
date:   2019-10-29 12:00:00 +0100
tags:   catalina beta swift-ui
---

If you have a Swift Package Manager package and use `swift package generate-xcodeproj` to generate an Xcode project, App Store will reject any apps that pull in your library with Carthage. This blog post will show you how to fix this.


## Background

I have a couple of open source projects, that historically have supported [CocoaPods][CocoaPods] and [Carthage][Carthage]. When Apple announced Swift Package Manager's extended platform support on WWDC'19, I started looking at how to add support for SPM in these projects.

After adding SPM support (which doesn't require an Xcode project) I initially kept my old Xcode project around to keep supporting Carthage. This made my projects support SPM, but the CocoaPods and Carthage support was still working like before.

However, I soon noticed that the old project setup didn't play well with SPM, since I had to refer to the SPM code and tests outside, pull in test libraries twice etc. So I decided to setup a new project with `swift package generate-xcodeproj`.

This is a really quick operation. It finishes in seconds and produces a new Xcode project with everything setup. However, there are a bunch of problems with this project and how its configured, many of which I will try to cover in another post soon enough.


## The problem

Even though your project will work perfectly well with SPM, the project is incorrectly configured and will be rejected by Apple if a developer pulls in your library using Carthage. The App Store submission will fail with this error message:

```
"This bundle Payload/Offgrid.app/Frameworks/XXX.framework is invalid. The Info.plist file is missing the required key: CFBundleVersion. Please find more information about CFBundleVersion at https://developer.apple.com/documentation/bundleresources/information_property_list/cfbundleversion"
```

However, if you look at the `Info.plist` that is linked in the library's build settings, you will notice that the file has this key is actually defined. You can find a detailed discussion about this problem [here](https://github.com/danielsaidi/Sheeeeeeeeet/issues/116).


## The solution

Instead of adjusting the `Info.plist`, all you have to do is to select the project then select the library target in the list of project targets. Go to `Build Settings` and search for `current project version`. Instead of an empty value, enter `1`.

Specifying a current project version will make the App Store submission work, even when using Carthage.


[Carthage]: https://github.com/Carthage
[CocoaPods]: http://cocoapods.org

