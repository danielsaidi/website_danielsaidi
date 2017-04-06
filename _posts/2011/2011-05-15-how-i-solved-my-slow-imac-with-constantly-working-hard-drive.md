---
title:  "How I solved my slow iMac with a constantly working hard-drive"
date:    2011-05-15 12:00:00 +0100
categories: general
tags: 	os-x spotlight
---


A couple of days ago, I blogged about solving a frustrating problem that made my
iMac dead slow. At the time of writing, I was not sure if I had actually solved
the problem, but I can now say that I have.

In short, my 27″ iMac has been really (really!) slow since I bought it. The hard
drive has been writing constantly, especially when starting and when waking up,
but also when idling. When the hard drive was reading and writing like hell, the
iMac went into slow motion.

![The mighty iMac – great once you fix Spotlight](/assets/img/blog/2011-05-15.jpg)

I use OSX 10.6.7 and have a BootCamp partition on which I have Windows 7 installed.

Neither the retailer nor the service provider (to which i sent the computer) nor
Apple Support were able to figure out why the computer was acting the way it was.
As a matter of fact, the SP did not even confirm the slow behavior. They probably
just ran Disk Utility and managed to scratch the chassi when doing so.

Last week, though, I finally managed to solve the problem and end 6 months daily
frustrations. To help others out, I sent this to some of colleagues and instantly
received a bunch of responses from equally frustrated colleagues who have had the
same problem without being able to solve it.

The problem turned out to be (as discussed [here](https://discussions.apple.com/message/12913591?messageID=12913591))
that Spotlight tries to index the BootCamp partition. When it fails to do so, it
just tries once more, then once more, then...well, you get the point 🙂

The solution is to add the BootCamp partition to the Spotlight ignore list. When
I did, the problems stopped immediately and my iMac is now lightning fast. It is
an amazing difference. You may need to re-add this ignore rule after rebooting (I
had to do it once) and maybe add a file to the BootCamp root, but once you have,
you will be in a world of speed.

Now, since no one (except the great people at the forum) was able to help me out
with this problem, my advice to Apple is to:

- Fix the Spotlight bug (duh)
- Recognise it, so that you know what is causing something that makes your great computers sooo slow and your customers soooooooo frustrating.
- Recognise it, so that, when people call in in desperation, you will be able to help.
- Please, tell your SP:s not to scratch my monitor the next time I send it to them. It is impossible for me to prove that they did.

If you have the same problem, I really hope this helps.