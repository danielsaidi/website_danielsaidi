---
title: NUnit BadImageFormatException
date:  2011-05-27 12:00:00 +0100
tags:  archive
icon:  dotnet
---

In a project of mine, NUnit suddenly started to warn that something's wrong with
the assembly. Turns out that accidentally disabling architectures is a bad thing.

I use NUnit in all my projects. It works amazingly well in all of them. In this
new project, it worked great at first, then suddenly starting failing with the
following errors:

![BadImageFormatException](/assets/blog/11/0527.png)

I tried to solve this problem for a long time, but eventually gave up and recreated
the project from scratch, which solved the problem.

After doing so, I sat down with a colleague and had a look at the original, still
failing project. We found that one architecture has been accidentally disabled. This
caused the test project to fail when it tested the project.