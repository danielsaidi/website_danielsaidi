---
title:  "jQuery Flash MP3 player download error"
date:   2009-05-17 21:13:00 +0100
tags: 	jquery javascript web
---

I am currently having problems with using the Single MP3 Player with the jQuery
Flash plugin. To see how the plugin works, check out [this page](http://jquery.lukelutman.com/plugins/flash/example-mp3.html).

The web site I'm working with has the following folder structure:

* **swf/singlemp3player.swf** - contains the MP3 player
* **mp3/...** - contains all mp3 files

The player can play all files, but it can not download them. The download button
just does not work.

After some experimenting, I realized that the player must be placed in the same
folder as the MP3 files for downloads to work.

Having the MP3 files and the player in a flat structure is really not an option
for me. I could solve the problem by moving things around, but prefer to have a
separation of content.

Has anyone used this plugin and solved this problem?