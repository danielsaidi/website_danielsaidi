---
title:  "SPM Package Problems"
date:   2021-01-06 12:00:00 +0100
tags:   spm
---

I have several open source projects and was very excited when Apple announced that Swift Package Manager is coming to iOS. After adding SPM support to my projects, I can now say that it's great, but not yet perfect. In this post, I'll list some SPM problems that I have faced, and how I've solved or learned to live with them.

This post is based on personal experiences, that may very well be caused by that I am using SPM incorrectly. Feel free to leave comments with your own experiences and correct me whenever I've got something wrong or if a SPM bug has been fixed.


## No static resources

SPM currently doesn't support static resources, so if you have to embed `xib`s, images, fonts etc. into your project, you can't use SPM.


## Invalid .gitignore

If you create a new Swift Package with the `swift package init` command, the generated `.gitignore` file will look like this:

```
.DS_Store
/.build
/Packages
/*.xcodeproj
xcuserdata/
```

This is very strange, since you may need to add an Xcode project later. If you do so later without adjusting `.gitignore`, the project will be excluded from git.

This actually happened to me when I published [SwiftUIBlurView][SwiftUIBlurView] and it took me a while to realize why people couldn't run the demo app.


## Invalid Xcode project setup

SPM doesn't require an Xcode project. You can just place source files in `Sources` and test files in `Tests` and SPM will happily consider it to be a package.

However, if you want to provide a demo app, add Carthage support etc. you need a project as well. You can create one by running `swift package generate-xcodeproj` from the console in your package root. This will generate a project for you.

However, the generated project is strange in a number of ways. Perhaps I am just doing it wrong, but you will have to fix many things, like 

xxxxxx


## Unit testing UIKit

If you create a new SPM package and add `UIKit` to it, you will not be able to run unit tests from the terminal. If you do, you will get this kind of errors:

xxxxx

To be able to run the tests, you have to create an Xcode project (see above) and run the tests through the project instead. You do this by

xxxxxx


## Package Synchronization

Xcode will automatically sync SPM dependencies every now and then, e.g. when you switch git branch. Even though it's handly, it's also dangerous, since it doesn't cancel ongoing sync operations.

This means that if you switch branch while a sync is ongoing, your `Package.lock` file can become invalid if a sync completes on another branch than it started on.


[SwiftUIBlurView]: https://github.com/danielsaidi/SwiftUIBlurView