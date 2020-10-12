---
title: "Show all files in Finder"
date:  2009-09-19 08:00:00 +0100
tags:  osx
---

A habit I've grown into while spending 80% of my time in front of a computer, is
to show hidden files in Explorer or Finder. However, Explorer or Finder will not
to so by default, so how to we bend them to our will?

In Windows Explorer, you can easily check a box to show all hidden files. In Mac
OSX, however, you have to type the following in a terminal window:

```
defaults write com.apple.finder AppleShowAllFiles TRUE
killall Finder
```

This will reconfigure and restart Finder, which upon a restart will now show you
all files, hidden or not. To revert and hide these files again, run this command:

```
defaults write com.apple.finder AppleShowAllFiles FALSE
killall Finder
```

An even easier way is to use the keyboard shortcut `Cmd+Shift+.`, which toggles
the visibility instantly. No terminal hacking required.