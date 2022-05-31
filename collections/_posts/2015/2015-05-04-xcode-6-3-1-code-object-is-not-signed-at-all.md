---
title: Xcode 6.3.1 - Code object is not signed at all
date:  2015-05-04 20:54:00 +0100
tags:  xcode
icon:  avatar
---

After upgrading to Xcode 6.3.1, I get an error that says `ERROR ITMS-90035: 
"Invalid Signature. Code object is not signed at all"`. Let's look at what 
this is and how to fix it.

This new, cryptic error popped up every time I tried to submit apps to the 
App Store. It even popped up in old apps that were perfectly submittable 
before upgrading to Xcode 6.3.1.

Turns out that the problem was caused by an `.sh` script file in one of the
external libraries used by the app. Previously, archiving apps with this file
worked, but after upgrading, it stopped working.

Since this file was not needed by the app, I just removed the file reference
and kept the file on disk. If you run into the same error code, I hope this
will solve your problem.