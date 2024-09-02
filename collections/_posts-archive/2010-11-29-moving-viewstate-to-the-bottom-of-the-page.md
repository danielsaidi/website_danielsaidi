---
title: Moving ViewState to the bottom of the page
date:  2010-11-29 12:00:00 +0100
tags:  archive

post: http://www.dotnetspider.com/resources/786-Move-ViewState-e-bottom-e-page.aspx
---

ASP.NET's `ViewState` is a way to simulate state in a stateless web environment. It's a cool technology, but is unfortunately easy for developers to misuse.

`ViewState` is a bag of bytes that is sent back and forth between a client and the server on each page load. It's posted by the page, using a single form.

When the server receives a page requests and generates the result, it can deserialize any data that is stored in `ViewState`. It then sends the state back in the same form. 

This means that the client and server will send and receive the view state for each request. This is a convenient to simulate state, but must be used with caution.

For instance, everything you store in it adds to the page size. If a user visits your site on a mobile device, it's not great to sent megabytes of data for each request. 

I've actually seen teams misusing this to send 40 MB back and forth for each request! 😱

So that's one learning - don't store things in `ViewState` out of convenience - it has a price!

Furthermore, view state is by default sent topmost in the web page, in a hidden text field in which the encoded data is stored. If the size of this state bag grows, this other content will take time to load, since it has to wait for the view state bytes to be sent. 

In a project that I'm working on, we managed to speed up page rendering significantly by moving the view state element to the bottom. [This page]({{page.post}}) explains how easy this is to do.

I adjusted the script and moved it to our master page. We have also moved all JavaScript files to ensure that nothing is delaying page rendering. The result is a site that appears to load much faster, although it continues to load after the page is rendered. 

Making websites load fast is very important. It's good for users and more friendly to search robots, which will hopefully boost both sales and page rank.