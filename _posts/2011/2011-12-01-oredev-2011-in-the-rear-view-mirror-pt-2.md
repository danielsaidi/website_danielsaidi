---
title:  "Øredev 2011 in the rear-view mirror – Part 2"
date:    2011-12-01 12:00:00 +0100
categories: conferences
tags: 	oredev
---


This is the second part of ny sum-up of Øredev 2011. I will label each talk with
day:order to satisfy all structure freaks (myself included) that read this.


## 1:4 – Phil Haack – Building Mobile applications with ASP.NET MVC4, HTML5 and jQuery Mobile

This talk was quite interesting, since Phil discussed a lot of aspects of mobile
browsing and how ASP.NET MVC4 can help you out.

`Adaptive rendering` will be added to the default web application template. This
will cause the page to automatically render differently depending on the size of
the screen. If you want to see it in action, check out Zurb Foundation. In other
words, ASP.NET MVC4 doesn't introduce adaptive rendering. It’s just a convenience.

Another new feature is that you can now create device-specific variations of the
same view. For instance, `Index.cshtml` is the default and `Index.iphone.cshtml`
is the variation that you’ll see when viewing the page on an iPhone. Custom modes
can be registered in `global.asax`, which means that you are free to tailor your
own set of custom views.

Phil also demonstrated using iBBDemo2 for simulating an iPhone when browsing the
web. Quite handy if you want to easily be able to try out your custom views.

All in all, a quite interesting talk, although Phil didn't seem too enthusiastic
about it. Also, the wifi at Øredev was a disaster, and caused several speakers a
lot of problems. I really hope they improve this until next year.



## 1:5 – Nathan Totten – Facebook Development

Nathan was another speaker who got hit hard by the wi-fi problems, demonstrating
how to develop apps for Facebook.

I really enjoyed this session, despite the technical problems, and the fact that
I already have developed Facebook apps. However, since I am self-taught in doing
so, listening to Nathan explaining several parts that I haven't gotten around to
was a great take-away.

Nathan talked about various types of Facebook applications like iFrame apps, web
sites with Facebook Connect, mobile applications, desktop apps (like Spotify) etc.
and how they use OAuth 2.0, REST services, the FB Graph API and FQL to integrate
with Facebook. He also discussed the JavaScript and C# Facebook SDK:s. His advice
was to use the JavaScript SDK whenever possible and use a server-side SDK when an
integration should be more invisible.

Finally, Nathan demonstrated how he works with local and live applications, e.g.
if you are developing an application locally (running on localhost) and have it
live as well. He then creates two separate FB apps: one that is bound to the live
site and one that is bound to localhost. He also told us that FBML is dead, so do
not use it ever again 🙂



## 1:6 – Fredrik Mörk – API – the hidden UI

The first Swedish speaker I got around to listen to was Fredrik Mörk, who talked
about how we have to give our api:s the same tender loving care that we give our
UI:s. Users shun bad guis while developers shun bad api:s, so we should put some
effort into our api designs.

An api must be discoverable, but how? Always assume that the user knows nothing.
Therefore, adapt conventions from other api:s, so that devs instantly recognize
your api instead of having to remember it. This also involves making it possible
to navigate through the api, adapting naming conventions (delete or remove, store
or save etc.), put an equal amount of attention to all parts of the api and take
care of the api like you do with other parts of your systems.

Fredrik advised us to always expose as primitive data types as possible, to make 
the api accessible to as many as possible. Always choose abstract before concrete
and choose your abstraction according to the purpose. For instance, if you expose
an IList instead of an IEnumerable, you are communicating that you expect people
to insert stuff into it (but this does not make any sense for REST api:s).

Fredrik’s said that once an api is out there, it is no longer yours to change. A
GUI can change. An api cannot. Be careful with inserting stuff into your API just
because a user wants it. It will ultimately bloat the API. Be intentional. Do not
let chance determine where a feature ends up and what it is called.

All in all a good session...and quite liberating with a Swenglish accent instead
of the spotless American and British ones that dominated the rest of the day.


