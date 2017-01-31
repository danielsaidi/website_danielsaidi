---
title:  "Xcode - Disable ARC for individual files"
date: 	2012-07-25 10:00:00 +0100
categories: apps
tags: 	ios objective-c arc
---


I am building an app that uses ARC (Automatic Reference Counting). Previously, I
also chose to rewrite all non-ARC code, since the compiler will not compile code
that uses `release`, `dealloc` (incorrectly) etc.

However, instead of rewriting all of your code, you could disable ARC for single
files instead. Just add a flag to the file under "Build phases" and the compiler
will ignore ARC for the specific file.

It is all wonderfully described on [this page](http://stackoverflow.com/questions/6646052/how-can-i-disable-arc-for-a-single-file-in-a-project).