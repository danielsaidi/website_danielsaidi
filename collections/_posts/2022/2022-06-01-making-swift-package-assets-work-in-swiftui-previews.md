---
title:  Making Swift package assets work in SwiftUI previews
date:   2022-06-01 01:00:00 +0000
tags:   swiftui spm swiftgen

icon:   swiftui

article1:   https://developer.apple.com/forums/thread/664295
article2:   https://dev.jeremygale.com/swiftui-how-to-use-custom-fonts-and-images-in-a-swift-package-cl0k9bv52013h6bnvhw76alid
swiftgen:   https://github.com/SwiftGen/SwiftGen
---


In this post, we'll take a look at how we can get colors, images and other assets that are defined in Swift packages to work in external SwiftUI previews, such as in an app.


## Background

Swift packages make it very easy to share assets, such as colors and images, as well as files, e.g. fonts. Just add resources to a package folder and specify the folder in the package definition file:

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

When you define resources for a package, the package will generate a `.module` bundle that you can use to access any embedded assets and resources. Tools like [SwiftGen]({{page.swiftgen}}) also use this bundle to access resources from the package.

However, while this works great within the package, SwiftUI is currently not able to use the `.module` bundle in external previews. Any previews that refer to external package assets in any way will crash.

This bug is discussed in great detail [here]({{page.article1}}) and [here]({{page.article2}}), where [Skyler_S](https://developer.apple.com/forums/profile/Skyler_S) and [Jeremy Gale](https://hashnode.com/@jgale) proposes creating a custom bundle that adds additional ways to resolve a bundle when it's being used in a SwiftUI preview.


## Creating a custom bundle

To create this custom bundle, lets first extend `Bundle` with a private class that the package can use to find the package bundle:

```swift 
extension Bundle {

    private class BundleFinder {}
}
```

We then have to define the package name. The pattern used to be `LocalPackages_<ModuleName>` for iOS, but the new format is:

```swift 
extension Bundle {

    static let myPackageBundleName = "MyPackage_MyPackage"
}
```

This can however change in new Xcode versions, so make sure to test it when a new version of Xcode is released. If it stops working, you can print out the path like this and look for the bundle name:

```swift
Bundle(for: BundleFinder.self)
    .resourceURL?
    .deletingLastPathComponent()
    .deletingLastPathComponent()
```

Also note that the name pattern above may be different for macOS, but you should then just be able to add an `#if os(macOS)` check and return another value.

We can now define a custom bundle that will look for the bundle in more places than `.module` does:

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

With this new bundle in place, you can now use the `.myPackage` bundle instead of `.module` to make external previews work. If they start crashing again, you can use the technique above to find out why.


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

If you run `swiftgen`, the generated code should now use `.myPackage` instead of `.module` and any external previews that refer to these assets should render without crashes.


## Conclusion

Adding assets to Swift packages is very easy and convenient, but SwiftUI currently have some bugs that make using them problematic. If you have these problems, I hope that this post was helpful. 

Let's hope that the Swift package team is aware of this problem and that the upcoming changes at this year's WWDC will finally provide a fix for it and make this post obsolete.