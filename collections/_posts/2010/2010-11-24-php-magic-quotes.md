---
title: PHP magic quotes
date:  2010-11-24 12:00:00 +0100
tags:  php
---

About a year ago, I had problems when sending data to a PHP page, using AJAX. In
order to be able to unpack the data, I had to use `stripslashes`, which does not
seem good. However, since it worked, I let it be without further considerations.

Then, about a month ago, I published a new web site. When I posted HTML via AJAX
on that site, all line feeds turned into u000a strings. The problem only occured
on his domain, and not on mine, and looked like this:

![Magic quotes](/assets/blog/2010/11-24.png "Magic quotes in action")

Then yesterday, the problem appeared on my domain as well. I did not understand
this at all, until I had a chat with a friend of mine, who just brainstormed and
came up with the solution.

Magic quotes.

The purpose of magic quotes is to protect web sites from script injections, but
since it did not work all that well, the feature is now deprecated. What it does
is wraping everything in escape slashes, which is why I had to use stripslashes.

However, if magic quotes are disabled (which it should, since it is deprecated),
stripping slashes will instead destroy the escaped line break.

So, I am now disabling magic quotes for all sites, using this `.htaccess` line:

```
php_flag magic_quotes_gpc off
```

Thanks Mattias Sundberg! It works like a charm!
