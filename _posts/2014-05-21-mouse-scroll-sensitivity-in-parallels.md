---
title:  "Mouse scroll sensitivity in Parallels Desktop"
date: 	2014-05-21 07:19:00 +0100
categories: general
tags: 	parallels visual-studio
---


I use Parallels Desktop to run Windows from a dedicated partition on my Mac. It
works great, but requires some setup.

One thing that I have had problems with, is that the mouse scroll sensitivity is
too sensitive in Visual Studio. A careful stroke was enough to send me to either
the top or bottom of my solution in Solution Explorer.

The solution to this problem was to go to `Virtual Machine > Configure > Mouse & 
Keyboard` and uncheck an option called ”Enable smooth scrolling”. Unchecking this
option solved my problems, although I am sure that it will cause some side-effects
later on.