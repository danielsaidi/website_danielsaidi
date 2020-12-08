---
title: Windows 8 Activation Fail
date:  2013-06-07 09:32:00 +0100
tags:  windows
---

Unlike Apple's outstanding OS X experience, Microsoft really have to step up the
Windows user experience. If you are going to charge a lot of money for people to
use your os and require them to activate their copy of Windows, you need to make
this work. Not work good, just plain work.

Sadly, Microsoft fails miserably at doing so.

I use an MSDN Windows 8 distribution that I get from work. When I try to activate
my copy in the (limited) Metro interface, I get this:

!["Windows can't activate at the moment" screen.](/assets/blog/2013/2013-06-07-windows-1.png)

Clicking the Activate button does not help, though. If you do, you are presented
a progress spinner that does not do anything:

![An activation spinner doing nothing much](/assets/blog/2013/2013-06-07-windows-2.png)

You end up with the same, useless error message.

Obviously, this Metro app will not help you out, so let's go pre Windows 8 style
and activate Windows from the Control Panel. Open the Control Panel, then search
for "activat" and select "Action Center/Windows Activation".

![Control panel activation screen](/assets/blog/2013/2013-06-07-windows-3.png)

This looks a lot more hi-tech, right? Sadly, this view is as useless as the Metro
app. Click Activate and you're taken to a progress bar dialog, that ends up in...

![0xC004F074 - Windows couldn't be activated](/assets/blog/2013/2013-06-07-windows-4.png)

This is getting ridiculous. How the f**k can you create a software that you have
to activate, that can't be activated (this is also not the first time that I've
had this problem). It may be by design, but I have a gut feeling that things are
rotten in the Microsoft UX basket. 

Activation is a core "feature", isn't it? Therefore activating Windows should be
easy, shouldn't it!?

Don't worry - activation is just a blog post away. When searching the world wide
web, I stumbled upon [this article](http://support.microsoft.com/kb/2750773?wa=wsignin1.0)
regarding activation.

What the article basically suggests, are two options for activating Windows 8:

* Search for, and run, Slui.exe 0x3
* Run Cscript.exe %windir%\system32\slmgr.vbs /ipk <Your product key>

Tadaa, couldn't be easier, right? I just can't even begin to understand how this
can be. How the hell can this ever get released? I am disgusted by this worthless
piece of rotten UX. Who in the MS organization decided to ignore this key-feature?
Who? Why!? When? Where? Wombat!

Well, now you too can activate Windows 8. I guess that is at least something.