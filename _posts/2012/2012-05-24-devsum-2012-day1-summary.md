---
title:  "DevSum 2012: Day 1 Summary"
date:    2012-05-24 07:33:00 +0100
categories: conferences
tags: 	conference
---


Today was first day of DevSum 2012, in central Stockholm. This is a short sum-up
of the first day. 


## Martin Laforest: HARNESSING THE QUANTUM WORLD – When tiny things do big things

Martin started off with a some Canada-oriented information and described how his
city Waterloo, has moved its focus from farming to tech in a very short time. He
spoke about the Waterloo technical institutions and Canada's pride in the field
of Quantum Physics research. He talked about **the cycle of innovation** and how
it leads us from curiosity (in the case of fire, fear even) to control, then on
to technology with great social impacts.

One example was steam, which we investigated, understood, learned to control and
led us to new innovations like the steam engine, which in turn led on to trains,
boats and enabled us to travel much greater distances than before.

Martin then talked about quantum physic facts, and how everything is a particle,
everything is a wave and how electrons can be at two places at the same time (an
effect called **superposition**) and how you ruin the quantum effect, as soon as
you inspect the system. etc. Pretty basic things that most of us with an interest
in this field already knows, but very well presented.

The quantum effects can be used to make our computers more powerful, unlike the
development of speed that has dominated the field for the last 50 years (where a
computer has become much faster, but still performs calculations in the same way
as it did way back when).

Another great benefit of this new technology, is that **quantum physics, quantum 
mechanics and quantum information** will make it possible to achieve 100% secure
decryption, since eavesdroppers will ruin the information exchange. In fact, this
technology already exists, but only over a limited distance.

Overall a very interesting session, especially if you are interested in the field
of quantum physics. Quantum physics implies a new, powerful era of the computer,
but also introduce new, more complex problems that arise when things go small and
quantum effects start to show themselves.



## Ayende Rahien: RavenDB - Amazin feats.

Ayende's talk was focusing on the awesome features of RavenDB. Ayende demoed the
power that comes with taming the no-schema nature of document databases. This is
why RavenDB can offer features like storing complex data types, a lightning fast
Map/Reduce operation engine, ad hoc queries, sharding etc.

This was an interesting session, although Ayende was as hard to hear as always.



## Andreas Håkansson - Developing web applications using Nancy

Andreas talked about his darling project, **Nancy** and demonstrated everything
from small to large applications. Controllers, routes, implicit casting, helper
methods, convention, pipelines, configuration, bootstrapping. Yep, it's all here.

Nancy seems great and it looks like the team has something big going on. My only
concern is how such a framework with so many external integration will manage to
always be up to date with the latest versions of, say, StructureMap and NUnit.



## Fil Maj - Going Cross-Platform with HTML5 and PhoneGap

Fil started off with some historical breakthroughs, like the alphabet, maps, the
printing press, the telegraph, the telephone etc. He then talked a bit about the
microfilm-based knowledge bank MEMEX (invented in 1945), and how HyperCard later
used the same ideas when it was invented in 1987, more than 40 years later.

Fil then moved on to discussing vendor lock-in and the switching cost that comes
with it. If we focus on delivering stuff based on the product of a certain vendor,
we will have to pay the price when it comes to making our things work with other
products.

Three examples were:

* Adobe Flash (no comment needed)
* The Windows API, where an internal MS memo in 1997 implied that switching cost
was part of the company's strategy.
* The iPhone SDK, where developers until 2008 were forbidden to discuss the SDK.

Fil wants embraces openness. This brings us to PhoneGap, which had many initial
problems with Apple, but which is now a widely approved way of developing native,
cross-platform apps with HTML5. It's not by any means a silver bullet (many core
features, like debugging and 3D work really bad), but it is a cool technology if
you want to build apps that can easily be ported to all major mobile platforms.

Fil then demonstrated **build.phonegap.com**, which is hosting for PhoneGap apps.
It looks awesome and I will try to play around with it in my next mobile project.

Thanks Fil, I really enjoyed your session!



## Rob Ashton - Modern technologies for web-based-gaming

This was a cool session about HTML5-based games. Rob went into detail describing
the canvas and the possibilities (and challenges) it provides. He also demoed not
using the canvas, but rather manipulating DOM elements instead. When doing so, he
used css3 transitions instead of moving the elements around in code, since moving
elements around will give you really slow animations.

Rob also demoed WebGL, hardware acceleration etc. The talk was too hysterical at
times, but I had fun and learned a bunch about HTML5 game development...but doubt
that I will find myself playing around with WebGL any evening soon.



## Robert Folkesson - ReST-baserade tjänster med WCF ASP.NET Web API

Always well-performing Robert Folkesson of Active Solution had a very interesting
session about REST-based services with ASP.NET Web API.

He begun talking about RESTs architectural constraints (client/server, stateless,
cacheable, layered, uniform interface) and how to enable really high scalability.
He also went through some of the REST interface guding principles (resource id:s,
HATEOAS - Hypermedia as the engine of application state etc.)

Some large sites (e.g. Twitter) do not comply to the REST specificationss, while
others (Netflix) do. Many APIs fail to provide hypermedia to the client. To see
how mature your REST service is, use the [Richardson Maturity Model](https://martinfowler.com/articles/richardsonMaturityModel.html). 

Robert had a good Web API demo with OData operations, media types and formatters.
I really enjoyed the session.



## Torkel Ödegaard - Build Real-Time Apps with Backbone.js and SignalR

I have tried to read up on Backbone several times, but always found it to be too
badly described, especially compared with Knockout. This session put my laziness
(and stupidity?) to shame though, as Torkel demonstrated the straightforward way
to get it up and running and unleasing its power.

Torkel talked about the 5 main abstractions in Backbone (Model, Collection, View,
Events and Router), demoed several Backbone features and then used Backbone with
SignalR to provide a UI with real-time updates as stuff happen on the server.

Extremely impressive session!



## Emil Cardell - Front-End CQRS

The last session of the day touched on an interesting area: CQRS. Emil discussed
the benefits (scalability, reliability) and problems (eventual consistency) with
applying CQRS (Command Query Responsibility Segregation).

The demos were good, despite some unfortunate technical problems that for a long
time caused no data to show up on the screen. For instance, Emil modified a CRUD
app to use CQRS, demoed continuation with headers, JIT etc.




