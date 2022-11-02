---
title: SPM .gitignore excludes Xcode projects
date:  2020-01-02 22:00:00 +0100
tags:  spm xcode
icon:  swift
---

When you create Swift Packages with `swift package init`, the generated .gitignore will exclude all Xcode projects by default. This will cause problems if you later add an Xcode project to your package.

SPM doesn't require an Xcode project. You can just place source files in `Sources` and test files in `Tests` and SPM will happily consider it to be a package. 

If you want to add a demo app, Carthage support etc. you will however need an Xcode project. Still, the SPM generated .gitignore file looks like this:

```
.DS_Store
/.build
/Packages
/*.xcodeproj
xcuserdata/
```

This means that your demo app project will be excluded from git, if you don't remove `/*.xcodeproj` from `.gitignore` before commiting your changes.

This happened to me when I published a new package, and it took me a while to realize why people claimed they couldn't find the demo app...which I believe did cost me some stars.

To avoid this problem, use a better .gitignore template, or at least remove `/*.xcodeproj` before you add a project to the package.


[SwiftUIBlurView]: https://github.com/danielsaidi/SwiftUIBlurView