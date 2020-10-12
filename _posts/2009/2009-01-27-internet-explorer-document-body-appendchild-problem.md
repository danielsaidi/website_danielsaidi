---
title: Internet Explorer document.body.appendChild(...) problem
date:  2009-01-27 08:46:00 +0100
tags:  html javascript web
---

On my spare time, I develop a PHP web application on OS X and thus test my sites
in Firefox, Safari and Opera on a daily basis. Every once in a while, however, I
also verify that the code works in Internet Explorer.

Not too surprisingly, IE often crashes where other browsers do not (including my
iPhone browser). The reasons for the crashes can be most peculiar.

One issue occured after I added a PopupElement control to a site. The PopupElement
control makes it possible to display a div as a popup and automatically append a
semi-transparent background element to the page.

This control worked perfectly in all browsers that I tested it on. However, when
I eventually also tested it in Internet Explorer, I found that Internet Explored
did not like the following piece of JavaScript at all:

{% highlight javascript %}
document.body.appendChild
{% endhighlight %}

As I tried to access the site, Internet Explorer 6 refused to load the page, and
simply alerted that **"The operation was aborted"**.

It turns out that I appended the background "incorrectly", according to IE 6. If
`document.body.appendChild(...)` is executed within the body tag before the body
is closed, IE 6 will simply not load the page.

I have only had this problem in IE 6, not in any other browsers and not in later
versions of IE. It is most irritating, but still manageable.

To handle this situation, you can:

* wait until the body is loaded, using `body.onload`
* append the element to another element, instead of the body tag

Waiting for the body to load is not an option in many cases, since it causes the
logic to halt while waiting for the event. It is an ugly workaround that changes
the initial behavior, so I really do not recommend it.

Instead, I use the second option. Appending elements to any other element works
great and does not require you to change anything in how your page is loaded by
Internet Explorer 6.