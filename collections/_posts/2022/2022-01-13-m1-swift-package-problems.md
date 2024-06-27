---
title: M1 Swift Package and XCFramework Problems
date:  2022-01-13 08:00:00 +0100
tags:  swiftui xcode spm
icon:  swiftui
---

I love my 14" M1 MacBook Pro, but have some architectural problems when working with Swift Packages and XCFramework builds. Let's look at some problems...and solutions.


## Swift Packages can't preview SwiftUI previews

I have many open-source projects that use Swift Package Manager. Many contain SwiftUI views, so I was happy that Apple added support for SwiftUI previews within packages. 

Since this support was added, I often extract app-agnostic views and logics to app-specific packages, to improve build times and separate concertns. Running unit tests and building previews has never been faster.

However, on my M1, previews no longer work in Swift Packages. It doesn't matter if create a new package, use a simulator or a real device, clean derived data, etc. It never works.

Even this view in a brand new package fails:

```swift
struct TestView: View {
    
    var body: some View {
        Text("Hello, World!")
    }
}

struct TestView_Previews: PreviewProvider {
    
    static var previews: some View {
        TestView()
    }
}
```

When it try, I get an error saying `Cannot preview in this file - Message send failure for update`. Tapping the Diagnosticts button show a long message where you find these gems:

* LoadingError: failed to load library at path 
* mach-o file, but is an incompatible architecture (have 'x86_64', need 'arm64')

Previews work in apps, though, so my current workaround is to create an app project and drag in the views that I work with. However, this is a time consuming workaround.

After talking with Apple, it turns out that Xcode was running in Rosetta. I have no idea how this happened and can't remember enabling it, but unchecking Rosetta made things work.


## XCFrameworks don't support Bitcode

I have a closed-source project that I manage as an Xcode iOS Framework project, build with a Terminal script and distribute as an XCFramework.

Everything worked great on my Intel-based MacBook Pro, but after switching over to M1, the generated XCFramework no longer supports Bitcode.

I have Bitcode enabled in the framework project, though, and have tried adding additional flags and tweaking the build script, but nothing brings Bitcode support back.

The archive script is executed with Fastlane and contains of the following steps (real framework name replaced with MyFramework in the code below):

```
xcodebuild archive 
    -scheme MyFramework 
    -destination "generic/platform=iOS" 
    -archivePath build/MyFramework SKIP_INSTALL=NO BUILD_LIBRARY_FOR_DISTRIBUTION=YES
```

```
xcodebuild archive 
    -scheme MyFramework
    -destination "generic/platform=iOS Simulator" 
    -archivePath build/MyFramework-Sim SKIP_INSTALL=NO BUILD_LIBRARY_FOR_DISTRIBUTION=YES'
```

```
xcodebuild -create-xcframework 
    -framework build/MyFramework.xcarchive/Products/Library/Frameworks/MyFramework.framework 
    -framework build/MyFramework-Sim.xcarchive/Products/Library/Frameworks/MyFramework.framework 
    -output build/MyFramework.xcframework'
```

I have added options like `ENABLE_BITCODE`, flags like `-fembed-bitcode` and tried everything I could find, but when I add the resulting XCFramework file to an app, I get this warning:

```
*** was built without bitcode. You must rebuild it with bitcode enabled (Xcode setting ENABLE_BITCODE), obtain an updated library from the vendor, or disable bitcode for this target. Note: This will be an error in the future.
```

Since this used to work great on my Intel-based MacBook Pro, I'm not sure if this is due to the new hardware architecture or if it's a problem with macOS Mojave or Xcode 13.2. 

What finally made me fix this was to create a new Xcode iOS Framework project with the same name and move the code from the old to the new. This works, but I don't know why.

To see the silver lining here, this made me put some time into creating a Swift Package for the framework as well, which I didn't have before. I used to generate an XCFramework, but now I can just use it as a local package, which simplifies development of new features.

All in all, if you find yourself facing the same problem, try creating a new project with Xcode 13 and cross those luck-bringing fingers of yours.