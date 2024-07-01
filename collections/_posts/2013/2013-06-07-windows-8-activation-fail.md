---
title: Windows 8 Activation Fail
date:  2013-06-07 09:32:00 +0100
tags:  archive
icon:  avatar-swear

assets: /assets/blog/13/0607/
---

Unlike Apple's great OS X onboarding experience, The Windows onboarding is horrible. If you charge money to use your OS, activation must work. Too bad, it doesn't.

I use an MSDN Windows 8 distribution. When I try to activate it in the Metro app, I get this:

!["Windows can't activate at the moment" screen.]({{page.assets}}windows1.png)

Clicking Activate doesn't help, though. If you do, you just get a dead progress spinner:

![An activation spinner doing nothing much]({{page.assets}}windows2.png)

Obviously, this Metro app will not help you out, so let's activate Windows from the Control Panel instead. Open it, search for "activate" and pick "Action Center/Windows Activation".

![Control panel activation screen]({{page.assets}}windows3.png)

Sadly, this is as useless as the Metro app. Click Activate and you get a progress dialog that ends up in a "Windows couldn't be activated" error:

![0xC004F074 - Windows couldn't be activated]({{page.assets}}windows4.png)

This is ridiculous! How can Microsoft fail this hard with such an important onboarding step? Something is rotten in the Microsoft UX basket. 

Don't worry - activation is just a blog post away. When searching the web, I stumbled upon [this article](http://support.microsoft.com/kb/2750773?wa=wsignin1.0) regarding activation.

The article basically suggests two options for activating Windows 8:

* Search for, and run `Slui.exe 0x3`
* Run `Cscript.exe %windir%\system32\slmgr.vbs /ipk <Your product key>`

I just can't even begin to understand how this can be. How can this ever get released? Who in the MS organization decided to ignore this key-feature? Who? Why!? Gaaaah!