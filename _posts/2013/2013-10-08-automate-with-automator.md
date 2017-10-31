---
title:  "Automate with Automator"
date: 	2013-10-08 19:42:00 +0100
tags: 	automator
---


![Automator icon](/assets/img/blog/2013-10-08-automator.png)

A really useful tool, that never pops into my Windows-shaped mind (even though I
have used Macs for almost ten years now) when it comes to automating a workflow,
is the native OS X application Automator.

With Automator, you can create almost anything workflow-related, and in a really
convenient way. For instance, in an iOS app of mine, I use several screen-sized
images to make up a background in layers. Since the app is universal, I need one
image for old non-retina iPhones, one for old retina iPhones, one for new retina
iPhones, one for non-retina iPads and one for retina iPads etc. That means five
images...per layer. A background view with ten layers means scaling and cropping
fifty times.

Instead of doing this manually, I have created an Automator workflow, that takes
all my nice retina iPad screens, then resizes and crops it to the four remaining
sizes. The entire workflow completes in less than one minute, instead of me doing
this repetitive task manually and much, MUCH slower.

One thing that Automator does not support out of the box, though, is to crop an
image in other ways than around the center. For this, I found a nice add-on that
is called Proper Cropper. It let's you crop in more ways than around the center,
which is just what I need...but it turned out that it only supports PC.

So it seems like the Homebrew ImageMagick addon is what I'm looking for. It also
lets me run it as a shell script in my workflow, which takes some weight of the
non-developer drag-and-drop guilt I so often feel when using Automator.