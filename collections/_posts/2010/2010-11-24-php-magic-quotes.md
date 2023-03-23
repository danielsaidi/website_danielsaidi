---
title: PHP magic quotes
date:  2010-11-24 12:00:00 +0100
tags:  archive
icon:  php
---

About a year ago, I had problems when sending data to a php page, using AJAX. To
be able to unpack the data, I had to use `stripslashes`. Since it seemed to work, 
I was happy...until the world exploded.

I then developed a new web site for a company about a month ago, and just noticed
that when posting HTML via AJAX in production, all line feeds turns into `u000a`:

![Magic quotes](/assets/blog/2010/11-24.png "Magic quotes in action")

This only happened on their production server, not anywhere else. Then, yesterday,
it started appeared on one of my production servers as well. 

I first didn't understand what happened, but realized that it must have something
to do with the server's php version or configuration. I had a chat with a friend
and came up with the solution.

Magic quotes.

The purpose of magic quotes is to protect web sites from script injections, but
since it didn't work all that well, the feature is now deprecated. What it does
is wrapping everything in escape slashes, which is why I had to use `stripslashes`
when sending data with AJAX.

However, when magic quotes are disabled, stripping slashes will instead destroy
the escaped line break, which gives the result that can be seen in the image above.

So, I am now disabling magic quotes for all sites, using this `.htaccess` line:

```
php_flag magic_quotes_gpc off
```

Thanks Mattias Sundberg! It works like a charm!
