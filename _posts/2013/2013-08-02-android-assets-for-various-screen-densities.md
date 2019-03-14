---
title:  "Android Assets for Various Screen densities"
date: 	2013-08-02 12:08:00 +0100
tags: 	android
---


![Counter](/assets/blog/2013-08-05-android.png)


Android devices come in a great deal of different flavors. You have slow devices,
fast devices, crappy devices, great devices, lowres devices and ultra-hd devices.

When you want your app to look great in various screen densities, the app's image
assets should be exported for the following densities:

* MDPI
* HDPI
* XHDPI
* XXHDPI

These densities use a **2:3:4:6 scaling ratio**, which means that

* 1 MDPI = 1 MDPI
* 1 HDPI = 1.5 MDPI
* 1 XHDPI = 2 MDPI
* 1 XXHDPI = 3 MDPI

For instance, the launcher icon is **48, 72, 96 or 144** pixels wide (mdpi, hdpi,
xhdpi, and xxhdpi), depending on the screen density.

If you look at the [Android Developer Iconography Page](http://developer.android.com/design/style/iconography.html),
it suggests that you should create your launcher icon in **864x864** pixels. This
will provide you with an image that can be evenly divided to the various sizes.

This means that when you design artwork for an app, you should either use vector
based assets that can be exported in any resolution, or create original graphics
to be **18 times larger** than the MDPI pixel size (864/18 = 48). This results in
an image asset that can be evenly divided for all available screen densities.