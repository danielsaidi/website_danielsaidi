---
title: Reset Xcode load plugin bundles warning
date:  2016-09-05 17:42:00 +0100
tags:  xcode
icon:  swift

assets: /assets/blog/16/0905/
---

I accidentally clicked "Skip Bundles" instead of "Load Bundles" when I started Xcode after adding new plugins. This causes Xcode to not load plugins. How can we fix this?

Xcode obviously doesn't want you to load plugins, since the skip button is the default one:

![Xcode Load Plugin Bundles Warning Dialog]({{page.assets}}bundles.png)

If you made this mistake, you may have noticed that restarting Xcode will not help. Plugins will not load and you are never again prompted about whether or not to load them.

To load these plugins, you have open up the Terminal and run the following command:

```sh
defaults delete com.apple.dt.Xcode DVTPlugInManagerNonApplePlugIns-Xcode-7.3.1
```

If you restart Xcode, you will now be prompted about whether or not to load these bundles. This time, press "Load Bundles" and your plugins will be properly loaded.