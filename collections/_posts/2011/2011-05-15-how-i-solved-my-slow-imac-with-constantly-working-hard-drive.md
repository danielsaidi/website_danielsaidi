---
title: How I solved my slow iMac with a constantly working hard-drive
date:  2011-05-15 12:00:00 +0100
tags:  macos
icon:  avatar

post:   https://discussions.apple.com/message/12913591?messageID=12913591
---

A couple of days ago, I blogged about solving a frustrating problem that made my
iMac dead slow. At the time of writing, I wasn't sure if I had actually solved
the problem. I can now say that I have.

In short, my 27″ iMac has been super slow since I bought it. The hard drive has
been writing constantly, especially when starting and waking up, but also when
being idle. When the hard drive was reading and writing like crazy, the iMac went
into slow motion mode.

![The mighty iMac – great once you fix Spotlight](/assets/blog/2011/110515.jpg)

For info, I use OSX 10.6.7 and have a BootCamp partition on which I run Windows 7.

Neither the retailer, the service provider to whom I sent the computer nor Apple
Support were able to figure out why the computer was acting the way it was. As a
matter of fact, the service provider didn't even confirm the slow behavior. They
probably just ran Disk Utility and managed to scratch the chassi when doing so.

Last week, though, I finally managed to solve the problem and end 6 months daily
frustrations. To help others out, I sent this to some of colleagues and instantly
received a bunch of responses from equally frustrated colleagues who have had the
same problem without being able to solve it.

The problem (as discussed [here]({{page.post}})) turned out to be that Spotlight
tries to index the BootCamp partition, without being able to write to it. When it
fails to write, it tries once more, then once more, then...

The solution is to add the BootCamp partition to the Spotlight ignore list. When
I did, the problems stopped immediately and my iMac is now lightning fast. It is
an amazing difference. You may need to re-add this ignore rule after rebooting (I
had to do it once) and maybe add a file to the BootCamp root, but once you have,
you will be in a world of speed.

If you have the same problem, I really hope this helps.