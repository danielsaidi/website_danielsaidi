---
title: Automating with Automator
date:  2013-10-08 19:42:00 +0100
tags:  macos automation

assets: /assets/blog/13/1008/
image:  /assets/blog/13/1008.png
---

A useful tool that is never top of mind for me when automating a workflow, is the native OS X (today macOS) Automator app.

![Automator icon]({{page.image}}){:class="plain"}

Automator lets you create workflows in a convenient way. For instance, I use it to do batch image resizing of many, MANY images in a game that I'm currently building.

Instead of doing it manually, I created an Automator workflow that takes all my retina iPad images and resizes and crops them to four target sizes. The entire workflow completes in less than a minute, instead of me having to do it manually, over and over again.

One thing that Automator doesn't support out of the box, is to crop an image in other ways than around the center. For this, I found a nice add-on called `Proper Cropper`. It let's you crop in more ways than around the center, which is just what I need.

It however turned out to only support PC, so I used the `ImageMagick` instead. It lets me run it as a shell script in my workflow, which takes some weight of the non-developer drag-and-drop guilt I so often feel when using Automator. I now use this add-on and it works great.