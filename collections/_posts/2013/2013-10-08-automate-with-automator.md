---
title: Automate with Automator
date:  2013-10-08 19:42:00 +0100
tags:  macos automation

image: /assets/blog/2013/2013-10-08-automator.png
---

A really useful tool that is never top of mind for me when automating a workflow,
is the native OS X application Automator.

![Automator icon]({{page.image}})

With Automator, you can create almost anything workflow-related in a convenient
way. For instance, in an iOS app of mine, I use several screen-sized images to 
make up a background in layers. Since the app is universal, I need one image for 
old non-retina iPhones, one for old retina iPhones, one for new retina iPhones, 
one for non-retina iPads and one for retina iPads etc. That means five images per
layer. A background view with ten layers means scaling and cropping fifty times.

Instead of doing this manually, I created an Automator workflow that takes all my
retina iPad screens, then resizes and crops it to the four remaining sizes. The
entire workflow completes in less than one minute, instead of me havng to do this
repetitive task manually and much, MUCH slower.

One thing that Automator doesn't support out of the box, though, is to crop an
image in other ways than around the center. For this, I found a nice add-on that
is called `Proper Cropper`. It let's you crop in more ways than around the center,
which is just what I need, but turned out to only supports PC.

Instead, the `ImageMagick` add-on seems to be a god fit for the Mac. It lets me
run it as a shell script in my workflow, which takes some weight of the
non-developer drag-and-drop guilt I so often feel when using Automator. I now
use this add-on to great extent and it works great.