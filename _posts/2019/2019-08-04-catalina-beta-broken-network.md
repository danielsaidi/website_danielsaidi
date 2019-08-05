---
title:  "Fix broken network in Catalina"
date:   2019-08-04 12:00:00 +0100
tags:   catalina beta swift-ui
---

In this post, I'll show you how to restore the network configuration in Catalina (beta 5), if it suddenly stops working. It happened to me and the solution was to just delete a couple of files.

I upgraded to Catalina beta 5 immediately as it was released. The new beta fixed a bunch of perfomance issues and Xcode compatibility problems from the previous beta, so I was very happy with this upgrade.

However, after leaving the computer idle for an hour, the Internet connection was suddenly completely dead. I use both tethered and WiFi connections at work, and now none of these connections worked and nothing I did to restore them had any affect.

I tried forgetting the WiFi network and reconnecting to it, recreated both the tethered and WiFi connections in Network preferences and tried resetting PRAM and SMC, but nothing worked. However, replacing my USB adapter with a USB-C adapter did work...but enabling WiFi made it stop working once more.

After contacting my company's IT departement and Apple Support without any positive result, I decided to try an approach that I found regarding another Catalina problem - deleting preference plist files in `/Library/Preferences`.

Based on the file names, I tried deleting the following files:

```
com.apple.networkextension.necp.plist
com.apple.networkextension.plist
com.apple.networkextension.uuidcache.plist
SystemConfiguration/com.apple.wifi.message-tracer.plist
SystemConfiguration/NetworkInterfaces.plist
SystemConfiguration/preferences.plist
```

After rebooting the computer, the network now worked once more.

You most probably only have to delete one or two of these files, but macOS recreates them when you reboot and I haven't noticed any strange side-effects with resetting all of them.

If you also run into network problems in Catalina, I hope that this will help you!