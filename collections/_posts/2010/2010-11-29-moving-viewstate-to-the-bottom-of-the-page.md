---
title: Moving ViewState to the bottom of the page
date:  2010-11-29 12:00:00 +0100
tags:  .net web
---

`ViewState` is ASP.NET's way of simulating state in the otherwise state-less web
environment. It is a bag of bytes that is sent back and forth between the client
and server. It is then deserialized by the server, which can use it to restore a
previous state in its components.

However, `ViewState` comes with a price (many, in fact), and you need to use the
technoology with cautios. For instance, since it is a *bag of bytes*, everything
you store in it adds to the page size.

View state is by default sent topmost in the web page, in a hidden text field in
which the encoded data is stored. If the view state grows, this means that other
content will be pushed down, which may cause content to load slower since it has
to wait for the bytes to be sent. In a project that I'm currently working on, we
managed to speed up page rendering a lot by moving view state to the bottom.

This is quite straightforward to do. I followed [this tutorial](http://www.dotnetspider.com/resources/786-Move-ViewState-e-bottom-e-page.aspx)
and was good to go in a minute or so.

However, I adjusted the script a bit and moved it to our master page as well. We
have also moved all JavaScript files as well, to ensure that nothing is delaying
page rendering. The result is a page that appears to load faster, although it is
loading after the page is rendered as well. This is also more friendly to search
robots, hopefully boosting your rank.