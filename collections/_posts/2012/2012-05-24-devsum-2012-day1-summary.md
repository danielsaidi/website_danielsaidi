---
title: DevSum 2012 - Day 1 Summary
date:  2012-05-24 07:33:00 +0100
categories: conferences
tags:  conferences
icon:  avatar
---

This is a summary of the first day of the DevSum 2012 conference in Stockholm, Sweden.


## Martin Laforest: Harnessing the Quantum World – When tiny things do big things

Martin started with some Canada-oriented information and described how his city Waterloo has moved its focus from farming to tech in a very short time. 

Martin talked about Waterloo's technical institutions and Canada's position within the field of Quantum Physics research. He talked about **the cycle of innovation** and how it leads us from curiosity to control, then on to technology with great social impacts.

One example was steam, which we investigated, understood, learned to control and led us to new innovations like the steam engine, which in turn led on to trains, boats and enabled us to travel much greater distances than before.

Martin then talked about quantum physic facts, and how everything is a particle, everything is a wave and how electrons can be at two places at the same time (an effect called **superposition**) and how you ruin the quantum effect, as soon as you inspect the system.

Basic things that most of us with an interest in this already knows, but very well presented.

Quantum effects can be used to make computers more powerful, unlike the development of speed that has dominated the field for the last 50 years, where computers have become much faster, but still perform calculations in the same way as they've always done.

Another benefit of this technology, is that **quantum physics, quantum  mechanics and quantum information** will enable 100% secure decryption, since eavesdropping will ruin the information exchange. In fact, this already exists, but only over a limited distance.

This was a very interesting session, on how quantum physics implies a new, powerful era for the computer, but how it also introduces new, more complex problems when things go small and quantum effects start to show themselves.


## Ayende Rahien: RavenDB - Amazin feats.

Ayende's talk focused on RavenDB, where he demoed the power that comes from taming the no-schema nature of document databases. This is why RavenDB can offer features like storing complex data types, fast Map/Reduce operations, ad hoc queries, sharding etc.

This was an interesting session, but at the same time little more than a sales pitch, which I'm not sure belongs to a conference.


## Andreas Håkansson - Developing Web Applications using Nancy

Andreas talked about his pet project - **Nancy** - and demonstrated building both small and large applications. Controllers, routes, casting, pipelines, configuration, bootstrapping - it's all there, ready to be used.

Nancy seems great. It looks like the team has something big going on. My only concern is how a framework with so many external integration will manage to always be up to date. I guess it won't. Time will tell.


## Fil Maj - Going Cross-Platform with HTML5 and PhoneGap

Fil's talk started with some historical breakthroughs, like the alphabet, maps, the printing press, the telegraph, the telephone, etc. 

Fil then talked about the microfilm-based knowledge bank MEMEX (invented in 1945), and how HyperCard used the same ideas when it was invented in 1987, over 40 years later.

Fil then discussed vendor lock-in and the switching cost that comes with it. If we focus on a certain vendor, we will pay the price when making our things work with other vendors.

Three examples were:

* Adobe Flash (no comment needed)
* The Windows API (where an internal Microsoft memo in 1997 implied that vendor lock-in and switching cost was part of the company's strategy).
* The iPhone SDK, where developers until 2008 were forbidden to discuss the SDK.

Fil wants to embrace openness, which brings us to PhoneGap. It had many initial problems with Apple, but is now a widely approved way of developing native, cross-platform apps in HTML5. It's not a silver bullet (many core features, like debugging and 3D work really bad), but is a cool technology for apps that can be ported to all major mobile platforms.

Fil showed **build.phonegap.com**, which is hosting for PhoneGap apps. It looks awesome and I will try to play around with it in my next mobile project.


## Rob Ashton - Modern technologies for web-based-gaming

This was a cool session about HTML5-based games, where Rob went into detail on the canvas and the possibilities (and challenges) it provides. 

Rob also demoed not using the canvas, but instead manipulating DOM elements. He used css3 transitions instead of moving elements, since it gives you really slow animations. Rob also demoed WebGL, hardware acceleration, etc. 

This talk was too hysterical at times, but I had fun and learned a lot about how to develop games in HTML5.


## Robert Folkesson - ReST-based services with WCF ASP.NET Web API

Always amazing Robert Folkesson of Active Solution had a very interesting session about REST-based services with ASP.NET Web API.

Robert begun with talking about REST's architectural constraints (client/server, stateless, cacheable, layered, uniform interface) and how to enable high scalability. He went through some of the REST interface guding principles (resource id:s, HATEOAS, etc.)

Some large services (e.g. Twitter) don't comply with the REST specifications, while others (e.g. Netflix) do. Many APIs fail to provide hypermedia. To see how mature a REST service is, you can use the [Richardson Maturity Model](https://martinfowler.com/articles/richardsonMaturityModel.html). 

Robert had a great Web API demo with OData operations, media types and formatters. I really enjoyed the session and suggest that you have a look if you can find it.


## Torkel Ödegaard - Build Real-Time Apps with Backbone.js and SignalR

I have tried to read up on Backbone many times, but find its docs lacking when compared with Knockout. I was therefore very happy to get an amazing onboarding by Torkel.

Torkel talked about the 5 main abstractions in Backbone (Model, Collection, View, Events & Router), demoed many features and used Backbone with SignalR to provide a UI with real-time updates as stuff happen on the server.

An extremely impressive session!


## Emil Cardell - Front-End CQRS

The last session of the day was on CQRS (Command Query Responsibility Segregation), where Emil discussed some benefits (scalability, reliability) and some problems (eventual consistency) involved with applying CQRS.

The demos were good, despite some technical problems that for a long time caused no data to show up on the screen. For instance, Emil modified a CRUD app to use CQRS, demoed continuation with headers, JIT etc. Do better Øredev!