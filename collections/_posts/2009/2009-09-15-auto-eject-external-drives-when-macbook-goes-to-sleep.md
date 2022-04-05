---
title: Auto-eject external drives when Macbook goes to sleep
date:  2009-09-15 08:00:00 +0100
tags:  macos

link: http://www.macosxhints.com/article.php?story=20080329201951648
---

**Edit Aug. 28, 2010** `SleepWatcher` has been changed since I wrote this post
and now differs from the information found in the link below. This approach does
still work, though, as does the modified script.

Since switching over from PC to Mac, I've always been annoyed that I manually
have to eject external disks before putting the computer to sleep.

Luckily, I found a workaround. The link below has a link to a nice program called
`SleepWatcher` and provides you with a nice collection of wakeup and sleep scripts:

[{{page.link}}]({{page.link}})

The scripts will make sure that your external disk is automatically ejected when
your computer goes to sleep. However, while the original script will eject USB
drives, mounted .dmg drives etc. as well, I have edited the sleep script to only
eject a particular external drive.

```
#!/bin/sh
osascript -e 'tell application "Finder" to eject (disks where name = "type in the name of your disk here")'
```

Voilá – your external drive will now eject when your computer goes to sleep. No
more annoying warning messages about first ejecting the external drive.