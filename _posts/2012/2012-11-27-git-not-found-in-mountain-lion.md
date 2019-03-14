---
title:  "Git not found in Mountain Lion"
date: 	2012-11-27 10:24:00 +0100
tags: 	osx git
---


I recently updated OS X Lion to OS X Mountain Lion on all my personal computers.
The installation was smooth, but afterwards some things did not work as expected.

First of all, **VMware Fusion 3.x** did not work anymore. However, since I have
just updated to Windows 8 (which does not support VMware Fusion 3) this was not
that big of a deal. I have had more than a year of fun with Fusion, but I think
I'll go back to rebooting if I want to work (or game) in Windows.

However, one thing that I had to fix was **git**, which broke when upgrading. If
I opened up the Terminal and typed a git command, git was no longer recognized.
Downloading and installing git from the git website did not help either. Bummer!

Maybe this has been fixed at the time of writing, but I just wanted to share how
I re-installed git. If you have the same problems as I had, maybe this will help
you out as well.

Follow the steps below to re-install git using Xcode:

* Start Xcode
* In the main menu, select `XCode > Preferences`
* Select the Downloads tab
* Install Command Line Tools

Once the installation is done, git will once again be available in the Terminal.