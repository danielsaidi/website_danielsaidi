---
title:  "Fix broken network in Catalina"
date:   2019-08-04 12:00:00 +0100
tags:   catalina beta swiftui
icon:   swift
---

In this post, I'll show how to restore the network configuration in macOS Catalina beta 5, if it suddenly stops working. It happened to me and the solution was to just delete a couple of files.


I immediately upgraded to macOS Catalina beta 5 when it was released. It fixed a bunch of perfomance issues and Xcode compatibility problems, so I was very happy with this upgrade.

However, after leaving the computer for an hour, the Internet connection was suddenly dead. I use both tethered and WiFi connections at work, and none of these connections worked anymore.

Nothing I did to restore them had any affect. I tried forgetting and reconnecting to the WiFi network, recreated the tethered and WiFi connections in Network Preferences and did even reset PRAM and SMC, but nothing worked.

After talking to IT and Apple Support without result, I decided to try an approach that I found regarding another Catalina problem: deleting preference files in `/Library/Preferences`.

Based on their names, I tried deleting the following files:

```
com.apple.networkextension.necp.plist
com.apple.networkextension.plist
com.apple.networkextension.uuidcache.plist
SystemConfiguration/com.apple.wifi.message-tracer.plist
SystemConfiguration/NetworkInterfaces.plist
SystemConfiguration/preferences.plist
```

I rebooted the computer after deleting these files, and to my delight the network worked once again.

You probably only have to delete one or some of these files, but macOS recreates them on reboot and I haven't noticed any side-effects with resetting all of them. If you also run into these problems, I hope that this will help you!