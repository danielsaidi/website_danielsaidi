---
title: Øredev 2011 in the rear-view mirror – Part 4
date:  2012-01-17 12:00:00 +0100
tags:  conference
categories: conferences
---

This is the fourth part of my Øredev 2011 summary. It has taken quite a long time
to get this finished, so I will write a bit less about each session and refer to
external resources instead.
 

## Udi Dahan – Who needs a Service Bus anyway

Udi Dahan, founder of `NServiceBus`, had a nice talk about why we should consider
using service buses. He begun with the history of the service bus, speaking about
CORBA, the rise and fall of the Broker architecture and how a service bus differ
from a broker:

> A broker is in the middle of everything, a service bus is everywhere

A bus is distributed everywhere, plugged into every part of the system. There's
no remoting, since none is needed. While a broker is central and ties everything
together, a bus communicates with messages and makes sure that every subscriber
receives the messages it should receive.

Udi finally demonstrated NServiceBus and how to set it up. The demo was cool, but
hard to describe. If you haven't checked out NServiceBus, or any other buses for
that matter, make sure to do so.


## Jeff Atwood – Creating a Top 500 Internet Website in C# for Dummies

When you publish your kick-ass web site for the world to see and use, how do you
optimize it to make it stand the load of millions of visitors? Jeff knows, and
shared his four greatest optimization tools:

- Static content
- Reverse proxy
- Multitenancy
- Caching

A `CDN` (Content Delivery Network) is a must. If you don't want to use cloud-based
services like Amazon S3, at least put content on a simply configured server, separate
it from your logic and you’ll be able to distribute your content all over the world,
grabbing the one closest to your users when they require it.

A `reverse proxy` distributes incoming requests over a number of internal servers.
With load balancing capabilities, it can drastically improve the amount of traffic
your web site can handle.

`Multitenancy` means that one application does many things. Having several applications
running on one server makes each perform more poorly than if one is configured to do
several things. So, have one application to handle several sites and services and
you’ll be off to prestanda heaven.

`Caching` means...well, we all know, but the issue is how you should cache. Having
one cache per server may `cause inconsistency`, but having one that is shared by all
may cause poor performance. Jeff use one MySQL per app and one that is shared by all
and syncs with the individual instances.

Jeff also spoke about serialization and how you must consider your serialization 
options – `binary serialization` may crash if the assembly changes and `xml` may
be CPU intense. A final advice was to design your systems as if you have a farm,
caching etc. even if you don't have one at the moment.

A great, but intense session.


## Marc Mercuri – Cloud First Services

Marc started off by stating that you must have an entirely different mindset when
you develop for the cloud and that you should be designing all new applications as
if they are to be run in the cloud.

Marc went through various hosting alternatives (`on premise`, `cloud-based` and
`partner hosted`) and some of the popular service models:

- `Infrastructure as a service` (Amazon EC2 etc.) – you get a server and do the rest yourself.
- `Platform as a service` (Azure, AppEngine etc.) – an environment to which you add your apps.
- `Software as a service` – free or commercial software, ready to be used by you and others.

If we break down our services into well-defined capabilities, workloads, solutions,
roles and services, we will be able to the following:

- scale them independently of eachother.
- replace one service with another one with the same capabilities.
- move, exchange or delete one service, without making the rest fail.

With cloud-based services, we must design all tasks to be async and stateless and
always assume that services we use may not be available. Designing your services
this way will prepare them for reality.

Use distributed cache, queues, external data storage etc. and you will be able to
easily scale out when you build that killer-app that the whole world wants to use.
Consider your storage alternatives. Some data is perfect to store in a relational
database, while other may fit better in NoSQL or BLOB storage. Boost availability
with `redundancy` (multiple instances) and `resiliency` (how to recover).

And finally, some final words of wisdom:

- Moving to the cloud is NOT equivalent to designing for the cloud.
- Believing that moving to the cloud means moving all or nothing, is plain wrong.
- Platform SLA:s are not Application SLA:s. Uptime doesn't cover your app logic.
- Bad applications will not behave better in the cloud.
- Support and operations are not automatically automised.

Phew, not bad for a one hour session! I even excluded the Azure-specific parts.