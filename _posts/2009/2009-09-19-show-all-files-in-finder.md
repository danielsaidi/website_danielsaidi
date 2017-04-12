---
title:  "Show all files in Finder"
date:   2009-09-19 08:00:00 +0100
categories: general
tags:	finder
---


As a computer professional (just kidding), I prefer to see all files in the file
system and not just the ones that Finder or Explorer wants us to see.

In Windows, you can easily just check a box to show all hidden files in Explorer.
In OS X, however, you have to type the following in a terminal window:

	defaults write com.apple.finder AppleShowAllFiles TRUE
	killall Finder

This will restart Finder and have it show all files, hidden or not. To revert it
and hide these files again, use this command:

	defaults write com.apple.finder AppleShowAllFiles FALSE
	killall Finder

HOpe it helps!
