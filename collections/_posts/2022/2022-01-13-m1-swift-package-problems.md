---
title:  M1 Swift Package Problems
date:   2022-01-13 08:00:00 +0100
tags:   swift swiftui xcode
---

I got my new 14" M1 MacBook Pro in mid-December and absolutely love it. However, there are things with the new architecture that cause a bunch of serious problems when wokring with Swift packages.


## SwiftUI previews are failing in packages

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


## Bitcode failure for closed-source packages

I have a closed-source project that I manage as a framework project, build with a terminal script and distribute as an XCFramework.

Everything worked great on my Intel-based MacBook Pro, but after switching over to my new M1, the generated framework no longer supports Bitcode. I have Bitcode enabled in the framework project, though, but for some reason this no longer applies when I run the archive script.

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

When I add the XCFramework file to an app, I now get a warning that the framework doesn't support Bitcode and that the app must therefore disable Bitcode as well.

Since this used to work on my Intel-based MacBook Pro, I'm not sure if this is due to the new hardware architecte or if it's a problem with the new macOS Mojave or Xcode 13.2.

Any information you may have would be most welcome.


## Conclusion

These problems above are pretty serious to my everyday workflow. I will update this post with any new information that I may find and would greatly appreciate any information you may have. If so, feel free to write in the discussion form below or reach out via [Twitter](https://twitter.com/danielsaidi).