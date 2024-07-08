---
title: Øredev 2011 in the rear-view mirror – Part 3
date:  2011-12-06 12:00:00 +0100
categories: conferences
tags:  conferences
icon:  avatar
---


This is the third part of my sum-up of Øredev 2011, which took place in Malmö, 
Sweden.


## Dan North – Embracing Uncertainty - the Hardest Pattern of All

If there's one talk I regret not attending, it's this one. Everyone I spoke to were blown away by this keynote on how we strive to avoid uncertainty, even when it's the better alternative.

Quotes like “Fear leads to risk, risk leads to processes, processes leads to hate, suffering and Gantt charts” and "We are rather wrong than be uncertain" and the way Dan reasons about how faith becomes religion, are just amazing. I can't wait for the video.

For a better summary of this session, [visit Daniel Lee's excellent blog](https://danlimerick.wordpress.com/11//10/redev-2011-day-2-rollercoaster-ride/).



## Greg Young – How to not apply CQRS

I love Greg. After this talk, I stormed out of the room and bursted out that "he is the Henry Rollins of software engineering", to which a stranger turned around, equally happy, and replied "I KNOW!". Later, I overheard a conversation, where a guy says "Wow, Greg looks JUST like Phil Anselmo". Rock star quality.

But *what did he talk about* I hear you ask. Well, he talked about how a good way to fail with CQRS is to apply it *everywhere* in a monolithic system. 

> CQRS is not a top level architecture. It requires a bounded context.

So applying CQRS without a bounded context is an almost fail-proof way to fail. When you apply CQRS everywhere, nothing can change.

Greg talked about CQRS & DDD and the dangers of imaginary domain experts. Who can be considered to be a domain expert? A consultant who have worked with a system for a while, or an employee who have worked with the domain for years, but lacks coding skills?

This is really important, since CQRS is *business-centric*. CQRS and DDD only works with domain expertise. It will otherwise be like playing the telephone game and using Google Translate to translate Shakespeare. BAs are really good at asking the right questions, but they are not domain experts.

As an exercise, Greg talked about Programming as Analysis, which I'd like to try. The point is to build a system for a domain that you don't know anything about. Timebox your access to the domain expert to two hours. In two hours, you have to find out everything you need.

You will fail, of course. But in doing so, you'll come up with a bunch of new questions. So you throw everything away. Then do it all again. Two hours. Then do it again. Then again.

Greg concluded his talk with pointing out that the most certain way to fail with CQRS is to learn it by building a CQRS framework. Instead, you should focus on the business values of applying CQRS. Greg finished his talk with calling frameworks "crack for architects" and stating that they are evil.

A great session!


## Rickard Öberg – Event Sourcing explained

Rickard talked about Event Sourcing and started with describing a common architecture:

	Client <- Service facade <- Domain <- Storage

and how with Event Sourcing, things look a bit different:

	Client <- Service facade <- Domain (Commands -> Events) <- Event storage

With Event Sourcing, we don't store state, but events. This is a huge difference. By storing events, we can replay all events that has affected an entity during its lifetime and can build it up from scratch to its current state.

To avoid heavy build-up operations, we can use stored snapshots:

- Event (latest)
- Event
- Event
- Event
- Snapshot
- Event
- Event (oldest)

With snapshots, we start with the lastest event in the stack. As long as it's not a snapshot, we keep it for later. Once we reach a snapshot, we grab it and apply all stored events to it in order. This way, we don't have to replay its entire life, just a part of it.

Rickard then talked a bit about his event sourcing framework. Clearly suffering after Greg's framework burstout, Rickard had the balls to still insist that his framework was really good and that he likes to use it.

Event sourcing makes report generation trivial, since you never loose data. If you add $500 to a bank account then withdraw $300, and the system doesn't store events, all we know is that we now have $200 more than when we started. Maybe the system writes transactional details to the log, but with event sourcing, the event store *becomes* the log.

Due to its log-like nature, event sourcing simplifies debugging. Nothing gets thrown away. The events are what make the entities look the way they do. If an event is accidentally not saved, that's bad, but that is another problem for another discussion.

Event sourcing continues to interest me. Maybe one day, I will use it?


