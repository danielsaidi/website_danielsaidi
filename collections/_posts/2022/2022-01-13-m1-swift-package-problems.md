---
title: M1 Swift Package and XCFramework Problems
date:  2022-01-13 08:00:00 +0100
tags:  swiftui xcode spm
icon:  swiftui
---

I absolutely love my brand new 14" M1 MacBook Pro, but there are architectural problems when working with Swift packages and XCFramework builds. In this post, I'll describe some problems...and solutions.


## Swift Packages can't preview SwiftUI previews

I have a bunch of open source projects that support Swift Package Manager. Many of them contain SwiftUI views, and I was happy that Apple added the ability to use SwiftUI previews within packages. 

Since then, I often extract app-agnostic views and logics to app-specific packages, to improve build times and separate concertns. Running unit tests and building previews has never been faster.

However, on my M1, previews no longer work in Swift Packages. It doesn't matter if create a new package, use a simulator or a real device, clean derived data and the build folder. It never works.

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

When it fails, I get a preview error saying "Cannot preview in this file - Message send failure for update" and tapping the Diagnosticts button reveal a long message where you can find these gems:

* LoadingError: failed to load library at path 
* mach-o file, but is an incompatible architecture (have 'x86_64', need 'arm64')

Previews work in app projects, though, so my current workaround is to create an app project and drag in the views that I work with. However, this is a time consuming workaround.

After getting in contact with Apple, it turns out that Xcode was running in Rosetta. I have no idea how this happened and can't remember enabling that option, but unchecking Rosetta made previews work.


## XCFrameworks don't support Bitcode

I have a closed-source project that I manage as an Xcode iOS Framework project, build with a Terminal script and distribute as an XCFramework.

Everything worked great on my Intel-based MacBook Pro, but after switching over to M1, the generated XCFramework no longer supports Bitcode. I have Bitcode enabled in the framework project, though, and have tried adding additional flags and tweaking the build script, but nothing brings Bitcode support back.

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

I have added options like `ENABLE_BITCODE`, flags like `-fembed-bitcode` and basically tried everything that I could find, but when I add the resulting XCFramework file to an app, I get this warning:

```
*** was built without bitcode. You must rebuild it with bitcode enabled (Xcode setting ENABLE_BITCODE), obtain an updated library from the vendor, or disable bitcode for this target. Note: This will be an error in the future.
```

Since this framework used to work great on my Intel-based MacBook Pro, I'm not sure if this is due to the new hardware architecture or if it's a problem with the new macOS Mojave or Xcode 13.2. All I know is that I'm looking for the solution a problem that is new to my M1 and that no one else seems to share.

What finally made me solve this problem was simply to create a new Xcode iOS Framework project with the same name and move the source code from the old project into the new one. This works, but since the build settings of the two projects are identical, I'm at a loss as to why it works.

To see the silver lining here, this actually made me put some time into creating a Swift Package for the framework as well, which I didn't have before. Earlier, I generated the XCFramework while developing new features, but now I can just use it as a local package, which simplifies development of new features.

All in all, if you find yourself facing the same problem, try creating a new project with Xcode 13 and cross those luck-bringing fingers of yours.