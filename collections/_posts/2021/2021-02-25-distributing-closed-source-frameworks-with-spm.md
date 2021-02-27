---
title:  Distributing closed-source frameworks with SPM
date:   2021-02-15 07:00:00 +0100
tags:   swift xcode spm

icon:   keyboardkit

keyboardkit: https://github.com/danielsaidi/KeyboardKit
keyboardkitpro: https://github.com/danielsaidi/KeyboardKitPro
---


In this post, we'll look at how to distribute closed-source XCFrameworks-based products with the Swift Package Manager. The information is based on a real product that I just launched and goes through project setup, product distribution and how to solve some frustrating problems along the way.


## Background

I have an open-source project called [KeyboardKit]({{page.keyboardkit}}), that can be used to build custom software keyboards for iOS and iPad. Although it's open-source and completely free, it's currently my main source of income as a freelancer, with several exciting client projects.

As part of releasing KeyboardKit 4.0 a few days ago, I wanted to experiment with other monetization models than sponsorships and hour-based consultation. I’m super happy about hour-based project work as well, but think well-defined pricing and packaging would be a great addition.

I have therefore created a closed source, license-based extension to KeyboardKit that I call [KeyboardKit Pro]({{page.keyboardkitpro}}). It unlocks pro features, such as more locales, input sets, layouts and secondary actions, which saves developers a lot of time. It uses the exact same extension points as are available to everyone, which means that anyone can implement the same features if they'd want (the demo app even shows you how). KeyboardKit Pro is thus just a convenience that you can buy to save time, and not something you have to buy to extend the core library. This was a very important core principle for me.

With this said, I'm not going to make this into a sales pitch, but rather focus on the tech challenges that excited me with developing KeyboardKit Pro. 

Since KeyboardKit is open source and distributed via dependency managers like CocoaPods and Swift Package Manager (SPM), my biggest goal with this undertaking was to verify that I could combine the open-source core library with a license-based, closed-source pro plugin that depends on the core library. Could this be done?

Let's find out.


## Project setup

I started reading up on this and decided to first focus on how I wanted the closed-source package to be distributed. If possible, I wanted both the core library and the extension to use the same distribution method, so that developers wouldn’t have to jump through hoops to get it to work. 

Since the core library supports CocoaPods and SPM, I decided to base the pro distribution around SPM. It’s built into Apple’s dev tools and will become a de facto standard as time moves on and the technology matures. Developers can still use CocoaPods for KeyboardKit, but KeyboardKit Pro requires SPM.

This part both excited and worried me. Would SPM and Swift be mature enough to actually support what I was trying to do? I based everything on technologies that hasn’t been out for even two years, with new core features being released with each new Swift version. Would it work?

Since I host my open-source projects on GitHub, this was a natural place for me to host KeyboardKit Pro as well. However, public repositories mean public source code, which wouldn’t work in this case. To keep KeyboardKit Pro closed-source, I realized that I had to keep the source code separated from the distributable product. In GitHub words, this meant that I had to keep the source code in a private repo, then compile it and add the built product to a public repo that would be used for distribution.

I thus created a private repo that contains source code that I compile into an `XCFramework`, which is a way to bundle binaries for multiple platform into one single package. I then created a second repo with the public parts of the product including readmes, FAQs, license information and the compiled product. The public repo also contains an SPM package that is used to distribute the framework.

This meant that I would have three repositories:

* **KeyboardKit** - a public open-source repository
* **KeyboardKitPro** - a public distribution repository
* **KeyboardKitProSource** - a private closed-source repository

With these repositories in place, the idea was that developers could start using KeyboardKit, then extend their product with pro features by pulling in KeyboardKitPro in the same way, using SPM. Pro features should then be unlocked with a license key, preferably with a single line of code.

I tried this and verified that it worked. SPM took care of distributing and injecting the XCFramework into the target project without any problems. This would not have been possible two years ago.

I could now run KeyboardKit and KeyboardKit Pro on simulator and on real devices, but for the setup to be viable, I had one critical test remaining: Could I distribute KeyboardKit Pro-based apps to the App Store?

I tried this...and failed. 


## App Store Distribution

After adding KeboardKit Pro to the keyboard extension, the App Store validation started complaining that `The bundle contains disallowed nested bundles`. Turns out that app extensions must not contain frameworks. And how do we distribute KeyboardKit Pro? That’s right - as a framework!

This was a serious blow to my idea of how I wanted to distribute KeyboardKit Pro. If closed-source required distributing products as frameworks instead of source code, and extensions mustn’t contain frameworks, was I doomed to fail?

I started Googling, reading the same few articles over and over, until I found a way to fix this - by adding the framework to the main app instead of the extension, then have the extension dynamically refer to the main app's framework instead.

This involved the following steps:

* Add a KeyboardKit Pro SPM dependency to the main app
* Expand the SPM dependency and drag the XCFramework file to the project
* Add this framework to the keyboard extension and mark is as `Do Not Embed`
* Add `@executable_path/../../Frameworks` to the extension’s `Runpath Search Paths`.

With all this in place, I tried again...and failed. This time, the App Store validation process started complaining about the framework being added multiple times.

After some investigations, I found that Xcode adds SPM distributed XCFrameworks **twice** to the app bundle - both to Frameworks and to Plugins. The solution should thus be to add a build step that removes the plugin folder from the app bundle.

Luckily, other people had stumbled into this problem as well and provided me with a build script to solve this problem. Adding this bottommost build script to the main app solved the problem (using `$(BUILT_PRODUCTS_DIR)/$(PLUGINS_FOLDER_PATH)/KeyboardKitPro.framework` as input file):

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

With these fixes in place, the developer experience of adding KeyboardKit Pro isn't as nice as I had hoped, but at least it works. I can hopefully refine this setup over time, e.g. by embedding the build script into the library and make it move the framework from the extension to the main app.


## Developer Setup

KeyboardKit is setup with a single `setup(with:)` call that provides KeyboardKit with a view that they want to use as the main keyboard view. I wanted the pro setup to be as easy at this.

After some work, I came up with an extension to the already existing setup, where developers can call `setupPro(withLicenseKey:view:)` or `setupPro(withLicenseKey:)` to unlock and register pro features. This means that pro setup replaces the previous setup call and takes care of everything with a single line of code.

The pro setup will validate the provided license key and show a warning and crash the app if the license is invalid, or throw an assertion error if it’s outdated. This will not affect any apps already in production, which means that once an app is out, the license can’t expire for that particular device.

If the license is valid, the pro setup registers the license, which is required for all pro features, then unlocks and injects pro features. Most pro features will be immediately available without developers having to do anything. For instance, a multi-locale keyboard will get access to all unlocked locales.


## Licensing

Before wrapping up, let’s touch on licensing, which is something that I am still working on.

I want the license model to be capable of handling multiple parameters, while still keeping it simple. I will therefore make it possible to base a license on these factors, but will not use all of them for this particular product:

* Specific features
* Number of unlocked locales
* Number of supported app bundles
* Number of developers (e.g. ssh access to the repo)
* Number of users (requires some kind of analytics)

I have decided to start with a Basic, Medium and Full tier setup, where each tier unlocks all pro features, but a different amount of locales. As more features are added, I can play around with these parameters even more.

A license can be used for a single app bundle ID. More IDs can be added to the license at a discounted rate. Here, I'm thinking 50% of the original price for each new bundle.

Licenses expire after a year, after which they have to be renewed for new releases after this date. This means that developers don't have to upgrade the license if they don't want to. They can just stay on the last version their license supports, but will miss out on new features.

License expiration will never affect the end-used or shipped apps. This means that KeyboardKit-based apps will not stop working when a license expires.

I am excited about developing the license model even more.


## Conclusion

I hope that this text has been an interesting read. I’m excited to release KeyboardKit Pro into the open and see if the model holds up in real life. I'm also developing a public web site and more stuff that I hope to release soon.

You can find more information about KeyboardKit and KeyboardKit Pro at the [open source website]({{page.keyboardkit}}) and the [pro product page]({{page.keyboardkitpro}}).

Thanks for reading!