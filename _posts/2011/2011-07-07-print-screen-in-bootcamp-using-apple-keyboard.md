---
title:  "Print screen in BootCamp using an Apple keyboard"
date:    2011-07-07 12:00:00 +0100
categories: general
tags: 	bootcamp
---


I have started to boot up Windows directly from my BootCamp partition on my iMac
instead of running Windows under VMWare Fusion. This way, I do not have to share
resources with the OS X partition, which is nice for gaming, programming etc.

This works really well, but sometimes, running Windows on an iMac using an Apple
bluetooth keyboard can be a bit frustrating.

Today, for instance, I had to take a screen dump. Without OS X keyboard shortcut.
Without a PC print screen button. How do you do that? I was at a total loss. The
`Shift+Fn+F11` combination did not work for me, since I had this option enabled:

![Boot Camp Control Panel](/assets/img/blog/2011-07-07.png)

With this option enabled, neither Shift+Fn+F11 or Shift+F11 work. Now before you
rule me out as a no-brainer, I naturally tried to press Shift+F11 without the Fn
key, but that did not work either.

What did work, however, was:

- Uncheck the checkbox above
- Press Shift+Fn+F11
- Enjoy your screen dump in any way you like
- Re-check the checkbox above

It's a bit of a pain, yes. Any suggestions? Having to press the Fn-key each time
I have to press an F-key is not an option.