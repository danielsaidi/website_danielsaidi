---
title: Activating Windows 8
date:  2012-11-11 00:30:00 +0100
tags:  windows
---

Inspired by Øredev and all the great sessions, I finally installed Windows 8. I 
decided to upgrade my Windows 7 installation, and found the installation quick 
and painless....and the activation a nightmare. 

After the installation was done, I was asked to activate Windows. However, when
I tried to do so from the new Metro app, it just said that Windows failed to
activate and that I should try again later.

I entered classic mode and opened the old control panel. There, I found that the
old Windows 7 product key was still being used. No wonder the activation process
failed. However, I couldn't change the key from this view. There was no edit button, 
no text fields, nothing.

Turns out that this problem is well-known, so I found a cmd command that I could 
use to change the current product key (why there is no ui for this is beyond me). 
To do so, start the command prompt and enter the following:

`slmgr.vbs -ipk <YOUR PRODUCT KEY>`

After this, return to the control panel and run the activation routine again. 
It should work now. Can we all agree that this is by far the worst onboarding 
experience ever?