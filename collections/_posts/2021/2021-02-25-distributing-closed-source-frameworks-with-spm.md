---
title:  Distributing closed-source frameworks with SPM
date:   2021-02-15 07:00:00 +0100
tags:   swift spm xcode open-source closed-source
icon:   swift

keyboardkit: https://github.com/danielsaidi/KeyboardKit
keyboardkitpro: https://github.com/danielsaidi/KeyboardKitPro
website: https://getkeyboardkit.com
---


In this post, we'll look at how to distribute closed-source XCFrameworks-based products with the Swift Package Manager (SPM). The post goes through project setup, package distribution and how to solve some problems along the way.


## Background

I have an open-source project called [KeyboardKit]({{page.keyboardkit}}), that can be used to build custom keyboards for iOS and iPad. Although it's open-source and completely free, it's currently my main source of income, with several exciting freelance projects.

As part of releasing KeyboardKit 4.0, I wanted to try other monetization models than sponsorships and hour-based consultation. I have therefore created a closed-source, license-based extension that I call [KeyboardKit Pro]({{page.keyboardkitpro}}), which unlocks more locales and pro features that saves developers a lot of time. 

[KeyboardKit Pro]({{page.keyboardkitpro}}) uses the same extension points as are available to everyone else, which means that anyone can implement the same features instead of using Pro (the demo app even shows you how). KeyboardKit Pro is thus just a convenience to save time, and not something you have to buy to extend the core library. This was a very important principle for me.

With this said, I'm not going to make this into a sales pitch, but rather focus on the tech challenges that excited me with this project. Was it possible to combine an open-source library with a license-based, closed-source extension that depends on the core library.

Let's find out.


## Project setup

I decided to first focus on how to distribute the closed-source package. If possible, I wanted the core library and the closed-source extension to use the same distribution method, so that developers wouldn’t have to jump through hoops to get it to work. 

Since the core library supports CocoaPods and SPM, I decided to go with SPM for the closed-source extension. SPM is built into Apple’s dev tools and will become the standard as the technology matures. Developers can still use CocoaPods for the core library, but the closed-source extension requires SPM.

This part both excited and worried me. Would SPM and Swift be mature enough to support what I was trying to do? I based everything on technologies that hasn’t been out for even two years, with new core features being released with each new Swift version. Would it work?

Since I host my open-source projects on GitHub, this was a natural place for me to host the closed-source project as well. However, a public repository means public code, which wouldn’t work in this case. To keep the library closed-source, I realized that I had to keep the source code separated from the distribution package. In GitHub words, this meant that I had to keep the source code in a private repo, then compile it and add the compiled framework to a public repo that would be used for distribution.

I thus created a private GitHub repo that contains source code that I compile into an `XCFramework`, which is a way to bundle binaries for a single or multiple platform into a single package. We'll look at how to build this framework later. I then created a public repo for the compiled product, together with readmes, FAQs, license information etc. The public repo also contains an SPM package that is used to distribute the framework.

This meant that ended up with three repositories:

* **KeyboardKit** - a public open-source repository
* **KeyboardKitPro** - a public distribution repository
* **KeyboardKitProSource** - a private closed-source repository

With these repositories in place, the idea was that developers could start using the core library, then add Pro at any time, by pulling in KeyboardKit Pro with SPM. Pro can then be unlocked with a license key.

Let's look at how the private repository is setup.


## Private closed-source repository

The private, closed-source repository will not be a Swift Package, but rather contain an Xcode project with a Framework target for each supported platform. The frameworks are then built and added to an `XCFramework`, which contains all compiled frameworks.

If your closed-source library should support multiple platforms, you can follow these steps to create a multi-platform project:

* Create a new Xcode Framework project, e.g. an iOS Framework
* Add some code to the framework target
* Make the framework support all platforms you need, e.g. iOS and macOS

To build a platform-specific archive, run this script (MyLibrary should be replaced with your library name):

```swift
xcodebuild archive \
-scheme SCHEME_NAME \
-destination "generic/platform=PLATFORM" \
-archivePath build/LIBRARY_NAME-PLATFORM_SUFFIX \
SKIP_INSTALL=NO \
BUILD_LIBRARY_FOR_DISTRIBUTION=YES
```

In the script above, `SCHEME_NAME` is a placeholder for the name of the scheme, which is unique for each platform target. Valid `PLATFORM` names are `iOS`, `iOS Simulator`, `OS X` and (I guess...haven't tried yet) `tvOS` and `watchOS`. `LIBRARY_NAME` is a placeholder for the name of the library and `PLATFORM_SUFFIX` should be the name of the platform.

After you have built each archive, you can add them to an XCFramework with this script, which in this example creates an XCFramework for iOS, iOS Simulator and macOS:

```swift
xcodebuild -create-xcframework \
-framework build/MyLib-iOS.xcarchive/Products/Library/Frameworks/MyLib.framework \
-framework build/MyLib-sim.xcarchive/Products/Library/Frameworks/MyLib.framework \
-framework build/MyLib-macOS.xcarchive/Products/Library/Frameworks/MyLib_macOS.framework \
-output build/MyLib.xcframework
```

For convenience, I have added these steps to the project's `Fastfile` and created a lane that lets me run all scripts in one single `fastlane archive` call in the terminal:

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

Running `fastlane archive` from the project root will remove any previous builds, then build three archives that are then added to a single XCFramework.

We are now ready to distribute this XCFramework file with Swift Package Manager. We just need to create a distribution package first.


## Public distribution package

Start by creating a new package, using `swift package init`. You can remove the unit test folder and all content in `Sources`, since we will only have a single binary file as source.

In `Package.swift`, list all supported platforms and replace the default targets with a `binaryTarget`:

```
targets: [
    .binaryTarget(
        name: "MyLib",
        path: "Sources/MyLib.xcframework")
]
```

You will notice that the autogenerated target will break and the project not build. Solve this by adding the generated framework file to `Sources`. This should make the package valid.

If you like me base a closed-source library on an open-source one, you should add an SPM dependency to ensure that developers will use the correct version of the open-source library:

```
dependencies: [
    .package(url: "https://github.com/danielsaidi/MyLib", .branch("main")),
],
```

However, this will never be enforced by SPM, since the binary is already built and the binary target can have no dependencies. However, I still prefer to specify the dependency like this.

If you push your changes, you will now be able to add a reference to this public package. It will be added to just like an open-source library, with the big difference that it pulls in a binary instead of source code.


## App Store Distribution

If you just have a single app target to which you add framework packages like above, everything will probably work right away. 

However, if your app has extensions and these extensions are the ones that are going to use the closed-source framework, you may run into some frustrating problems when uploading your app to App Store.

For KeyboardKit Pro, you just have to add the dependency to the app target instead of the extension. The extension will still get access to the package, since it will look in the app as well. This is different from how open-source packages work.

If this doesn't work in your case, you can do some project configurations to make binary packages work with Xcode and App Store distribution.


### Extensions must not contain frameworks

If App Store validation process fails with a `The bundle contains disallowed nested bundles` error, your extension contains binary frameworks, which isn't allowed.

I eventually found a way to fix this, by adding the framework to the app instead of the extension, then make the extension dynamically refer to the main app's framework. This involved the following steps:

* Add the closed-source SPM dependency to the main app
* Expand `Swift Package Dependencies` in the Project Navigator
* Expand the SPM dependency's `Referenced Binaries`
* Drag the `XCFramework` file to the app target
* Add the framework to the extension and mark is as `Do Not Embed`
* Add `@executable_path/../../Frameworks` to the extension’s `Runpath Search Paths`

The last bullet will make the extension search for the framework outside of its own bundle. After writing this, I also thing that Xcode actually does this by default for all new projects.

With all this in place, I tried distributing the app again. The error was now gone, but distribution still failed. This time, the App Store validation complained that the framework was added multiple times. 


### App must not contain duplicate frameworks

After some digging in the generated product, I found that Xcode adds SPM distributed XCFrameworks **twice** to the app bundle - both to Frameworks and to Plugins. 

At least, this happened in the KeyboardKit Pro case. I have done this with other libraries afterwards and haven't faced this problem, so perhaps Xcode has fixed this since.

However, *if* you tun into this problem, the solution is to add a build step that removes the framework from the app bundle's plugin folder. Adding this bottommost build script to the main app solved the problem:

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

With these fixes in place, the developer experience of adding KeyboardKit Pro wasn't as nice as I had hoped, but at least it works. 

I can hopefully refine this setup over time, e.g. by embedding the build script into the library and make it move the framework from the extension to the main app.


## Licensing

Before we wrap up, let’s touch on licensing.

I want developers to sign up for a Pro license, then register the license when they launch their app. I want the license engine to be capable of handling multiple parameters, while still keeping it simple. 

I have therefore developed a license engine that makes it possible to base licenses on these factors:

* Specific features
* Supported locales
* Supported bundles
* Number of developers (e.g. ssh access to the repo)
* Number of users (requires some kind of analytics)

A license can be used for a single app bundle ID, but more IDs can be added at a discount. Licenses expire after a year, after which they have to be renewed. Developers don't have to upgrade the license if they don't want to. They can stay on the last version their license supports, but have to renew to get access to new features. License expiration will never affect the end-used or shipped apps. This means that apps will not stop working when a license expires.

I have decided to start with a Basic, Medium and Full tier setup, where each tier unlocks all pro features, but a different amount of locales. As more features are added, I can play around with these parameters more. I'm excited about developing the license model even more.


## Conclusion

I would love to hear your thoughts about this model. Do you see any pros and cons, risks and missed opportunities? At the end of the day, I hope that I have found a good balance between providing a powerful and completely free and open platform with a way for people and companies to upgrade with features that they could build themselves if they'd like, but choose to go the pro route for to save time, get new features without additional cost or work etc. To me, it feels like a good and fair model.

I hope that this text has been an interesting read. I’m excited to release this into the open and see if the model holds up in real life. You can find more information on the [KeyboardKit website]({{page.keyboardkit}}).

Thanks for reading!