---
title:  "Enable USB Debugging on Android Devices"
date: 	2013-07-31 12:19:00 +0100
categories: apps
tags: 	android debugging
---


![Image of an Android teacher](/assets/img/blog/2013-08-05-android.png)


I am currently looking at Android Development. Despite programming for iOS for a
couple of years, I have never looked at Android except when doing some tutorials.

I am currently going through the [developer.android.com](http://developer.android.com)
docs to familiarize myself with all the tools, terms, acronyms etc. I also begun
setting up my Nexus 7 for development.

However, I had some problems to get USB debugging to work. Nothing happened when
I plugged the Nexus into my computer. It didn't show up in Android Studio, which
means that I could target it when building the app.

Turns out you have to enable Developer Mode on the Android device. However, this
feature is hidden by default on Android 4.2 and later.

To make it available, go to `Settings > About phone` and tap `Build number` seven
times. Android will then tell you `Congratulations, you are now a developer`. If
you now return to the previous screen, you will find a Developer options section.

Talk about a hidden option. I love it!