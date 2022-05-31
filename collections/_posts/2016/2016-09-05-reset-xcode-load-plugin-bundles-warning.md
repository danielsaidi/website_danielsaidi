---
title: Reset Xcode load plugin bundles warning
date:  2016-09-05 17:42:00 +0100
tags:  xcode
icon:  swift
---

Today, I accidentally clicked "Skip Bundles" instead of "Load Bundles" when I started
Xcode after adding new plugins. This cause Xcode to not load the plugins on subsequent
launches. How can we fix this?

Xcode obviously doesn't want you to load custom plugins, since the blue skip button is 
the default one:

![Xcode Load Plugin Bundles Warning Dialog](/assets/blog/2016/2016-09-05_bundles.png)

If you also made this mistake, you may have noticed that restarting Xcode will not help.
The plugins will not load and you are never again prompted about whether or not to load
them.

If you really want to load these plugins, you have open up the Terminal and run the
following command:

```sh
defaults delete com.apple.dt.Xcode DVTPlugInManagerNonApplePlugIns-Xcode-7.3.1
```

If you restart Xcode, you will now be prompted about whether or not to load these 
bundles. This time, press "Load Bundles" and your plugins will be properly loaded.