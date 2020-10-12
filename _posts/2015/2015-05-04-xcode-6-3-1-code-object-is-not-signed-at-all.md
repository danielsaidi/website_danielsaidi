---
title: Xcode 6.3.1 - Code object is not signed at all
date:  2015-05-04 20:54:00 +0100
tags:  xcode ios
---

After upgrading to Xcode 6.3.1, I got a new error that I haven't received before:

```
ERROR ITMS-90035: "Invalid Signature. Code object is not signed at all..."
```

The error popped up every time I tried to submit apps to the App Store, even old
apps that were perfectly submittable before upgrading Xcode to 6.3.1.

Turns out that the problem was caused by an .sh file in one of the external libs
used by the app. Previously, archiving apps with this .sh file worked great, but
not anymore.

Since this .sh file was not needed by the app, I just removed the file reference
and kept the file on disk. If you run into the same error code, I hope this will
solve your problem.