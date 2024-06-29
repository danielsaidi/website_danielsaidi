---
title:  Distributing closed-source frameworks with SPM
date:   2021-02-15 07:00:00 +0100
tags:   swift spm xcode open-source closed-source
icon:   swift
---


In this post, we'll look at how to distribute closed-source products with the Swift Package Manager (SPM). We'll go through project setup, package distribution and some problems.

{% include kankoda/data/product.html name="LicenseKit" %}{% assign licensekit=project %}
{% include kankoda/data/open-source.html name="KeyboardKit" %}


## Background

I have an open-source project called [KeyboardKit]({{project.github}}), which is open-source. As I later wanted to explore monetization, I created a commercially licensed, closed-source extension called [KeyboardKit Pro]({{project.github_pro}}), that unlocks more locales and pro features.

In this post, let's take a look at some technical challenges with having two versions of the same project. It it possible to combine open-source with license-based, closed-source?


## Project setup

One requirement for the closed-source project was to use the same distribution method as the open-source one. Since it supports both CocoaPods & SPM, I decided to go with SPM for the closed-source, since it will become the standard tool as the technology matures.

This both excited and worried me. I was excited to try distributing binary dependencies with SPM, but would it be mature enough to support it? I based everything on a tool that hasn’t been out for even two years. Would it work?

Since I host the open-source project on GitHub, this was a natural place for me to host the closed-source version too. However, a public repository means public code, which wouldn’t work in this case. I had to separate the source code from the distribution somehow.

I therefore created a private GitHub repository for the source code and a public one for the versioned binary distribution, the readmes, the web documentation, etc.

This meant that I now have three repositories for KeyboardKit:

* **KeyboardKit** - a public repository for the open source code
* **KeyboardKitPro** - a public repository for the binary pro release
* **KeyboardKitProSource** - a private repository for the closed source code

With this setup, a developer can start using the open-source library, then upgrade to Pro by changing the repo URL and replacing `import KeyboardKit` with `import KeyboardKitPro`.


## Creating a private closed-source repository

The private repository contains a Swift Package as well as an Xcode project that builds the package into a framework for each platform. The frameworks are then used to generate an `XCFramework`, which contains all compiled frameworks.

To make a closed-source library support multiple platforms, just follow these steps:

* Create a new Xcode Framework project.
* Add some code to the framework target.
* Make the framework support all platforms you need, e.g. iOS & macOS.

To build a platform-specific archive, just run this script with the variables replaced:

```swift
xcodebuild archive \
-scheme <SCHEME_NAME> \
-destination "generic/platform=<PLATFORM>" \
-archivePath build/<LIBRARY_NAME>-<PLATFORM_SUFFIX> \
SKIP_INSTALL=NO \
BUILD_LIBRARY_FOR_DISTRIBUTION=YES
```

Replace `<SCHEME_NAME>` with the name of the scheme, `<LIBRARY_NAME>` with the name of the library, `<PLATFORM>` with either `iOS`, `iOS Simulator`, `OS X`, `tvOS`, `watchOS`, or `xrOS` and `<PLATFORM_SUFFIX>` with a unique suffix for each platform. 

After you have built each archive, you can add them to an `XCFramework` with `xcodebuild -create-xcframework`. Here, we create an XCFramework for iOS, iOS Simulator & macOS:

```swift
xcodebuild -create-xcframework \
-framework build/MyLib-iOS.xcarchive/Products/Library/Frameworks/MyLib.framework \
-framework build/MyLib-sim.xcarchive/Products/Library/Frameworks/MyLib.framework \
-framework build/MyLib-macOS.xcarchive/Products/Library/Frameworks/MyLib_macOS.framework \
-output build/MyLib.xcframework
```

I then added this to my `Fastfile` file, which lets me run all scripts with `fastlane archive`:

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

This will remove any previous builds, then build three separate archives that are combined into an XCFramework. This script has evolved to handle multi-platform builds even better, with separate lanes for each platform.

To distribute this with Swift Package Manager, we need to create a distribution package.


## Creating a public distribution package

To create a distribution package, we start by creating a package with `swift package init`. Since we will only have a single binary, you can remove the unit test folder and all content in `Sources`.

In `Package.swift`, add all supported platforms and replace `targets` with a `binaryTarget`:

```
targets: [
    .binaryTarget(
        name: "MyLib",
        path: "Sources/MyLib.xcframework"
    )
]
```

Since we haven't added the framework to `Sources` yet, the target will break and the project not build. Add the generated framework file to `Sources` to make the package build.

My first version of the closed-source project depended on the open-source project, which meant that I also had to define the dependency in the distributoion package file:

```
dependencies: [
    .package(url: "https://github.com/danielsaidi/MyLib", .branch("main")),
],
```

This will however not be enforced by SPM, since the binary framework is already built and a binary target can't have dependencies. The current version of KeyboardKitPro therefore actually inline copies the open-source version, to avoid having external dependencies.

You will now be able to add it to another project. It will be added just like any other open-source package, with the big difference that it pulls in binary code instead of source code.


## App Store Distribution

If you add the binary package to your app, everything should just work. However, if the app has extensions and these extensions also use the package, you may face some frustrating problems when uploading your app to App Store.

Unlike open-source packages, binary packages must only be added to the app target, not its extensions. An extension will still get access to the binary framework, which is different from how open-source packages work.

So the first thing to check is that you only add the binary package to your main app target. Here are some other problems (which may be fixed in later versions of Xcode and SPM).

### Extensions must not contain frameworks

If you get a `The bundle contains disallowed nested bundles` error when you submit an app to the App Store, some extension may contain binary frameworks, which isn't allowed.

To fix this, make sure to add the framework to the app instead of the extension, and make the extension dynamically refer to the main app's framework:

1. Add the closed-source SPM dependency to the main app target.
1. Expand `Swift Package Dependencies` in the Xcode Project Navigator.
1. Expand the SPM dependency's `Referenced Binaries` section.
1. Drag the `XCFramework` file to the app target.
1. Add the framework to the extension and mark is as `Do Not Embed`.
1. Add `@executable_path/../../Frameworks` to the extension’s `Runpath Search Paths`.

The last step will make the extension look for frameworks outside of its own bundle, which is required for the app to pass the automated review check.

With these changes, you can still run into problems. For instance, my app upload still failed because a framework was added multiple times. 

### App must not contain duplicate frameworks

After some digging around in the generated build, I found that Xcode adds SPM distributed XCFrameworks **twice** to the app bundle - both to Frameworks and to Plugins.

If you run into this, the solution is to add a build step that removes the framework from the app bundle's Plugin folder. Adding this build script to the main app solved the problem:

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

You must also provide a pointer to the framework in `Input Files`. For my case, this was:

```
$(BUILT_PRODUCTS_DIR)/$(PLUGINS_FOLDER_PATH)/KeyboardKitPro.framework
```

This may however have been fixed in later versions of Xcode, since I no longer run into the problem. But if it starts happening again, perhaps the same thing can fix it.


## Licensing

Before we wrap up, let’s touch on licensing.

I want developers to sign up for a Pro license, then register their license key to start using the Pro package. I want the license engine to be capable of handling multiple parameters.

I therefore created a [license engine]({{licensekit.url}}) that makes it possible to define different properties, constraints and configurations to a license, such as:

* Supported locales
* Supported features
* Supported bundle IDs
* Number of developers (ssh access to the repo)
* Number of users (requires some kind of analytics)

A license can be used for a single app, but more IDs can be added at a discount. Licenses expire after a year, after which they have to be renewed to keep using the product.

I decided to start with a Basic, Silver & Gold tier setup, where each tier unlocks a different set of features and locales. As more features are added, I will experiment with this more.


## Conclusion

I hope that I have found a good balance between providing a powerful and completely free and open platform with a way for people and companies to upgrade with features that they could build themselves, but choose to go the pro route for to save time.

I hope that this has been an interesting read. I’m excited to release this and to see if the model holds up in real life. You can find more information on the [KeyboardKit website]({{project.url}}).