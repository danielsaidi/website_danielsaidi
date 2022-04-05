---
title: Automatically convert media links with jQuery
date:  2009-04-16 22:25:00 +0100
tags:  javascript jquery web
---

This post will show you how to automatically convert media links with jQuery, for
instance audio links to a Flash-based audio player.

I'm currently building a PHP-based framework that can be used to create web sites
with a database and PHP classes that auto-generate corresponding JavaScript
types that trigger the same functionality in an asynchronous way. It also comes
with a CMS that lets you add pages with text, images, links and any data you like.

As I now use this framework to create a personal web site, which contains a bunch
of links to MP3 files and YouTube clips, I want to let visitors play this content
directly from the web site. I therefore want to convert these links to proper media
players, depending on the content type. 

Imagine my excitement, when I found a jQuery Flash and Media plugins, that convert
"a" tags that refer to MP3 files or YouTube clips to a music/video player! I use it
to automatically convert any `a` tag that uses the `media` class to the proper media
player, based on the media type.

The Flash plugin has been discontinued, but the jQuery media plugin is still up:

[http://malsup.com/jquery/media](http://malsup.com/jquery/media)