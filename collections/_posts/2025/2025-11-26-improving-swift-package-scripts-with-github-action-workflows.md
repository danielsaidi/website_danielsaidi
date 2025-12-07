---
title:  Improving Swift Package Scripts with GitHub Actions workflows
date:   2025-11-26 06:00:00 +0000
tags:   swift sdks automation

assets: /assets/blog/25/1126/
image:  /assets/blog/25/1126/image.jpg
image-show: 0

sdk: https://github.com/danielsaidi/swiftpackagescripts

bsky: https://bsky.app/profile/danielsaidi.bsky.social/post/3m57ish4b6c2g
toot: https://mastodon.social/@danielsaidi/115618153465658559
linkedin: https://www.linkedin.com/posts/danielsaidi_building-closed-source-binaries-with-github-activity-7393319947101159424-iylF
---

I have improved my open-source project [Swift Package Scripts]({{page.sdk}}) which contains Swift Package-related scripts, by adding a couple of convenient GitHub Action Workflows to it.

![SwiftPackageScripts logo](/assets/sdks/swiftpackagescripts-header.jpg)


## What is Swift Package Scripts?

[Swift Package Scripts]({{page.sdk}}) (SPS) is an open-source project that has Terminal scripts for handling common Swift Package-related tasks, like building, testing, releasing, bumping version, etc.

SPS also has a set of GitHub Actions workflows that can build and test your packages on each push, auto-generate DocC documentaton for GitHub Pages, etc.

SPS doesn't require any additional system tools, like Ruby. This makes more lightweight and stable than other tools, like Fastlane.


## How do you add it to your package?

To add SPS to a package, just clone the repo and run `./scripts/sync-to.sh` from the SPS root folder:

```
./sync-to.sh ../../Projects/MyPackage/
```

This will copy the `scripts/` folder to the path you define. You can also just copy the folder manually.

Swift Package Scripts 2.0 also includes a new `.github/workflows` folder (see more below), This folder is ignored by default, but you can include with `--github-workflows 1`.


## How do you use the scripts?

Once you have the `scripts/` folder in the package folder, you can run the scripts from the Terminal.

For instance, you can get the package name with `./script/package-name.sh`, build it for all supported platforms with `./scripts/build.sh` and test it on iOS with `./scripts/test.sh --platform (or -p) iOS`.

These scripts perform individual as well as composite tasks. For instance, the `release.sh` script uses several other scripts to create a new release.


## Available scripts

At the time of writing [Swift Package Scripts]({{page.sdk}}) has the following scripts:

* `build.sh` - Build a target for all or certain platforms.
* `chmod-all.sh` - Run `chmod +x` on all scripts in the same folder.
* `docc.sh` - Build DocC documentation for all or certain platforms, incl. web transformed docs.
* `git-default-branch.sh` - Get the default git branch name.
* `l10n-gen.sh` - Parse an Xcode 26 string catalog and create public key wrappers.
* `package-name.sh` - Get the package name.
* `release.sh` - Validate the current branch and code, then create a new release tag.
* `release-validate-git.sh` - Validate that a release is made from the main branch.
* `release-validate-package.sh` - Perform pre-release validations and tests.
* `sync-from.sh` - Sync scripts from a Swift Package Scripts folder.
* `sync-to.sh` - Sync scripts to a target package folder.
* `test.sh` - Test a target on all or certain platforms.
* `version-bump.sh` - Bump the current version a major, minor, or patch step.
* `version-number.sh` - Get the current version number.
* `xcframework.sh` - Generate a binary XCFramework for all or certain platforms.


## <span class="tag new">NEW</span> GitHub Actions Workflows!

The latest major version of [Swift Package Scripts]({{page.sdk}}) adds a set of handy GitHub Actions workflows to a new `.github` folder in the project root.

* `build.yml` - Build the package for all or certain platforms.
* `docc.yml` - Build DocC and deploy documentation to GitHub Pages.
* `test.yml` - Test the package on all or certain platforms.
* `version-bump.yml` - Bump the current version a major, minor, or patch step.
* `xcframework.yml` - Generate a binary XCFramework and dSYMs for all or certain platforms.

The `build`, `docc` and `test` workflows will run on each push to the main branch, while `version-bump` and `xcframework` has to be triggered manually.

Unlike scripts, you may not want to add the entire `.github` folder to your project. Instead, just copy the workflows that make sense for your project. Although the workflows use the scripts to resolve contextual information, you may want to adjust which platforms to target.

The `.github` folder also contains a `FUNDING.yml` file that shows you how to set up GitHub Sponsors.



## Conclusion

[SwiftPackageScripts]({{page.scripts}}) has been extended with GitHub Actions workflows that can be used to ensure that your code builds and that all tests pass. There are also workflows that simplify automatically updating the online documentation on each push, bumping the package version, and generating binary artifacts. 

If you manage a Swift Package, make sure to give [SwiftPackageScripts]({{page.scripts}}) a try and let me know what you think.