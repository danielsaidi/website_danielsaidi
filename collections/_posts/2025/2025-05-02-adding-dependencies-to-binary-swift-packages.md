---
title:  Adding dependencies to binary Swift packages
date:   2025-05-02 07:00:00 +0000
tags:   general

assets: /assets/blog/25/0502/
image:  /assets/blog/25/0502/image.jpg
header: /assets/blog/25/0502/header.jpg
image-show: 0

emojikit: https://github.com/danielsaidi/emojikit
gesturebutton: https://github.com/danielsaidi/gesturebutton
keyboardkit: https://keyboardkit.com
keyboardkitpro: https://keyboardkit.com/pro
licensekit: https://kankoda.com/sdks/licensekit
vietnameseinput: https://kankoda.com/sdks/vietnameseinput
vietnameseinput-repo: https://github.com/kankoda/vietnameseinput

bsky: https://bsky.app/profile/danielsaidi.bsky.social/post/3lodlyzxwns2i
toot: https://mastodon.social/@danielsaidi/114448990814752516
---

While regular Swift packages can define dependencies, binary packages can't. But there is a way to make the Swift Package Manager fetch & link dependencies for a binary package. Let's find out how.

![An image of a coffee shop counter, selling code.]({{page.header}})


## TLDR;

If you just want to know how to add a dependency to a binary Swift package, and are not interested in the details, this is a quick summary. The rest of the article provides you with more details.

To add a dependency to a binary Swift package, you can add it as a second package target, then add that target to your product's `targets` list. This adds a `LicenseKit` dependency to `VietnameseInput`:

```swift
// swift-tools-version: 6.0

import PackageDescription

let package = Package(
    name: "VietnameseInput",
    platforms: [...],
    products: [
        .library(
            name: "VietnameseInput",
            targets: ["VietnameseInput", "LicenseKit"]
        )
    ],
    targets: [
        .binaryTarget(
            name: "LicenseKit",
            url: "https://github.com/LicenseKit/LicenseKit/.../LicenseKit.zip",
            checksum: "389a58fc8148215a8f8fed06960aa24ddaba3a5b88e73093f60256ddf947cc1d"
        ),
        .binaryTarget(
            name: "VietnameseInput",
            url: "https://github.com/Kankoda/VietnameseInput/.../VietnameseInput.zip",
            checksum: "794fdce20d26376a93d488fec52c06662a88e698970a6faf6794a5d6536a7a7d"
        )
    ]
)
```

The URLs and checksums have been shortened for readability. I have only verified that it works with a dependency that too is a binary package, but it should be doable with a regular package as well.


## Background

I work on several closed-source SDKs, which all ship as binary Swift packages. I have struggled with how to manage dependencies for them, since binary Swift packages can't define dependencies.

Defining a dependency in a regular Swift package is really easy. For instance, this package depends on another package called `EmojiKit`, which is added to the main target:

```swift
// Package.swift
// swift-tools-version: 6.0

import PackageDescription

let package = Package(
    name: "MyPackage",
    platforms: [...],
    products: [
        .library(
            name: "MyPackage",
            targets: ["MyPackage"]
        )
    ],
    dependencies: [
        .package(
            url: "https://github.com/danielsaidi/EmojiKit.git",
            .upToNextMajor(from: "1.6.0")
        )
    ],
    targets: [
        .target(
            name: "MyPackage",
            dependencies: ["EmojiKit"]
       ),
        .testTarget(
            name: "MyPackageTests",
            dependencies: ["MyPackage"]
        )
    ]
)
```

However, if you use a `.binaryTarget` to distribute closed-source, you'll find that that `.binaryTarget` doesn't have a `dependencies` argument.


## How did this affect me?

One of my closed-source SDKs is [KeyboardKit Pro]({{page.keyboardkitpro}}), which is based on the open-source [KeyboardKit]({{page.keyboardkit}}). KeyboardKit is a regular package that can have dependencies, but KeyboardKit Pro is a binary one.

Since binary packages can't define dependencies (that I know of), KeyboardKit Pro can't depend on KeyboardKit. So I have used file sync to add the KeyboardKit source code to KeyboardKit Pro.

But this solution introduces another problem. Since KeyboardKit Pro can't have dependencies, this also stops KeyboardKit from having any, which means that KeyboardKit also needs to use file sync for its dependencies. This becomes messy and makes builds slow.

Another of my products - [LicenseKit]({{page.licensekit}}) - has the same problem, but in a different way. LicenseKit can be used to let apps and SDKs require a commercial license to be used. But while apps can add it as a package dependency, closed-source SDKs that are shipped as binary Swift packages can't.

Since LicenseKit mainly targets closed-source SDKs, this posed a serious problem for my product.



## Why can't binary packages have dependencies?

To understand the problem, compare this binary Swift package with the regular package above:

```swift
// Package.swift
// swift-tools-version: 6.0

import PackageDescription

let package = Package(
    name: "MyPackage",
    platforms: [...],
    products: [
        .library(
            name: "MyPackage",
            targets: ["MyPackage"]
        )
    ],
    targets: [
        .binaryTarget(
            name: "MyPackage",
            url: "https://github.com.../MyPackage.zip",
            checksum: "2c518939b9dc2...3ecb3c1ecbcbaa"
        )
    ]
)
```

We only have a single `.binaryTarget`, and if you check the `.binaryTarget` signature you'll find that it doesn't let you define a dependency. So what to do?


## How have I managed dependencies so far?

To avoid dependencies in my closed-source SDKs, I have used file syncing to sync source files from the library that I want to use.

For instance, since KeyboardKit Pro depends on KeyboardKit but can't specify a dependency, I have used this sync script to file copy KeyboardKit into KeyboardKit Pro:

```shell
#!/bin/bash

# This script syncs the KeyboardKit dependency

# Variables
NAME="KeyboardKit"
NAMEPRO="KeyboardKitPro"
SOURCE="../src/Sources/$NAME"
TARGET="Sources/KeyboardKitPro/_Dependencies/$NAME"
TARGET_DOCC="Sources/KeyboardKitPro/$NAMEPRO.docc"

# Remove
rm -rf "$TARGET"
rm -rf "$TARGET_DOCC/Essentials"
rm -rf "$TARGET_DOCC/Developer"
rm -rf "$TARGET_DOCC/Features"

# Add dependency and remove documentation
cp -r "$SOURCE/" "$TARGET/"
rm -rf "$TARGET/$NAME.docc"

# Add documentation content
cp -r "$SOURCE/$NAME.docc/Essentials" $TARGET_DOCC
cp -r "$SOURCE/$NAME.docc/Developer" $TARGET_DOCC
cp -r "$SOURCE/$NAME.docc/Features" $TARGET_DOCC
cp -r "$SOURCE/$NAME.docc/Resources" $TARGET_DOCC
cp Resources/Logo.png "$TARGET_DOCC/Resources"

# Remove other things not used in this library
rm -rf "$TARGET/_Pro"
rm -rf "$TARGET/_Keyboard/KeyboardInputViewController+SetupCore.swift"
rm -rf "$TARGET/App/KeyboardAppView+LicenseRegistrationView.swift"
rm -rf "$TARGET/Bundle/Bundle+KeyboardKit.swift"
rm -rf "$TARGET/Emojis/Emoji+KeyboardWrapper.swift"

# Commit the changes
git add .
git commit -am "Update $NAME"
```

This will delete any old files, then sync everything it needs from KeyboardKit that it shall copy. The result is an (almost) complete copy of KeyboardKit:

![A screenshot of the copy result]({{page.assets}}sync-script.jpg)

In this case, this is actually what I want, since KeyboardKit Pro is just meant to extend KeyboardKit with more features. But it leads to some tricky challenges.

For instance, KeyboardKit depends on my [GestureButton]({{page.gesturebutton}}) library for its keyboard button gestures. And while KeyboardKit could have pulled it in as a proper Swift package dependency, the fact that KeyboardKit is then copied into KeyboardKit Pro in its turn, makes this impossible. 

This has made me used the file sync approach even in KeyboardKit. It works ok, but isn't as clean as proper Swift package dependencies. It also makes build times slower than they need to be.

All in all, KeyboardKit depends on [GestureButton]({{page.gesturebutton}}) and [EmojiKit]({{page.emojikit}}), while KeyboardKit Pro depends on KeyboardKit and [LicenseKit]({{page.licensekit}}). It's a controlled and stable mess, but has worked for many years now.


## Finding a workaround

When I created my latest closed-source SDK - [VietnameseInput]({{page.vietnameseinput}}) - which will be used by KeyboardKit Pro to enable Vietnamese typing, I faced new dependency-related challenges.

While VietnameseInput is used by KeyboardKit Pro, it's a standalone product that also [LicenseKit]({{page.licensekit}}) to handle licenses. I understand that this may be confusing, so I'll try to sum up why:

* KeyboardKit Pro "depends" on LicenseKit by syncing files.
* By syncing files, LicenseKit's internal code become available.
* KeyboardKit Pro has custom license code that uses this internal code.
* When creating this new Vietnamese SDK, I wanted it to use only public license tools.

So I wanted to find a way to make at least VietnameseInput depend on LicenseKit in a way that will preserve LicenseKit's access scope and intended system design.

To do this, I had to find a way to make VietnameseInput pull in LicenseKit as a proper dependency.

My closed-source SDKs have a **private** source code repository, and a **public** distribution repository. The private repository has a regular Swift package, while the public repository has a binary package.

So, while the public VietnameseInput repo has a binary package, the private source code repository has a regular package. So I started with adding the [LicenseKit]({{page.licensekit}}) dependency there:

```swift
// swift-tools-version: 6.0

import PackageDescription

let package = Package(
    name: "VietnameseInput",
    platforms: [...],
    products: [
        .library(
            name: "VietnameseInput",
            targets: ["VietnameseInput"]
        )
    ],
    dependencies: [
        .package(
            url: "https://github.com/LicenseKit/LicenseKit", 
            .upToNextMajor(from: "1.2.4")
        )
    ],
    targets: [
        .target(
            name: "VietnameseInput",
            dependencies: ["LicenseKit"]
        ),
        .testTarget(
            name: "VietnameseInputTests",
            dependencies: ["VietnameseInput"]
        )
    ]
)
```

This worked great! I was able to add proper commercial license support to VietnameseInput with very little effort, using both Gumroad integrations, encrypted license files and source code licenses.

I then used the private source code to build an XCFramework, and uploaded it to a pre-release on the [VietnameseInput]({{page.vietnameseinput-repo}}) GitHub repository. The URL of this upload will then be added to the public, binary package, together with its computed checksum.

The big question remains: Will the public binary package be able to define a LicenseKit dependency?


## The result

It was time to see if the public binary package could pull in LicenseKit as a proper dependency, even though binary targets can't have dependencies.

This is how the binary VietnameseInput package looked before adding the dependency:

```swift
// swift-tools-version: 6.0

import PackageDescription

let package = Package(
    name: "VietnameseInput",
    platforms: [...],
    products: [
        .library(
            name: "VietnameseInput",
            targets: ["VietnameseInput"]
        )
    ],
    targets: [
        .binaryTarget(
            name: "VietnameseInput",
            url: "https://github.com/Kankoda/VietnameseInput/.../VietnameseInput.zip",
            checksum: "794fdce20d26376a93d488fec52c06662a88e698970a6faf6794a5d6536a7a7d"
        )
    ]
)
```

My idea was that if the package could define multiple targets and include them all in the `.library` product, perhaps dynamic linking could take care of the rest?

So, I tried adding a second target and adding it to the library:

```swift
// swift-tools-version: 6.0

import PackageDescription

let package = Package(
    name: "VietnameseInput",
    platforms: [...],
    products: [
        .library(
            name: "VietnameseInput",
            targets: ["VietnameseInput", "LicenseKit"]
        )
    ],
    targets: [
        .binaryTarget(
            name: "LicenseKit",
            url: "https://github.com/LicenseKit/LicenseKit/.../LicenseKit.zip",
            checksum: "389a58fc8148215a8f8fed06960aa24ddaba3a5b88e73093f60256ddf947cc1d"
        ),
        .binaryTarget(
            name: "VietnameseInput",
            url: "https://github.com/Kankoda/VietnameseInput/.../VietnameseInput.zip",
            checksum: "794fdce20d26376a93d488fec52c06662a88e698970a6faf6794a5d6536a7a7d"
        )
    ]
)
```

This actually worked! The public package compiled with no problems, after which I created a proper release and published it to the  [VietnameseInput]({{page.vietnameseinput-repo}}) GitHub repository.

I then created a test app and pulled in the VietnameseInput version. It also worked! The app fetched both VietnameseInput and LicenseKit, and did allow me to register its VietnameseInput license key.


## Conclusion

I'm not sure how this exactly works, but guess that dynamic linking lets VietnameseInput locate and use LicenseKit due to the app's library search paths. If you know more, I'd love to hear about it.

This means that I can start using proper dependencies for my closed-source SDKs. This will improve build times and also remove external dependency types from the documentation.


## Disclaimer

To be clear, I have so far only made this work with a binary Swift package. I'm not sure how I would go about to add a regular `.target` that points to a regular package. If you do, please share.

I have also just tested this in the test app that I mentioned. I can't see why this shouldn't work when publishing the app to the App Store, but maybe there are additional steps required.

I have looked all over for good documentation from Apple or the community, but have had to resort to my own experiments. I'd love to discuss this with anyone who have strugged with this too.