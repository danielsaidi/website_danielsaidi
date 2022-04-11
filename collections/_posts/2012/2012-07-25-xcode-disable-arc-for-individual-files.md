---
title: Xcode - Disable ARC for individual files
date:  2012-07-25 10:00:00 +0100
tags:  ios objc
---

I am building an app that uses ARC (Automatic Reference Counting), which means I
from now on will not have to handle memory management as actively as I have done
before. There are still things you have to consider when using ARC, but it makes
memory management a lot easier and less tedious.

I have also rewritten all non-ARC code, since the compiler will not compile code
that uses `release`, `dealloc` (incorrectly) etc. However, I have now found out
that instead of rewriting all of your code, you can disable ARC for a single
file. Just add the file path to a flag to the file under "Build phases", and the
compiler will ignore ARC for the specific file.

It's all wonderfully described on [this page](http://stackoverflow.com/questions/6646052/how-can-i-disable-arc-for-a-single-file-in-a-project).