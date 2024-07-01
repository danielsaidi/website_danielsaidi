---
title: Xcode 6.3 - Code object is not signed at all
date:  2015-05-04 20:54:00 +0100
tags:  xcode
icon:  avatar
---

After upgrading to Xcode 6.3, I get an error saying `ERROR ITMS-90035: "Invalid Signature. Code object is not signed at all"`. Let's look at what this is and how to fix it.

This error popped up every time I tried to submit apps to the App Store. It even popped up in old apps that were perfectly submittable before upgrading to Xcode 6.3.1.

Turns out that the problem was caused by an `.sh` file in an external library. Archiving apps with this file worked before, but stopped working in Xcode 6.3.

Since this file was not needed by the app, I just removed the file reference
and kept the file on disk. If you run into the same error code, I hope this
will solve your problem.