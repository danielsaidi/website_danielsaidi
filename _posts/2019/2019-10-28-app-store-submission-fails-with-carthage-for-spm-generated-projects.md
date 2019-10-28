---
title:  "App Store submission fails when using SPM generated projects with carthage"
date:   2019-10-29 12:00:00 +0100
tags:   catalina beta swift-ui
---

If you have a Swift Package Manager library and use `swift package generate-xcodeproj` to generate an Xcode project, App Store will reject any apps that pull in your library with Carthage. This blog post will show you how to fix this.


## Background

I have some open source libraries that historically have supported [CocoaPods][CocoaPods] and [Carthage][Carthage]. When Apple announced Swift Package Manager's extended platform support on WWDC'19, I added SPM support to these projects as well.

When adding SPM support (which doesn't require an Xcode project) I initially kept my old Xcode project around for Carthage support. However, I soon noticed that the old project didn't play well with SPM, e.g. when handling dependencies to the library.

I therefore decided to setup a new project with `swift package generate-xcodeproj`. This is a quick operation that finishes in seconds and produces a shiny new Xcode project. However, there are many problems the project's initial configuration.


## The problem

One problem with the generated project is that it will work perfectly with SPM, but will be rejected by Apple if an app pulls in the library with Carthage. App Store submissions will fail with this error message:

```
"This bundle Payload/Offgrid.app/Frameworks/XXX.framework is invalid. The Info.plist file is missing the required key: CFBundleVersion. Please find more information about CFBundleVersion at https://developer.apple.com/documentation/bundleresources/information_property_list/cfbundleversion"
```

If you look at the `Info.plist` that is linked in the library's build settings, the `CFBundleVersion` key is actually defined, which makes this problem hard to discover and solve. You can find a more discussion about this problem [here](https://github.com/danielsaidi/Sheeeeeeeeet/issues/116).


## The solution

Instead of adjusting the `Info.plist`, which is correctly setup, select the project in the project navigator, select the library target, go to `Build Settings` and search for `current project version`. Instead of an empty value, enter `1`. This will make App Store submission work, even when using Carthage.

[Carthage]: https://github.com/Carthage
[CocoaPods]: http://cocoapods.org

