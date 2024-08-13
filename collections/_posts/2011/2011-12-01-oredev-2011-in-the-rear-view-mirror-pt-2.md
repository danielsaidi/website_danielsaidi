---
title: Øredev 2011 in the rear-view mirror – Part 2
date:  2011-12-01 12:00:00 +0100
categories: conferences
tags:  conferencess
icon:  avatar
---


This is the second part of my sum-up of Øredev 2011, which took place in Malmö, 
Sweden.


## Phil Haack – Building Mobile applications with ASP.NET MVC4, HTML5 and jQuery Mobile

This talk was quite interesting, since Phil discussed a lot of aspects of mobile browsing and how ASP.NET MVC4 can help you out.

`Adaptive Rendering` will be added to the default web application template. It will cause web applications to automatically render differently based on the target screen size.

Another new feature is that you can create device-specific variations of the same view. For instance, `Index.cshtml` is the default view and `Index.iphone.cshtml` is a variation that you get when you view the page on an iPhone. Custom modes can be added to `global.asax`.

Phil also demonstrated using iBBDemo2 for simulating an iPhone when browsing the web. Quite handy if you want to easily be able to try out your custom views.



## Nathan Totten – Facebook Development

Nathan was hit hard by the conference's Wi-Fi problems, as he showed how to build apps for Facebook.

He talked about different kinds of Facebook applications, like iFrame apps, web sites with Facebook Connect, mobile applications, desktop apps (like Spotify) etc. and how they can use OAuth 2.0, REST services, the FB Graph API & FQL to integrate with Facebook. 

Nathan also discussed the JavaScript and C# Facebook SDKs. His advice was to use the JavaScript SDK if possible, and the server-side SDK when integrations should be hidden.

Finally, Nathan demonstrated how he works with local and live applications. He creates two separate apps: one that's bound to the live site and one that's bound to localhost.



## Fredrik Mörk – API – the hidden UI

The first Swedish speaker I watched was Fredrik Mörk, who talked about how we have to give our APIs the same tender loving care that we give our UIs. Users shun bad UIs while developers shun bad APIs, so we should put some effort into our API design.

An API must be discoverable, so always assume that the developer knows nothing. Adapt conventions from other APIs to make developers recognize your API instead of having to remember it. The REST standard is a great start.

Fredrik advised us to always expose as primitive data types as possible, to make an API accessible to as many as possible. Always choose abstract before concrete and choose your abstraction according to the purpose. For instance, if you expose an IList instead of an IEnumerable, you are communicating that you expect people to insert stuff into it.

Once an API is published, it's no longer yours to change. While a GUI can change, an API can't. Be careful with adding features and data to an API without a plan. Be intentional. Do not let chance determine where a feature ends up and what it's called.

All in all a great session!