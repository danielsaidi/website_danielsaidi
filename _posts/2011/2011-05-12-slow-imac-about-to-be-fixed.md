---
title:  "Slow iMac about to be fixed?"
date:   2011-05-12 12:00:00 +0100
tags: 	osx
---

I have been having big problems with my iMac 27″ (4GB RAM) that runs Windows 7 on
a Boot Camp Partition (using VMWare Fusion). It is basically the same setup as I
have on my MacBook Pro (which has 8GB RAM though), with the minor difference that
the MBP is fast as lightning and the iMac is slow as HELL!

Now, don’t get me wrong. I do not expect the iMac to be fast when running both OS
X and Win 7 with 2GB each, but you have to experience it to know what I’m talking
about. Basically, the HDD reads and writes constantly. Sometimes, it goes into an
extra intense state of writing furiously, during which the computer goes slowmo.

It does not matter whether or not VMWare is running, the iMax is dead slow anyway.
Despite all my communication with various Mac Stores and Apple Support, no one has
been able to help me...

...until tonight, thanks to the people at [discussions.apple.com](https://discussions.apple.com/message/12913591?messageID=12913591)

The key concept here is that Boot Camp + Spotlight can go bad, causing Spotlight
to constantly index the Boot Camp partition. I'm almost certain (99% give or take)
that this is what has been causing the slowmotion behavior for me as well.

When I rebooted my iMac, the disc was going crazy from start, writing and reading
like crazt. As soon as I added the Boot Camp partition to the no-indexing list in
Spotlight, the disk went quiet and the computer has been working SOOOO good since
then. The change gets reset when I restart my computer, but I will follow the root
folder file advice in the discussion and see if it helps.