---
title: PHP magic quotes
date:  2010-11-24 12:00:00 +0100
tags:  php
icon:  php
---

I've had problems when sending data to a PHP page, using AJAX. To unpack the data, I had to use `stripslashes`. It seemed to work, and I was happy, until the world exploded.

I then developed a new site for a company, and just noticed that when posting HTML via AJAX in production, all line feeds turns into `u000a`:

![Magic quotes](/assets/blog/10/1124.png "Magic quotes in action")

This only happened in production, not anywhere else. Then, yesterday, it started appeared on one of my production servers as well. 

I didn't understand what happened, but realized that it must have something to do with the server configuration. I had a chat with a friend and found the solution.

Magic quotes.

Magic quotes was meant to protect web sites from script injections, but since it didn't work that well, that feature is now deprecated. So it's not available everywhere.

What magic quotes does is wrapping everything in escape slashes, which is why I had to use `stripslashes` when sending data with AJAX.

However, when magic quotes is disabled, stripping slashes instead destroys the escaped line break, which gives the result that can be seen in the image above.

To ensure that magic quotes is disabled everywhere, you can add this to `.htaccess`:

```
php_flag magic_quotes_gpc off
```

Thanks Mattias Sundberg! It works like a charm!
