---
title:  Making Swift package assets work in external SwiftUI previews
date:   2022-06-02 10:00:00 +0000
tags:   swiftui swift spm swiftgen

icon:   swiftui

article1:   https://developer.apple.com/forums/thread/664295
article2:   https://dev.jeremygale.com/swiftui-how-to-use-custom-fonts-and-images-in-a-swift-package-cl0k9bv52013h6bnvhw76alid
swiftgen:   https://github.com/SwiftGen/SwiftGen
---


In this post, we'll take a look at how we can get colors, images and other assets that are defined in Swift packages to work in external SwiftUI previews, such as in an app project.


## Background

Swift packages make it very easy to share assets, such as colors and images, as well as files, such as fonts. Just add resources to a package folder and specify the folder in the package definition file:

```swift
// swift-tools-version: 5.6

import PackageDescription

let package = Package(
    name: "MyPackage",
    products: [
        .library(
            name: "MyPackage",
            targets: ["MyPackage"])
    ],
    dependencies: [],
    targets: [
        .target(
            name: "MyPackage",
            dependencies: [],
            resources: [.process("Resources")]), // <-- Define the folder here
        .testTarget(
            name: "MyPackageTests",
            dependencies: ["MyPackage"])
    ]
)
```

When you define resources, the package will generate a `.module` bundle that you can use to access any embedded assets and resources. Tools like [SwiftGen]({{page.swiftgen}}) also use this bundle to access resources.

However, while this works great within the package itself, such as in package previews, Swift will not be able to use the `.module` bundle in external previews, due to its lacking capabilities to locate the bundle while in preview mode. If another package or an app uses the package, any previews that refer to the package assets in any way will crash.

This is most likely a bug, rather than the intended behavior, and is discussed in great detail [here]({{page.article1}}) and [here]({{page.article2}}). The proposed and currently working solution is to create a custom bundle extension, that adds some missing ways to resolve the bundle when it's being used in a preview.


## Creating a custom bundle

Let's solve this problem by defining a custom package bundle. First, extend `Bundle` with a class that we can use to find the package:

```swift 
extension Bundle {

    private class BundleFinder {}
}
```

We then have to define the name of the package. This could change in every new version of Xcode, so make sure to test it whenever a new version of Xcode is released.


```swift 
extension Bundle {

    static let myPackageBundleName = "MyPackage_MyPackage"
}
```

The name convention used to be `LocalPackages_<ModuleName>` for iOS, but it may change at any time. If it stops working, you can print out the path like this and look for the bundle name in the print:

```swift
Bundle(for: BundleFinder.self)
    .resourceURL?
    .deletingLastPathComponent()
    .deletingLastPathComponent()
```

Also note that the name pattern above may be different for macOS.

We can now define a custom bundle, which will look for the package bundle in more places than the generated `.module` bundle:

```swift
public static let myPackage: Bundle = {
    let bundleNameIOS = myPackageBundleName
    let candidates = [
        // Bundle should be here when the package is linked into an App.
        Bundle.main.resourceURL,
        // Bundle should be here when the package is linked into a framework.
        Bundle(for: BundleFinder.self).resourceURL,
        // For command-line tools.
        Bundle.main.bundleURL,
        // Bundle should be here when running previews from a different package
        // (this is the path to "…/Debug-iphonesimulator/").
        Bundle(for: BundleFinder.self)
            .resourceURL?
            .deletingLastPathComponent()
            .deletingLastPathComponent()
            .deletingLastPathComponent(),
        Bundle(for: BundleFinder.self)
            .resourceURL?
            .deletingLastPathComponent()
            .deletingLastPathComponent(),
    ]

    for candidate in candidates {
        let bundlePathiOS = candidate?.appendingPathComponent(bundleNameIOS + ".bundle")
        if let bundle = bundlePathiOS.flatMap(Bundle.init(url:)) {
            return bundle
        }
    }
    fatalError("Can't find myPackage custom bundle.")
}()
```

That's it! If you now use the `.myPackage` bundle instead of `.module`, external previews should work as well. If they don't, you can use the techniques above to find out why they don't.


## Using our custom bundle in SwiftGen

If you use [SwiftGen]({{page.swiftgen}}) to generate code from your assets and resource files, you should tell it to use the custom bundle as well, by adjusting the `swiftgen.yml` file so that it defines a `bundle`:

```
// swiftgen.yml

fonts:
  inputs:
    - Sources/MyPackage/Resources/Fonts
  outputs:
    - templateName: swift5
      output: Sources/MyPackage/Fonts/Fonts.swift
      params:
        bundle: Bundle.myPackage
        fontAliasName: SwiftGenFont

xcassets:
  inputs:
    - Sources/MyPackage/Resources/Colors.xcassets
    - Sources/MyPackage/Resources/Images.xcassets
  outputs:
    - templateName: swift5
      output: Sources/MyPackage/Assets/Assets.swift
      params:
        bundle: Bundle.myPackage
```

If you now run `swiftgen` from the Terminal, the generated code should use the `myPackage` bundle instead of `.module`. Referring to your fonts and assets from external previews will now hopefully work.


## Conclusion

Adding assets to Swift packages is very easy and convenient, but currently have some bugs that make using them problematic. If you have faced these problems, I hope that you found this post helpful. 

Let's hope that the Swift package team is aware of this problem and that the upcoming changes at this year's WWDC will finally provide a fix for it.