---
title: SPM .gitignore excludes Xcode projects
date:  2020-01-02 22:00:00 +0100
tags:  spm xcode
icon:  swift
---

When you create Swift Packages with `swift package init`, the default .gitignore excludes Xcode projects. This will cause problems if you later add an Xcode project to the package.

SPM doesn't require an Xcode project. You can just place source files in `Sources` and test files in `Tests` and SPM will happily consider it to be a package. 

If you want to add a demo app, Carthage support etc. you however need an Xcode project. Still, the default SPM generated .gitignore file looks like this:

```
.DS_Store
/.build
/Packages
/*.xcodeproj
xcuserdata/
```

This means that your demo app will be excluded, if you don't remove `/*.xcodeproj` from `.gitignore` before commiting your changes.

This happened to me when I published a new package. It took me a while to realize why people claimed they couldn't find the demo app...which I believe did cost me some stars.

To avoid this problem, use a better .gitignore template, or at least remove `/*.xcodeproj` before you add a project to the package.