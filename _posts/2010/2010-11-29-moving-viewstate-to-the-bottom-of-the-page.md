---
title:	"Moving ViewState to the bottom of the page"
date:	2010-11-29 12:00:00 +0100
categories: dotnet
tags: 	asp-net viewstate
---


There are a number of reasons to why this is a good idea. For instance, we chose
to do so to speed up page rendering in a project that I'm currently working with.

It's really quite straightforward to achieve this. I followed this tutorial, and
was good to go in a minute or so:

[http://www.dotnetspider.com/resources/786-Move-ViewState-e-bottom-e-page.aspx](http://www.dotnetspider.com/resources/786-Move-ViewState-e-bottom-e-page.aspx)

However, I adjusted the script a bit and moved it to our master page as well. We
have also moved all JavaScript files as well, to ensure that nothing is delaying
page rendering. The result is a page that appears to load faster, although it is
loading after the page is rendered as well. This is also more friendly to search
robots, hopefully boosting your rank.