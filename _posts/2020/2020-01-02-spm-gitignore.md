---
title:  "SPM .gitignore excludes Xcode projects"
date:   2020-01-02 22:00:00 +0100
tags:   spm
---

When you create a Swift Package with the `swift package init` command, the generated `.gitignore` will exclude all Xcode projects by default. This will cause problems if you later add an Xcode project to your package.

SPM doesn't require an Xcode project. You can just place source files in `Sources` and test files in `Tests` and SPM will happily consider it to be a package. However, if you want to add a demo app, Carthage support etc. you need an Xcode project.

However, since the generated `.gitignore` file will look like this:

```
.DS_Store
/.build
/Packages
/*.xcodeproj
xcuserdata/
```

your created project will be excluded from git, if you don't remove `/*.xcodeproj` from `.gitignore` before commiting your changes.

This happened to me when I published my small [SwiftUIBlurView][SwiftUIBlurView] package. It took me a while to realize why people claimed they couldn't find the demo app. It also probably cost me some stars.

To avoid this problem, use a better `.gitignore` template, or at least remove `/*.xcodeproj` before you add a project to the package.


[SwiftUIBlurView]: https://github.com/danielsaidi/SwiftUIBlurView