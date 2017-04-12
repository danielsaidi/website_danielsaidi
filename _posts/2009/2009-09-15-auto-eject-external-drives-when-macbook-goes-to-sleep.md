---
title:  "Auto-eject external drives when Macbook goes to sleep"
date:   2009-09-15 08:00:00 +0100
categories: general
tags:	mac macbook
---


> **Edit August 28, 2010** SleepWatcher has been changed and differs compared to
the information found in the link below. This approach does, however, still work,
as does the modified script that I provide below.

I have a Macbook, and have always been annoyed that I manually have to eject the
external disk every time I put the computer to sleep.

Luckily, I found a solution to this problem. The link below provides a link to a
nice program called SleepWatcher and also provides you with a nice collection of
wakeup and sleep scripts:

[http://www.macosxhints.com/article.php?story=20080329201951648]()

The scripts will make sure that your external disk is automatically ejected when
your computer goes to sleep.

However, the original script will also eject USB drives, mounted .dmg drives etc.
while I only want to eject a particular external drive. I thus edited the .sleep
script to the following:

	#!/bin/sh
	osascript -e 'tell application "Finder" to eject (disks where name = "type in the name of your disk here")'

Voilá – your external disk will now go to sleep when your computer does...and no
more annoying warning messages will appear.