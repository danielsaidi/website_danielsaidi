---
title:  "Disable .tmp files in Aptana"
date:   2009-08-16 20:26:00 +0100
categories: web
tags: 	html
---


I occasionally develop PHP sites on OS X. When I once pushed an update to my beta
testers, I was told that it contained a LOT of .tmp files, which I could not see
on OS X.

This came as a deja-vu to me, since I have had this problem earlier. I remembered
that the problem then was that Aptana Studio 2 created .tmp files for previewing
purposes.

What Aptana doesn't do, though, is to remove these files. It's quite annoying, but
easily fixed. Just follow these steps:

* Open Aptana
* Select Aptana Studio / Preferences in the main menu
* Under Aptana / Editors / HTML - select Source only
* Under Aptana / Editors / PHP - select Source only

That's it - no more hidden .tmp files!