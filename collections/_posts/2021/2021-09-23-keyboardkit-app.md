---
title:  Building the KeyboardKit app
date:   2021-09-23 07:00:00 +0100
tags:   swiftui
---


In this post, I'll discuss the development of my new [KeyboardKit](http://) app for iOS and iPadOS. I'll go through my first release vision, the actual outcome as well as a bunch of findings, problems etc.


IMAGE HERE


## Background

Many years ago, an artist friend contacted me regarding wanting to create a custom keyboard with her own Goth artwork. The project was a quick one, and my first experience with developing custom keyboard extensions.

Although the app was basic, I learned a lot about custom keyboard extensions, and found the pretty restricted context both fascinating and frustrating. The basic proxy api lacked a lot of functionality and Apple's rules that applied to extensions (main app must have some functionality, the keyboard must be functional without full access etc.) were tricky to work around.

The app only took a few nights to build, launched as [Goth Emoji](xxx) and gave me my first (and only) semi-viral launch experience, with the app trending for a few days.

GOTH EMOJI IMAGE

Although the first virality faded quickly, the app got me started on something that actually would turn out to change my life. 

After the release, we wanted to launch a second app with artwork that didn't fit the first app. As I started developing the "sequel", [Metal Emoji](xxx), I realized that the best course of action would be to reuse as much as possible.

METAL EMOJI IMAGE

As I started moving reusable code and components out of the Goth Emoji app, into a shared container, I realized

Say hi to KeyboardKit.


## KeyboardKit (the library)

