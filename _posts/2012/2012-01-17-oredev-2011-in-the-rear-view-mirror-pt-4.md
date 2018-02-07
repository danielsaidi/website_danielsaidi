---
title:  "Øredev 2011 in the rear-view mirror – Part 4"
date:    2012-01-17 12:00:00 +0100
categories: conferences
tags: 	conference
---


This is the fourth part of my Øredev 2011 summary. It has taken quite a long time
to get this finished, so I will write a bit less about each session in this post
and refer to external resources instead of spending lines on describing products
and concepts.
 

## 2:4 – Udi Dahan – Who needs a Service Bus anyway

Udi Dahan, founder of `NServiceBus`, had a nice talk about why we should consider
using a service bus. He begun with the history of the service bus, speaking about
CORBA, the rise and fall of the Broker architecture and how a service bus differ
from a broker:

> A broker is in the middle of everything, a service bus is everywhere

A bus is distributed everywhere, plugged into every part of the system. There is
no remoting, since none is needed. While a broker is central and ties everything
together, a bus communicates with messages and makes sure that every subscriber
receives the messages it should receive.

Udi finally demonstrated NServiceBus and how to set it up. The demo was cool, but
hard to describe. So if you have not checked out NServiceBus, or any other buses
for that matter, make sure to do so. They are great for certain tasks.

 

## 2:5 – Jeff Atwood – Creating a Top 500 Internet Website in C# for Dummies

When you publish your kick-ass web site for the world to see and use, how do you
optimize it to stand the traffic? Jeff knows, and shared his four greatest means
of optimization:

- Static content
- Reverse proxy
- Multitenancy
- Caching

A `CDN` (Content Delivery Network) is a must-have. If you do not want to use any
cloud-based services like Amazon S3, at least put content on a simply configured
server of your own, separate it from your logic and you’ll be able to distribute
your content all over the world, grabbing the one closest to your users when they
require it.

A `reverse proxy` distributes incoming requests over a number of internal servers.
With load balancing capabilities, it can drastically improve the amount of traffic
your web site can handle. Just make sure to make it sticky if a client has to end
up on the same server for each request (he/she shouldn't have to, if you do things
correctly).

`Multitenancy` means that one application does many things instead of one. Having
several applications on one server makes each perform more poorly than if one is
configured to do several things. So, have one application to handle several sites
and services and you’ll be off to prestanda heaven.

Caching means...well, we all know. The issue is how you should cache. Having one
cache per server may `cause inconsistency`, but having one that is shared by all
may cause poor performance. Jeff use MySQL for cache storage. He has one per app
and one that is shared by all and syncs with the individual cache instances.

Jeff also spoke about serialization and how you must consider your serialization 
options – `binary serialization` may crash if the assembly changes and `xml` may
be CPU intense. A final piece of advice was to design your systems as if you have
a farm, caching etc...even if you do not have one at the moment.

A great, but intense session. I hope my description gave it justice.

 

## 2:6 – Marc Mercuri – Cloud First Services

Marc covered a lot in this talk. He started off by stating that you must have an
entirely different mindset when you develop for the cloud and that you should be
designing all new applications as if they are to be run in the cloud.

Marc went through various hosting alternatives (`on premise`, `cloud-based` and
`partner hosted`) and some of the popular service models:

- `Infrastructure as a service` (Amazon EC2 etc.) – you get a server somewhere and do the rest yourself
- `Platform as a service` (Azure, AppEngine etc.) – a configured environment to which you add your applications
- `Software as a service` (nuff commercial 🙂 – free or commercial software, ready to be used by you and others

If we break down our services into well-defined capabilities, workloads, solutions,
roles and services, we will be able to:

- scale them independently of eachother
- replace one service with another one with the same capabilities
- move, exchange or delete one service, without making the rest fail

With cloud-based services, we must think async for all tasks and design them to
be stateless and always assume that services we use will not be available at the
moment. Designing your services this way will prepare them for what will come 🙂

Use distributed cache, queues, external data storage etc. and you will be able to
easily scale out when you build that killer-app that the whole world wants to use.
Consider your storage alternatives. Some data is perfect to store in a relational
database, while other may fit better in NoSQL or BLOB storage. Boost availability
with `redundancy` (multiple instances) and `resiliency` (how to recover).

And finally, some final words of wisdom:

- Moving to the cloud is NOT equivalent to designing for the cloud
- Believing that moving to the cloud means moving all or nothing, is plain wrong
- Platform SLA:s are not Application SLA:s. Assuring uptime does not mean covering your application logic.
- Bad applications will not behave better in the cloud.
- Support and operations are not automatically automised

Phew, not bad for a one hour session! I even excluded the Azure-specific parts.


