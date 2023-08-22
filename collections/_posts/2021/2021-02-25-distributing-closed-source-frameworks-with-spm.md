---
title:  Distributing closed-source frameworks with SPM
date:   2021-02-15 07:00:00 +0100
tags:   swift spm xcode open-source closed-source
icon:   swift
---


In this post, we'll look at how to distribute closed-source XCFrameworks-based products with the Swift Package Manager (SPM). The post goes through project setup, package distribution and how to solve some problems along the way.

{% include kankoda/data/open-source.html name="LicenseKit" %}{% assign licensekit=project %}
{% include kankoda/data/open-source.html name="KeyboardKit" %}


## Background

I have an open-source project called [KeyboardKit]({{project.github}}), that can be used to build custom keyboards for iOS. Although it's open-source, it's currently my main source of income, through various freelance projects.

As I later wanted to explore other monetization models, I created a commercially licensed, closed-source extension called [KeyboardKit Pro]({{project.github_pro}}), that unlocks more locales and pro features.

In this post, let's take a look at some technical challenges that excited me with this project. It it possible to combine open-source with a license-based, closed-source extension that depends on the core library?

Let's find out.


## Project setup

I first decided to focus on how to distribute the closed-source product. I would prefer it to use the same distribution method as the open-source project, so developers don't have to struggle to get it to work. 

Since the core library supports CocoaPods and SPM, I decided to go with SPM for the closed-source too since it's built into Apple’s dev tools and will become the standard as the technology matures.

This part both excited and worried me. Would SPM and Swift be mature enough to support what I was trying to do? I based everything on technologies that hasn’t been out for even two years. Would it work?

Since I host my open-source projects on GitHub, this was a natural place for me to host the closed-source project as well. However, a public repository means public code, which wouldn’t work in this case. 

To keep things private, I therefore had to separate the source code from the distribution. In other words, I had to keep the source code in a private repo, then use a public repo to publish the generated binary.

I thus created a private GitHub repo for the source code, as well as a public repository that contains readmes, FAQs, license information etc., and the SPM package that is used to distribute the framework.

This meant that ended up with three repositories:

* **KeyboardKit** - a public open-source repository
* **KeyboardKitPro** - a public distribution repository
* **KeyboardKitProSource** - a private closed-source repository

With these repositories in place, a developer can start using the core library, then add Pro at any time by pulling in KeyboardKit Pro with SPM. Pro can then be unlocked with a license key.


## Creating a private closed-source repository

The private repository contains an Xcode project that builds a framework for each supported platform. The frameworks are then used to generate an `XCFramework`, which contains all compiled frameworks.

To make a closed-source library support multiple platforms, just follow these steps:

* Create a new Xcode Framework project, e.g. an iOS Framework
* Add some code to the framework target
* Make the framework support all platforms you need, e.g. iOS and macOS

To build a platform-specific archive, run this script (replace MyLibrary with your library name):

```swift
xcodebuild archive \
-scheme <SCHEME_NAME> \
-destination "generic/platform=<PLATFORM>" \
-archivePath build/<LIBRARY_NAME>-<PLATFORM_SUFFIX> \
SKIP_INSTALL=NO \
BUILD_LIBRARY_FOR_DISTRIBUTION=YES
```

Replace `<SCHEME_NAME>` with the name of the scheme, `<LIBRARY_NAME>` with the name of the library, `<PLATFORM>` with either `iOS`, `iOS Simulator`, `OS X`, `tvOS` or `watchOS`, and `<PLATFORM_SUFFIX>` with a unique suffix for each platform. 

After you have built each archive, you can add them to an `XCFramework` with this script, which in this example creates an XCFramework for iOS, iOS Simulator and macOS:

```swift
xcodebuild -create-xcframework \
-framework build/MyLib-iOS.xcarchive/Products/Library/Frameworks/MyLib.framework \
-framework build/MyLib-sim.xcarchive/Products/Library/Frameworks/MyLib.framework \
-framework build/MyLib-macOS.xcarchive/Products/Library/Frameworks/MyLib_macOS.framework \
-output build/MyLib.xcframework
```

I then added this to my `Fastfile`, which lets me run all scripts with a single `fastlane archive` call:

```
platform :ios do

  desc "Create an XCFramework for iOS, iOS Simulator and macOS"
  lane :archive do
    sh('cd .. && rm -rf build')
    sh('cd .. && xcodebuild archive -scheme MyLib-iOS -destination "generic/platform=iOS" -archivePath build/MyLib-iOS SKIP_INSTALL=NO BUILD_LIBRARY_FOR_DISTRIBUTION=YES')
    sh('cd .. && xcodebuild archive -scheme MyLib-iOS -destination "generic/platform=iOS Simulator" -archivePath build/MyLib-sim SKIP_INSTALL=NO BUILD_LIBRARY_FOR_DISTRIBUTION=YES')
    sh('cd .. && xcodebuild archive -scheme MyLib-macOS -destination "generic/platform=OS X" -archivePath build/MyLib-macOS SKIP_INSTALL=NO BUILD_LIBRARY_FOR_DISTRIBUTION=YES')
    sh('cd .. && xcodebuild -create-xcframework -framework build/MyLib-iOS.xcarchive/Products/Library/Frameworks/MyLib.framework -framework build/MyLib-sim.xcarchive/Products/Library/Frameworks/MyLib.framework -framework build/MyLib-macOS.xcarchive/Products/Library/Frameworks/MyLib_macOS.framework -output build/MyLib.xcframework')
  end
end
```

This will remove any previous builds, then build three archives that are added to a single XCFramework.

To distribute this framework with Swift Package Manager, we now need to create a distribution package.


## Creating a public distribution package

To create a distribution package, start by creating a package with `swift package init`. Since we will only have a single binary file as source, you can remove the unit test folder and all content in `Sources`.

In `Package.swift`, list all supported platforms and replace the default targets with a `binaryTarget`:

```
targets: [
    .binaryTarget(
        name: "MyLib",
        path: "Sources/MyLib.xcframework"
    )
]
```

Since we haven't added the framework to `Sources` yet, the target will break and the project not build. Adding the generated framework file to `Sources` should make the package build.

If you like me base a closed-source library on an open-source one, you should add an SPM dependency to ensure that developers will use the correct version of the open-source library:

```
dependencies: [
    .package(url: "https://github.com/danielsaidi/MyLib", .branch("main")),
],
```

This will however not be enforced by SPM, since the binary is already built and a binary target can't have any dependencies. However, I still prefer to specify the dependency like this.

If you push this package, you will now be able to add it to another project. It will be added just like any other open-source package, with the big difference that it pulls in binary code instead of source code.


## App Store Distribution

If you have a single app target to which you add the package, everything will probably work right away. 

However, if your app has extensions and these extensions are the ones that are going to use the closed-source framework, you may run into some frustrating problems when uploading your app to App Store.

First of all, you only have to add a binary package to the app target, not the extension. The extension will still get access to the binary framework, which is different from how open-source packages work.

### Extensions must not contain frameworks

If you get a `The bundle contains disallowed nested bundles` error when submitting your app to the App Store, some extensions may contain binary frameworks, which isn't allowed.

To fix this,make sure to add the framework to the app instead of the extension, then make the extension dynamically refer to the main app's framework:

* Add the closed-source SPM dependency to the main app
* Expand `Swift Package Dependencies` in the Project Navigator
* Expand the SPM dependency's `Referenced Binaries`
* Drag the `XCFramework` file to the app target
* Add the framework to the extension and mark is as `Do Not Embed`
* Add `@executable_path/../../Frameworks` to the extension’s `Runpath Search Paths`

The last step will make the extension look for frameworks outside of its own bundle, which is needed.

With this in place, I tried distributing the app to the App Store once more. It however still failed, this time because the framework was added multiple times. 

### App must not contain duplicate frameworks

After some digging in the generated product, I found that Xcode adds SPM distributed XCFrameworks **twice** to the app bundle - both to Frameworks and to Plugins. 

At least, this happened in the KeyboardKit Pro case. I have done this with other libraries afterwards and haven't faced this problem, so perhaps Xcode has fixed this since.

If you tun into this problem, the solution is to add a build step that removes the framework from the app bundle's plugin folder. Adding this bottommost build script to the main app solved the problem:

```
COUNTER=0
while [ $COUNTER -lt "${SCRIPT_INPUT_FILE_COUNT}" ]; do
    tmp="SCRIPT_INPUT_FILE_$COUNTER"
    FILE=${!tmp}

    echo "Removing $FILE"
    rm -rf "$FILE"
    let COUNTER=COUNTER+1
done
```

You must also provide a pointer to the framework in `Input Files`. For KeyboardKit Pro, this was:

```
$(BUILT_PRODUCTS_DIR)/$(PLUGINS_FOLDER_PATH)/KeyboardKitPro.framework
```

With these fixes in place, the developer experience wasn't as nice as I had hoped, but at least it works. I can hopefully refine this over time, e.g. by embedding the build script into the library.


## Licensing

Before we wrap up, let’s touch on licensing.

I want developers to sign up for a Pro license, then register the license when they launch their app. I want the license engine to be capable of handling multiple parameters, while still keeping it simple. 

I have therefore developed a [license engine]({{licensekit.url}}) that makes it possible to base licenses on these factors:

* Specific features
* Supported locales
* Supported bundles
* Number of developers (e.g. ssh access to the repo)
* Number of users (requires some kind of analytics)

A license can be used for a single app, but more IDs can be added at a discount. Licenses expire after a year, after which they have to be renewed to keep using the product. This doesn't affect shipped apps.

I have decided to start with a Basic, Silver and Gold tier setup, where each tier unlocks all pro features, but a different amount of locales. As more features are added, I will experiment with these parameters.


## Conclusion

I would love to hear your thoughts about this model. Do you see any pros and cons, risks and missed opportunities? At the end of the day, I hope that I have found a good balance between providing a powerful and completely free and open platform with a way for people and companies to upgrade with features that they could build themselves if they'd like, but choose to go the pro route for to save time.

I hope that this text has been an interesting read. I’m excited to release this into the open and see if the model holds up in real life. You can find more information on the [KeyboardKit website]({{project.url}}).

Thanks for reading!