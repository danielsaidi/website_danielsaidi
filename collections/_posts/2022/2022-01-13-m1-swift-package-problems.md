---
title:  M1 Swift Package and XCFramework Problems
date:   2022-01-13 08:00:00 +0100
tags:   swift swiftui xcode
---

I got my brand new 14" M1 MacBook Pro in mid-December and absolutely love it. However, there are things with the new architecture that cause serious problems when working with Swift packages and XCFramework builds. In this post, I'll describe the problems and return with solutions, should I find any.

Update 2022-01-18: I have solved the XCFramework Bitcode problem and have updated the text with how.


## Swift Packages can't preview SwiftUI previews

I have a bunch of open source projects that support Swift Package Manager, many of them containing SwiftUI views, and was very happy when Apple added the ability to use SwiftUI previews within packages. 

Since then, I often extract app-agnostic views and logics to app-specific packages, just to improve build times when just working with the app domain or views. Running unit tests and building previews has never been faster.

Until M1.

After getting my M1, previews no longer work in Swift Packages. It doesn't matter if create a new package from scratch, if I select simulator or a real device, if I clean derived data and the build folder. It never works.

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

When it fails, I get a preview error saying "Cannot preview in this file - Message send failure for update" and tapping the Diagnosticts button reveal a long message filled with various folder paths, but also these nuggets:

* LoadingError: failed to load library at path 
* mach-o file, but is an incompatible architecture (have 'x86_64', need 'arm64')

It seems like the previews are built with x86_64, while the computer needs arm64 to display them. However, I haven't found any way to specify the architecture for the previews. The same problem appears in framework projects as well.

Previews work in app projects, though, so my current workaround is to create an app project and drag in the views that I work with. However, this is a time consuming workaround that I'd love not having to spend time on.

If you know how to solve the preview problem, please start a discussion in the comment section below. I'll update this section as soon as I find more information or a solution.


## XCFrameworks don't support Bitcode

I have a closed-source project that I manage as an Xcode iOS Framework project, build with a Terminal script and distribute as an XCFramework.

Everything worked great on my Intel-based MacBook Pro, but after switching over to M1, the generated XCFramework no longer supports Bitcode. I have Bitcode enabled in the framework project, though, and have tried adding additional flags and tweaking the build script, but for some reason nothing I do bring Bitcode support back.

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

Since this framework used to work great on my Intel-based MacBook Pro, I'm not sure if this is due to the new hardware architecte or if it's a problem with the new macOS Mojave or Xcode 13.2. All I know is that I'm looking for the solution a problem that is new to my M1 and that no one else seems to share. Any information you may have would be most welcome.


### 2022-01-18: Solution

I managed to solve the XCFramework Bitcode problem and now have a framework that supports Bitcode. I wish I could tell you about a magic switch or build setting, but the only thing that worked for me was to create a new Xcode iOS Framework project with the same name, move in all the source code and unit tests from the old project and build...and now it worked.

I have compared the build settings in the old vs. the new project side by side, but to me they look identical, so I'm at a loss. 

To see the silver lining here, this actually made me put some time into creating a Swift Package for the framework as well, which I didn't have before. Earlier, I had to generate the XCFramework while developing new features, but now I can just remove that Swift Package dependency and replace it with a local package, which simplifies development of new features a lot.

All in all, if you find yourself facing the same problem, try creating a new project with Xcode 13 and cross those luck-bringing fingers of yours.


## Conclusion

These problems above are pretty serious to my everyday workflow, but I am happy to at least have solved the most critical one by getting XCFramework and Bitcode to work again. 

I will update this post with any new information about previews that I may find and would greatly appreciate any information you may have. If so, feel free to write in the discussion form below or reach out via [Twitter](https://twitter.com/danielsaidi).