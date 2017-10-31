---
title:  "Natural Scrolling in Windows 8"
date:	2012-11-29 23:03:00 +0100
tags: 	windows
---


![Apple Mouse](/assets/img/blog/2012-11-29-mouse.jpg)


In OS X Lion, Apple introduced natural scrolling. It means that when you scroll,
the scrollable content will move around like it was a sheet of paper you pressed
and dragged around with your finger. 

If you read the lines above, but have not tried natural scrolling yourself, you
may think that this is how you have scrolled all along. However, the traditional
way of scrolling is inverted.

Although natural scrolling feels strange at first, you quickly get used to it...
especially if you are used to scroll on your smartphone or tablet. After a while,
I found it really strange to go back to the old way of scrolling.

Since I run Windows on a separate partition, I can boot up a Windows computer on
my Mac and run it in parallell with OS X. Since natural scrolling does not exist
on Windows, I thus have to switch between natural and old-school scrolling when I
switch between OS X and Windows.

However, there is a farily easy way to setup natural scrolling in Windows as well.
Just follow these steps:

* If you are using a Magic Mouse, first make sure that you have installed Apple's
[Magic Mouse Utilities](http://www.trackpadmagic.com/magic-mouse/download) (free)
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
in the task bar. Click **"Reload this script"**.

Natural scrolling should now be enabled.