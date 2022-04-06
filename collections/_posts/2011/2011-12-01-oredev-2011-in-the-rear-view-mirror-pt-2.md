---
title:  "Øredev 2011 in the rear-view mirror – Part 2"
date:   2011-12-01 12:00:00 +0100
categories: conferences
tags: 	conference
---


This is the second part of my sum-up of Øredev 2011, which took place in Malmö, 
Sweden.


## Phil Haack – Building Mobile applications with ASP.NET MVC4, HTML5 and jQuery Mobile

This talk was quite interesting, since Phil discussed a lot of aspects of mobile
browsing and how ASP.NET MVC4 can help you out.

`Adaptive rendering` will be added to the default web application template. This
will cause the page to automatically render differently based on the screen size. 
To see it in action, visit Zurb Foundation.

Another new feature is that you can create device-specific variations of the same
view. For instance, `Index.cshtml` is the default view and `Index.iphone.cshtml`
is the variation that you’ll see when viewing the page on an iPhone. Custom modes
can be registered in `global.asax`, which means that you are free to tailor your
own set of custom views.

Phil also demonstrated using iBBDemo2 for simulating an iPhone when browsing the
web. Quite handy if you want to easily be able to try out your custom views.

All in all, a quite interesting talk, although Phil didn't seem too enthusiastic
about it. Also, the wifi was a disaster, and caused a lot of problems. I really
hope they improve this next year.



## Nathan Totten – Facebook Development

Nathan was hard by the wi-fi problems, as he demonstrated how to develop apps for 
Facebook.

Nathan talked about various types of Facebook applications like iFrame apps, web
sites with Facebook Connect, mobile applications, desktop apps (like Spotify) etc.
and how they use OAuth 2.0, REST services, the FB Graph API and FQL to integrate
with Facebook. He also discussed the JavaScript and C# Facebook SDK:s. His advice
was to use the JavaScript SDK whenever possible and a server-side SDK when the
integration should be more invisible.

Finally, Nathan demonstrated how he works with local and live applications, e.g.
if you are developing an application locally (running on localhost) and have it
live as well. He then creates two separate FB apps: one that is bound to the live
site and one that is bound to localhost. He also told us that FBML is dead.



## Fredrik Mörk – API – the hidden UI

The first Swedish speaker I got around to listen to was Fredrik Mörk, who talked
about how we have to give our api:s the same tender loving care that we give our
UI:s. Users shun bad ui:s while developers shun bad api:s, so we should put some
effort into our api designs.

An api must be discoverable, so always assume that the user knows nothing. Adapt
conventions from other api:s, so that devs recognize your api instead of having
to remember it. This also involves making it possible to navigate through the api,
adapting naming conventions (delete or remove, store or save etc.) and to put an
equal amount of attention to all parts of the api and take care of the api like
you do with other parts of your systems.

Fredrik advised us to always expose as primitive data types as possible, to make 
the api accessible to as many as possible. Always choose abstract before concrete
and choose your abstraction according to the purpose. For instance, if you expose
an IList instead of an IEnumerable, you are communicating that you expect people
to insert stuff into it. This however doesn't make sense for REST api:s.

Once an api is out there, it's no longer yours to change. A GUI can change. An
api cannot. Be careful with adding features and data to an API just because a 
user wants it. It will ultimately bloat the API. Be intentional. Don't let chance
determine where a feature ends up and what it's called.

All in all a great session!