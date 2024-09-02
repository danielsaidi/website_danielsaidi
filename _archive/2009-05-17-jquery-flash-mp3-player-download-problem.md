---
title: jQuery Flash MP3 player download error
date:  2009-05-17 21:13:00 +0100
tags:  archive
icon:  javascript
---

I am currently having problems with the Single MP3 Player with the jQuery Flash
plugin, which fails to download files in other folders.

The web site that I'm currently building has the following folder structure:

* **swf/singlemp3player.swf** - contains the MP3 player
* **mp3/...** - contains all mp3 files

With this structure, it turns out that the Single MP3 Player can play all files,
but can't download them. The download button just doesn't work.

After some experimenting, I found that the player must be placed in the same
folder as the MP3 files, for downloads to work. However, having the MP3 files and
the player in a flat structure is really not an option for me. I could solve the 
 by moving things around, but prefer to have separation of content.

Has anyone used this plugin and solved this problem?