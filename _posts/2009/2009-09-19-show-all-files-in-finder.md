---
title:  "Show all files in Finder"
date:   2009-09-19 08:00:00 +0100
tags:	osx
---


A habit I've grown into as someone who spends 80% of his time awake in front
of a computer, is to prefer seeing all files in a folder when I use Explorer
or Finder. However, Explorer or Finder have grown into an equal habit of not
showing me all files in a folder. So, how to bend them to our will?

Well, in Windows, you can easily check a box to show all hidden files. In OS
X, however, you have to type the following in a terminal window:

```
defaults write com.apple.finder AppleShowAllFiles TRUE
killall Finder
```

This will restart Finder, which upon restarting will now show you all files,
hidden or not. To revert and hide these files again, run this command:

```
defaults write com.apple.finder AppleShowAllFiles FALSE
killall Finder
```

Hope it helps!
