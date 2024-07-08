---
title: Slow iMac about to be fixed?
date:  2011-05-12 12:00:00 +0100
tags:  macos
icon:  avatar-sweat

forum: https://discussions.apple.com/message/12913591?messageID=12913591
---

I have been having big problems with my iMac 27″ (4GB RAM) that runs Windows 7 on a Boot Camp Partition (using VMWare Fusion), where it writes constantly and is super slow. 

The iMac basically has the same setup as I have on my MacBook Pro with 8GB of RAM, with the difference that the MBP is fast as lightning and the iMac is slow as hell.

I naturally don't expect the iMac to be fast when running both OS X and Win 7 with 2GB each, but you have to experience it to know what I’m talking about.

The hard drive reads and writes constantly. It doesn't matter whether VMWare is running or not, it's very slow anyway.

Despite all my communication with various Mac Stores and Apple Support, no one has been able to help me...until tonight, thanks to the people at [discussions.apple.com]({{page.forum}}).

Turns out that Spotlight can go bad when you have a Boot Camp partition, which causes Spotlight to constantly try to index the Boot Camp partition.

If Spotlight is unable to write the index result to disk (due to mismatching file systems), the index operation will try again, and again, and again.

When I rebooted my iMac, the disc was writing like crazy from start. As soon as I added the Boot Camp partition to the no-indexing list in Spotlight, it went quiet and became fast.

The fix is reset when I restart my computer, but I will follow the root folder file advice in the discussion and see if it helps.