---
title: Enable USB Debugging on Android Devices
date:  2013-07-31 12:19:00 +0100
tags:  android

image: /assets/blog/13/android.png
---

I'm currently getting started with Android Development. Today, let's take a look
at how to enable USB debugging on Android devices, which I had some problems with.

![Image of an Android teacher]({{page.image}})

I'm going through the [developer.android.com](http://developer.android.com) docs to familiarize myself with all the tools, terms, acronyms etc. and have started setting up my Nexus 7 for development. However, nothing happened when I plugged the Nexus into my computer. It didn't even show up in Android Studio, which means that I couldn't use it to run or debug my Android app.

Turns out that you have to enable Developer Mode on Android devices. However, this is hidden by default on Android 4.2 and later.

To make this available, go to `Settings > About phone` and tap `Build number` seven times. Android will then tell you `Congratulations, you are now a developer` (funny!). If you return to the previous screen, you have a new Developer section. 

Enabling developer mode also lets the device connect over USB. Talk about hidden option!