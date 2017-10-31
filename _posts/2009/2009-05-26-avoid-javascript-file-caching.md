---
title:  "Avoid JavaScript file caching"
date:   2009-05-26 13:30:00 +0100
tags: 	javascript css cache`web
---


If you find yourself constantly clearing your browser cache because your browser
caches your JavaScript and CSS files, or receives bug reports from visitors that
visit your site and get served with old versions of your scripts and styles, you
may find this quick tip useful.


## While developing

To avoid having to clear the browser cache and force the browser to load CSS and
JavaScript files from disk at all times, simply add a timestamp to the file path,
for instance `site.js?ts=115421` instead of `site.js`.

This will cause the browser interpret the URL as a new file, which makes it load
the uncached file each time the page is loaded. Problem solved! :)

However, this is a solution that should not be used when the site goes live.


## When going live

The approach above is suitable for development, since bandwidth is no issue when
developing. However, browser caching is there for a reason - to reduce bandwidth
and reduce load times. We don't want our visitors to download all script and css
files every time they reload a certain page.

To force all users to load script file updates, you could rename the file. Still,
a more convenient alternative is to simply...add a timestamp to the file path. A
word of warning though - when in production, only update the timestamp when a file
has actually changed. As soon as you upload a new version of the js or css file,
update its timestamp.