---
title:  "Strict DocType - here I come!"
date:   2009-01-29 08:17:00 +0100
categories: web
tags: 	html css
---


After some initial struggles, I'm now on track with strict XHTML. It's fun to pay
attention to the code you write and make sure that it follows certain rules. Then
see it validate.

Still, the main reason to why it took some time for me to get this going, is that
I am sick and tired of creating web pages that look great in almost all browsers,
just to see it collapse into a total mess when opening it in IE.

So, after a night of strictifying (1.0) the code of a site that is soon to be up,
a few things I've had to change:

* img elements must have an "alt" attribute
* target="_blank" is not allowed for a tags
* divs, spans etc. have no "name" attribute
* input elements cannot be added directly to the form tag, but must be nested in another element, like a div

These were the major things I had to fix in my web application. I don't like that
`target="_blank"` is no longer allowed (fix it with rels and javascript), but it
is nice to be forced to remove presentational elements from the HTML code.