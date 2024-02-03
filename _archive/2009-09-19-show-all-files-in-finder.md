---
title: Show all files in Finder
date:  2009-09-19 08:00:00 +0100
tags:  archive
icon:  avatar
---

This post will show you how to easily show all hidden files in Finder, both with
a Terminal script and a keyboard shortcut.

To show hidden files in OS X, you can type the following in the Terminal:

```
defaults write com.apple.finder AppleShowAllFiles TRUE
killall Finder
```

This will reconfigure and restart Finder, which upon a restart will now show you
all hidden files. To hide these files again, just run this command:

```
defaults write com.apple.finder AppleShowAllFiles FALSE
killall Finder
```

An even easier way is to use the keyboard shortcut `Cmd+Shift+.`, which toggles
the visibility instantly. No Terminal hacks required.
