---
title:  "Avoid empty img src values"
date:   2009-01-26 08:00:00 +0100
tags:	html performance web
---


A while ago, I was assigned to optimize a really slow web site. The problems were
numerous, like loading all content of an expandable dashboard with tons of data at
once, instead of when the user expanded each section.

However, while these problems were obvious, expected and easy to solve, one quite
unexpected performance thief was that the web application used xslt files with image
tags that did not handle missing image src values. Instead of just not rendering the
image tags that lacked src (or use a fallback image), the image tags were rendered
with `src=""`.

So, what happens when you have a page with a lot of image tags with empty src? At
first, you may think that these images will simply not load an image. However, what
really happens is that the images *will try to load the default page of the current
folder*.

For instance, consider an image at http://www.mywebsite.com/contact/contact-us.html.
If the src attribute is left empty, the image will not be “not set”, but instead try
to load http://www.mywebsite.com/contact.

So, if you have a ASP.NET, PHP or any kind of non-static site, where a lot of data
fetching goes on (this blog post was written a long time ago, but this is still true),
having a bunch of images trying to load e.g. the start page, where a lot of data is
being pulled out from the database, will really cripple your site.

To wrap things up, simply do not use empty src attributes for your images! If you do
not want to load an image due to various reasons, just omit the image tag altogether
or make sure to use a fallback value, an 1×1 pixel image or similar.