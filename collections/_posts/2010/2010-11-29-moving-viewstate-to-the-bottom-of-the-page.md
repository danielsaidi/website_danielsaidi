---
title: Moving ViewState to the bottom of the page
date:  2010-11-29 12:00:00 +0100
tags:  dotnet
icon:  dotnet

post: http://www.dotnetspider.com/resources/786-Move-ViewState-e-bottom-e-page.aspx
---

`ViewState` is ASP.NET's way of simulating state in the otherwise state-less web
environment. It's a cool technology, that is however easy to misuse.

`ViewState` is basically a bag of bytes that is sent back and forth between the 
client and the server. It's posted by the page, using a huge, single form, then
deserialized by the server. This means that you can put stuff in it to persist
state between page loads.

However, `ViewState` comes with a price (many, in fact), and you need to use it
with caution. For instance, since it is a *bag of bytes*, everything you store
in it adds to the page size. If a user visits your site on a mobile device, it's
not that great to sent megabytes of data to persist state that you could as well
re-fetch on the server. 

I've actually seen teams misusing this technology to send 40 MB back and forth
for each page load! 😱

So that's one learning - don't store things in this view bag out of convenience -
it has a price!

Furthermore, view state is by default sent topmost in the web page, in a hidden
text field in which the encoded data is stored. If the size of this state bag
grows, this means that other content will be pushed down, which may cause content
to load slower, since it has to wait for the view state bytes to be sent. 

In a project that I'm currently working on, we managed to speed up page rendering
significantly by moving the view state element to the bottom. [This page]({{page.post}})
explains how to do it in just a minute or so.

I adjusted the script a bit and moved it to our master page. We have also moved
all JavaScript files to ensure that nothing is delaying page rendering. The result
is a site that appears to load much faster, although it actually continues to load
after the page is rendered. 

Making web pages load fast is very important. It's good for users and more 
friendly to search robots, which will hopefully boost both sales and page rank.