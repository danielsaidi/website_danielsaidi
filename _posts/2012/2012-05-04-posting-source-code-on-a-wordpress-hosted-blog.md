---
title: Posting source code on a WordPress hosted blog
date:  2012-05-04 12:00:00 +0100
tags:  wordpress
---

As you probably have noticed, this is a code-oriented blog that I host on Wordpress.
Since I post a lot of code, I have been annoyed at the terrible syntax highlighting
for quite some time now.

The reason for the bad highlighting is that Wordpress hosted sites have limitations
when it comes to themes and plugins. Since I have not yet found a good way to handle
code here, I have wrapped the code in pre tags and highlighted it with a color, but
the result has been awful.

A couple of weeks ago, I found a post by the WordPress team that descibes how to get
nice syntax highlighting. It turns out it is very easy, using the sourcecode markup:

```
[code language=”css”]
your code here
[/code]
```

If you use this markup, WordPress will format the source code correctly. It supports
many different languages, e.g. C#, Java, JavaScript, CSS, XML etc. Have a look at
[this blog post](http://en.support.wordpress.com/code/posting-source-code/) for examples.

Thanks, WordPress! 