---
title: Øredev 2011 in the rear-view mirror – Part 4
date:  2012-01-17 12:00:00 +0100
categories: conferences
tags:  conferences
icon:  avatar
---

This is the fourth part of my Øredev 2011 summary. It has taken a long time to get it done, so I will write a bit less about each session and refer to external resources instead.
 

## Udi Dahan – Who needs a Service Bus anyway

Udi Dahan, the founder of `NServiceBus`, talked about why he thinks we all should consider using service buses. 

Udi begun with the history of the service bus, speaking about CORBA, the rise and fall of the Broker architecture and how a service bus differ from a broker:

> A broker is in the middle of everything, a service bus is everywhere

A bus is distributed and plugged into every part of the system. There's no remoting, since it's not needed. While a broker is central and ties everything together, a bus communicates with messages and ensures that every subscriber receives the messages it should receive.

Udi finally demonstrated how to set up and use NServiceBus. If you haven't checked out NServiceBus, or any other buses for that matter, make sure to do so.


## Jeff Atwood – Creating a Top 500 Internet Website in C# for Dummies

When you publish your kick-ass website for the world to see and use, how do you optimize it to stand the load of millions of visitors? Jeff knows, and shared his four greatest tools:

- Static content
- Reverse proxy
- Multitenancy
- Caching

A `CDN` (Content Delivery Network) is a must. Either use a cloud-based service like Amazon S3, or put your content on a server that's separated from the logic. You can then distribute your content globally and let clients site use the closest available content server.

A `reverse proxy` distributes all incoming requests over a number of internal servers. With load balancing in place, it can drastically improve the amount of traffic a site can handle.

`Multitenancy` means that one application does many things. Having several applications running on one server makes each perform more poorly than if one does several things.

`Caching` ...well, we all know, but the issue is how to cache. Having one cache per server may cause inconsistencies, but having a single one may cause poor performance. Jeff uses one MySQL per app and one that is shared by all to keep them in sync.

Jeff also talked about serialization and how to consider your serialization options – `binary serialization` may crash if the assembly changes and `xml` may be CPU intense. A final advice was to design things as if you have a farm, caching, etc. even if you currently don't.

A great, but intense session.


## Marc Mercuri – Cloud First Services

Marc started by stating that you must have an entirely different mindset when you build for the cloud, and should design all new applications as if they are meant to run in the cloud.

Marc went through various hosting options (`on premise`, `cloud-based` & `partner hosted`) and some of the popular service models:

- `IaaS - Infrastructure as a Service` (Amazon EC2 etc.) – you get a server and then do the rest of the work yourself.
- `PaaS - Platform as a Service` (Azure, AppEngine etc.) – an environment to which you add your apps and get a bunch of pre-built tools.
- `SaaS - Software as a service` – free or commercial software, ready to be used by you and others, often with different service and payment tiers.

If we break down our services into well-defined capabilities, workloads, solutions, roles and services, we will be able to:

- scale them independently of eachother.
- replace one service with another one with the same capabilities.
- move, exchange or delete one service, without making the rest fail.

With cloud-based services, we must design all tasks to be async and stateless and always assume that other services aren't available. Designing services this way will prepare them for the complex reality of distributed systems.

Use distributed caches, queues, external data storage, etc. and you will be able to easily scale when you build that killer-app that the whole world wants to use. 

Consider your storage alternatives. Sometimes a relational database if perfect, while other cases require NoSQL, BLOB storage, or plain files. You can boost your service availability with `redundancy` (multiple instances) and `resiliency` (how to recover).

And finally, some final words of wisdom:

- Moving to the cloud is NOT equivalent to designing for the cloud.
- Moving to the cloud does not mean you have to move all or nothing.
- Platform SLAs are not Application SLAs. Uptime doesn't cover your app logic.
- Bad applications will not behave better in the cloud.
- Support and operations are not automatically automised.

This was an amazing one hour session. It even had more - I've excluded the Azure  parts.