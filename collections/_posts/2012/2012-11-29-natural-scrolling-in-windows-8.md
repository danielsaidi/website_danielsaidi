---
title: Natural Scrolling in Windows 8
date:  2012-11-29 23:03:00 +0100
tags:  archive

image:  /assets/blog/12/1129.jpg
---

Apple introduced `natural scrolling` in OS X Lion, which makes the scroll content move as if it was a sheet of paper that you press and dragged around with your finger.

![Apple Mouse]({{page.image}})

If you haven't tried natural scrolling yet, you may think that this is how you have scrolled all along, but the traditional way of scrolling is actually inverted.

Although natural scrolling may feel strange at first, I quickly got used to it, and immediately loved it. It's just like swiping on your smartphone or tablet. I find it strange to go back.

Since I run Windows on a separate partition on my Mac, I can boot into Windows and run it in parallel with OS X. But since natural scrolling doesn't exist on Windows, I have to switch between natural and old-school scrolling when I switch between OS X and Windows.

However, there's an easy way to setup natural scrolling in Windows as well, to make these two operating systems behave in the same way.

To enable natural scrolling in Windows, just follow these steps:

* If you have a Magic Mouse, first install Apple's
[Magic Mouse Utilities](http://www.trackpadmagic.com/magic-mouse/download)
* Enable horizontal scrolling in the Magic Mouse app.
* Download and install [AutoHotkey](http://download.cnet.com/AutoHotkey-L/3000-2084_4-10279446.html) (free)
* When prompted, press yes to create a sample script.
* In the file that opens up, add the following lines below the `#z` and `^!n::` rows:

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

The reason why I set `MaxHotkeysPerInterval` to a silly high value, is to avoid the alert that is displayed if the number of received hotkeys exceeds this number.

After adding these lines, save the file and right-click the **AutoHotkey icon** in the task bar. Click **"Reload this script"**. Natural scrolling should then be enabled.