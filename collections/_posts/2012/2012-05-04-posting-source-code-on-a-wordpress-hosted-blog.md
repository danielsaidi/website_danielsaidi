---
title: Posting source code on a WordPress hosted blog
date:  2012-05-04 12:00:00 +0100
tags:  general
icon:  avatar
---

At the time of writing, this code-oriented blog was hosted on Wordpress. Since I
post a lot of code, I wanted the blog to have nice syntax highlighting. This is 
how you do it on Wordpress.

At first, the syntax highlighting on my Wordpress blog was pretty horrible. The
reason for this was that Wordpress hosted sites have limitations when it comes
to themes and plugins. Since I was yet to find a good way to handle code, I
wrapped code in a `pre` tag and highlighted it with a color.

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