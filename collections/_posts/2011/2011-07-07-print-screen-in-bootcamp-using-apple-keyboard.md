---
title: Print screen in BootCamp using an Apple keyboard
date:  2011-07-07 12:00:00 +0100
tags:  macos
icon:  avatar
---

I have started to boot Windows directly from a BootCamp partition on my iMac instead of running it with VMWare Fusion. This way, I don't share resources with the OS X partition.

This is nice for gaming, programming etc., since the Windows partition gets access to all system resources. There are however some rough edges.

For instance, using an Apple bluetooth keyboard in Windows isn't all that nice. I had to take a screen dump. Without OS X keyboard shortcut. Without a print screen button. How? 

I was at a loss. The `Shift+Fn+F11` combination didn't work, since I had this option enabled:

![Boot Camp Control Panel](/assets/blog/11/0707.png)

With this option enabled, neither `Shift+Fn+F11` or `Shift+F11` work. What did work, was:

- Uncheck the checkbox above.
- Press Shift+Fn+F11.
- Enjoy the screen dump.

It's a bit of a pain, but at least I can now take screenshots like a human being.