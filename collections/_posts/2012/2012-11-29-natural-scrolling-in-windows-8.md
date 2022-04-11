---
title: Natural Scrolling in Windows 8
date:  2012-11-29 23:03:00 +0100
tags:  windows
---

In OS X Lion, Apple introduced `natural scrolling`. It means that when you scroll,
the scrollable content will move around like it was a sheet of paper you pressed
and dragged around with your finger. 

![Apple Mouse](/assets/blog/2012/2012-11-29-mouse.jpg)

If you haven't tried natural scrolling yourself, you may think that this is how 
you have scrolled all along. However, the traditional way of scrolling is
actually inverted.

Although natural scrolling may feel strange at first, you quickly get used to it.
Especially if you are used to scroll on your smartphone or tablet. After a while,
I found it really strange to go back to the old way.

Since I run Windows on a separate partition, I can boot up a Windows computer on
my Mac and run it in parallell with OS X. Since natural scrolling doesn't exist
on Windows, I have to switch between natural and old-school scrolling when I
switch between OS X and Windows.

However, there is a farily easy way to setup natural scrolling in Windows as well.
Just follow these steps:

* If you have a Magic Mouse, first install Apple's
[Magic Mouse Utilities](http://www.trackpadmagic.com/magic-mouse/download)
to enable horizontal scrolling.

* Download and install [AutoHotkey](http://download.cnet.com/AutoHotkey-L/3000-2084_4-10279446.html) (free)

* When prompted, press **yes** to create a sample script.

* In the file that opens up in Notepad, add the following lines below the **#z**
and **^!n::** rows:

```text
#MaxHotkeysPerInterval 100000000000

WheelUp::
Send {WheelDown}
Return

WheelDown::
Send {WheelUp}
Return

WheelLeft::
Send {WheelRight}
Return

WheelRight::
Send {WheelLeft}
Return
```

The reason why I set `MaxHotkeysPerInterval` to a silly high number, is to avoid
the alert that is displayed if the number of received hotkeys exceeds this number.

After adding these lines, save the file and right-click the **AutoHotkey icon**
in the task bar. Click **"Reload this script"**. Natural scrolling should then be enabled.