---
title: Using shared libraries in iOS
date:  2010-02-04 12:00:00 +0100
tags:  archive
icon:  swift
---

After a couple of evenings, my first (really simple) iPhone app is taking shape.
However, I'm currently struggling with reusing functionality across apps.

Since I am a .NET developer at heart, it is painfully clear how spoiled I have
become by all amazing Visual Studio features. Some things that are natural and
easy to accomplish in .NET, does't come as natural with iOS and Xcode. 

One example is to gather reusable functionality in shared libraries, which is
not as easy in iOS and Xcode as in .NET and Visual Studio. I have found many
blogs that describe how to accomplish this. One good example was a post up at
[http://www.clintharris.net](http://www.clintharris.net), which since then has
been removed.

Until I get more comfortable with this, I will add references to shared folders
on the file system. It's not ideal, and removing files in the folder doesn't sync
well with the Xcode project, but at least I don't have to have copies of the same
files all over my various projects.