---
title: ImageGen Cache Problem
date:  2010-12-21 12:00:00 +0100
tags:  archive

post:   http://our.umbraco.org/projects/website-utilities/imagegen/imagegen-bugs/2982-%5BUPDATE%5D-Fix-to-caching-bug-in-201
file:   http://www.hajslund.com/blog.aspx?filterby=ImageGen
---

I am looking to use Umbraco ImageGen in a project that I'm currently working on. 
People who have used it really seem to like it, so I look forward to try it out.
However, I did run into a cache problem.

Turns out that the over one year old 2.0.1 release has a cache issue, which is
described in detail [here]({{page.post}}).

This problem causes the cache folder to grow and grow, which is not great. The
solution involves downloading a file and replacing parts of the 2.0.1 release.
Unfortunately, the file link is dead. 

After some searching, I found another copy of the file [here]({{page.file}}). 
Grab that one and you should be good to go.