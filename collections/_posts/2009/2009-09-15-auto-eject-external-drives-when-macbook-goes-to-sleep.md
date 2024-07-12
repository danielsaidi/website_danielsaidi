---
title: Auto-eject external drives when Macbook goes to sleep
date:  2009-09-15 08:00:00 +0100
tags:  macos
icon:  avatar

link: http://www.macosxhints.com/article.php?story=20080329201951648
---

**Edit Aug. 28, 2010 -** `SleepWatcher` has been changed and now differs from the information found below. This approach still works, though, as does the modified script.

After switching from PC to Mac, I've been annoyed to having to eject external disks before putting my computer to sleep.

Luckily, I found a workaround. The link below has a link to an app called `SleepWatcher` and provides you with a nice collection of wakeup and sleep scripts:

[{{page.link}}]({{page.link}})

The script makes sure that any external disks are automatically ejected when the computer goes to sleep, which removes the need to do this manually.

However, while the original script will eject USB drives, mounted .dmg drives etc. as well, I have edited the sleep script to only eject a particular external drive.

```
#!/bin/sh
osascript -e 'tell application "Finder" to eject (disks where name = "type in the name of your disk here")'
```

With this script, the specific disk will now eject when the computer goes to sleep. No more annoying warning messages about first ejecting the external drive.