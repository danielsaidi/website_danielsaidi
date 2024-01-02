---
title: Windows 8 Activation Fail
date:  2013-06-07 09:32:00 +0100
tags:  archive
icon:  avatar-swear

assets: /assets/blog/2013/130607/
---

Unlike Apple's outstanding OS X onboarding experience, Microsoft really have to 
step up the Windows onboarding. When you charge a lot of money for people to use
your OS and require them to activate it, the activation must work. Sadly, it doesn't.

I use an MSDN Windows 8 distribution. When I try to activate it in the (limited) 
Metro app, I get this:

!["Windows can't activate at the moment" screen.]({{page.assets}}windows1.png)

Clicking the Activate button doesn't help, though. If you do, you just get a dead
progress spinner:

![An activation spinner doing nothing much]({{page.assets}}windows2.png)

Obviously, this Metro app will not help you out, so let's activate Windows from 
the Control Panel instead. Open it, search for "activat" and select "Action 
Center/Windows Activation".

![Control panel activation screen]({{page.assets}}windows3.png)

Sadly, this view is as useless as the Metro app. Click Activate and you're taken 
to a progress bar dialog, that ends up in...

![0xC004F074 - Windows couldn't be activated]({{page.assets}}windows4.png)

This is just ridiculous. How can Microsoft fail so completely with such a critical
part of the onboarding? How can you create a software that you have to activate, 
that can't be activated? It may be by design, but I have a feeling that things are
rotten in the Microsoft UX basket. 

Don't worry - activation is just a blog post away. When searching the world wide
web, I stumbled upon [this article](http://support.microsoft.com/kb/2750773?wa=wsignin1.0)
regarding activation.

What the article basically suggests, are two options for activating Windows 8:

* Search for, and run `Slui.exe 0x3`
* Run `Cscript.exe %windir%\system32\slmgr.vbs /ipk <Your product key>`

This should work. I just can't even begin to understand how this can be. How the
hell can this ever get released? Who in the MS organization decided to ignore this 
key-feature? Who? Why!? Gaaaah!