---
title:	"Automatically convert HTML5 to HTML4"
date:	2011-01-27 12:00:00 +0100
tags: 	html4 html5 httpmodule iis web
---


I love HTML5 and how easy things will become once it breaks through. However, it
will take time for many browsers to support HTML5 and until they do, the code we
write must be supported by older browsers as well.

Still, we want to write HTML5 code today, right? So, how do we solve this dilemma?

To make it possible to write HTML5 and still support older browsers, I created a
class that automatically converts most new HTML5 elements to HTML4 elements when
the response is sent to the client.

For instance, <nav>...</nav> is translated to:

	...

for Internet Explorer 8 (which does not support HTML5), while Chrome and Firefox
will receive the original HTML5 code (since they support it).


## The basic implementation

First of all, HTML5 support is determined by one class, which implements a small
interface. If the browser is considered to **not** support HTML5, the HTML5 code
is converted to HTML4, using another small class.

The interface-based setup aims at making it easy to replace these quickly created
implementations with better ones, if they prove insufficient. For instance, maybe
you want to use a complete list of non-HTML5 browsers (here, we only consider IE8
and below to not have HTML5 support) and perhaps do not want the class name to be
applied to the end tag (which the current implementation adds).

If you feel like improving these classes, feel free to send a pull request with a
better implementation 🙂



## The ugly test hack

When we finally did try the classes, we applied them in the Render event of a web
form master page. It worked like a charm. When browsing the web site with Firefox,
Chrome etc. we received the HTML5 code:

![Firefox and Chrome gets HTML5](/assets/img/blog/2011-01-27-1.png)

As you can see in the screenshot above, we get the original HTML5 code. However,
in Internet Explorer 8, we get the converted HTML4 code:

![Internet Explorer 8 gets HTML4](/assets/img/blog/2011-01-27-2.png)

When viewing the site in IE8, we get converted HTML4 code. Isn’t this marvelous?

But wait a minute! Applying this in the Render event of the master page? That is,
really bad, isn't it? Yes, you are right. As I wrote, we were just trying it out.
Relax...then read on 🙂


## The pretty final solution

I guess that we can all agree that we want to make this conversion independent of
our code. The solution above (applying it in the Render event) only works for Web
Forms-based solutions. What if we want to automatically convert HTML5 to HTML4 in
an ASP.NET MVC-based web site?

We need to find a better way of triggering the HTML5 conversion. So, how do we do
it “correctly?

I decided to create an HttpModule that applies an HTML5 conversion filter to the
outgoing response. This will automatically handle all outgoing responses, if the
module is registered in web.config.

To enable the module, actions differ depending on which IIS setup you are using.

If you are using IIS 7.0 running in Integrated mode, you must add this following
tag to the web.config system.webServer modules section:

	<add name="NExtra.Web.HttpModules.Html5ElementConvertHttpModule" type="NExtra.Web.HttpModules.Html5ElementConvertHttpModule, NExtra, Version=2.0.0.0, Culture=neutral" />

If you are using IIS 6.0 or IIS 7.0 running in Classic mode (this also applies to
the dev server), add the following to the web.config system.web httpModules section:

	<add name="NExtra.Web.HttpModules.Html5ElementConvertHttpModule" type="NExtra.Web.HttpModules.Html5ElementConvertHttpModule, NExtra, Version=2.0.0.0, Culture=neutral" />

That should be all you have to do to enable automatic HTML5 to HTML4 conversion.

All the best!

