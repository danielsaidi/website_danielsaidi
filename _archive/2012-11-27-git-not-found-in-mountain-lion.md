---
title: Git not found in Mountain Lion
date:  2012-11-27 10:24:00 +0100
tags:  archive
---

I recently updated OS X Lion to OS X Mountain Lion on all my personal computers.
The installation was smooth, but afterwards some things did not work as expected.
For instance, git was no longer found.

After upgrading, `git` was simply not a recognized command in the Terminal any more.
Downloading and installing git from the git website didn't help either.

I finally solved it by re-installing git with Xcode:

* Start Xcode
* In the main menu, select `XCode > Preferences`
* Select the Downloads tab
* Install Command Line Tools

Once the installation is done, git will once again be available in the Terminal.