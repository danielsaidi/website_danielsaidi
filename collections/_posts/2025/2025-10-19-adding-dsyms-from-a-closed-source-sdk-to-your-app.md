---
title:  Adding dSYMs from a closed-source Swift SDK to an app
date:   2025-10-19 06:00:00 +0000
tags:   swift sdks

assets: /assets/blog/25/1019/
image:  /assets/blog/25/1019/image.jpg
image-show: 0

scripts: https://github.com/danielsaidi/swiftpackagescripts

bsky: https://bsky.app/profile/danielsaidi.bsky.social/post/3m3khet6nsc2a
toot: https://mastodon.social/@danielsaidi/115401171597413266
---

If your app depends on a closed-source SDK, you may find that the crash reports don't provide a full strack trace for the SDK. Let's see how you can add the SDK dSYMs to your app to improve this.


## What are dSYMs and why do we need them?

Before we start looking at how to add SDK dSYMs to your app, we may have to answer the question "what are dSYMs?".

The term `dSYM` stands for `debug symbol file`, which acts as a "translation guide" between compiled machine code and its original source code. 

When Swift code is compiled for release, the compiler optimizes it by stripping out human-readable information like function names, variable names, and line numbers, replacing them with memory addresses. This makes the code smaller and faster, but it also means that when a crash occurs, the crash report has hexadecimal addresses like `0x0000...`, instead of meaningful function names.

This is where dSYMs become essential. They connect those obscure memory addresses back to the original source code locations. When you upload dSYMs to App Store Connect alongside your app, Apple can automatically "symbolicate" crash reports, which converts those memory addresses into readable stack traces that show exactly in which function and on which lines the crash occurred. 

Without dSYMs, debugging production crashes is nearly impossible. With them, you get actionable crash reports that pinpoint exactly where things went wrong.

Third-party frameworks and SDKs must provide their dSYMs to developers, since only those dSYMs can decode crashes that originate within the framework's code. Since dSYMs are unique for a build, they must be produced as part of the build process.


## Uploading apps without dSYMs

If you upload an app that contains a closed-source framework, without including the dSYMs for that framework, Xcode will show a warning modal to tell you that the dSYMs are missing:

![A screenshot of the Xcode dSYMs missing alert modal]({{page.assets}}xcode-upload-warning.png){:class="plain"}

Without dSYMs, any crashes that originate from the closed-source SDK will be unsymbolicated. This will cause the crash report to omit any information that can be used to fix the crash.

![A screenshot of an Xcode crash report with unsymbolicated crash symbols]({{page.assets}}xcode-unsymbolicated-crash-report.png){:class="plain"}

The crash report tells us that `KeyboardKit` has crashed during `[NSString initWithUTF8String]`, but we can't see anything more than this. To get more information, we need the dSYMs from KeyboardKit.


## Creating dSYMs for a closed-source framework

If you are a closed-source SDK vendor, you have to include the dSYMs as part of your build process.

To do this, just add `DEBUG_INFORMATION_FORMAT=dwarf-with-dsym` to the `xcodebuild archive` command that is used to create the XCframework.

Top simplify things, I have created a [SwiftPackageScripts]({{page.scripts}}) open-source project that contains a bunch of package-related scripts, including the `framework` script that includes support for dSYMs. Check it out of you want to avoid relying on complicated scripts or tools to manage your SDK build process.

Once you managed to create dSYMs as part of creating a new SDK release, you should upload a zip file with the dSYMs alongside your SDK release, to give developers a way to download them.


## Adding additional dSYMs to an app

To get rid of the warning modal and receive properly symbolicated crash reports, you must add the dSYMs to your app before uploading it to App Store Connect.

To do so, first archive your app to prepare it for upload. The image below shows all archives for the KeyboardKit app, which uses the KeyboardKit SDK as a proper external dependency.

![A screenshot of the Xcode archives modal]({{page.assets}}xcode-archives.png){:class="plain"}

Right-click on the archive that you want to add dSYMs to and choose `Show in Finder`, then right-click the archive in Finder and choose `Show Package Contents`. 

Finally add the dSYMs to the dSYMs folder, to include them when uploading to App Store Connect:

![A screenshot of Finder and an added dSYMs file]({{page.assets}}finder-dsyms.png){:class="plain"}

If you return to the Xcode archives modal and press "Distribute App", you will now get a green checkmark instead of the dSYMs warning.

![A screenshot of the Xcode successful upload modal]({{page.assets}}xcode-upload-success.png){:class="plain"}

This means that the missing dSYMs are now properly uploaded, which will result in any future crash reports to be symbolicated.


## Other crash report tools

While Xcode requires that you include additional dSYMs as part of uploading your app to App Store Connect, other crash reporting tools like Crashlytics allow you to upload dSYMs later.


## Conclusion

Including the dSYMs of any closed-source SDKs your app depends on is very important, to be able to get proper crash reports for crashes that originate from the SDK. 

If an SDK you rely on doesn't provide dSYMs, reach out to the SDK vendor and ask them to include dSYMs in all future releases. You can then easily add the dSYMs to your app before uploading new builds to App Store Connect.