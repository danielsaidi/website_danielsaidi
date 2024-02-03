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

If there's one session I really regret not attending, it's this one. Everyone I
spoke to were blown away by Dan North's keynote about how we humans strive to
avoid uncertainty, even when it would be better to be uncertain than certain.

The quotes “Fear leads to risk, risk leads to process, process leads to hate and
suffering and Gantt charts” and "We would rather be wrong than be uncertain" and
the way Dan reasons about how faith becomes religion, makes this the top session
that will keep me waiting for the Øredev videos.

For a better summary of this session, [visit Daniel Lee's excellent blog](https://danlimerick.wordpress.com/2011/11/10/redev-2011-day-2-rollercoaster-ride/).



## Greg Young – How to not apply CQRS

I just love Greg. After this session, I stormed out of the room and bursted out 
I think it's SO cool that he is the Henry Rollins of software engineering", on
which a complete stranger turned around, equally happy, and shouted "I KNOW!". A
minute or so later, I happen to overhear a conversation, where one guy says "Wow,
Greg looks JUST like Phil Anselmo".

Greg is the personification of those metal vocalists I always wanted to be or at
least be friend with. It lets me embed myself in a thick layer of ignorance that
lets me ignore that I am a developer at a developer converence. All this despite
the fact that he wears Five Finger Shoes. Quite an achievement.

But hey, what did he talk about, I hear you ask. Well, he talked about how a good
way to fail with CQRS is to apply CQRS everywhere in a monolithic system. 

> CQRS is not a top level architecture. It requires a bounded context.

So applying CQRS in a non-core context, without a bounded context, is an almost
fail-proof way to fail. With CQRS applied everywhere, nothing can change.

Greg then talked about CQRS and DDD and the dangers of imaginary domain experts.
Who can be considered to be a domain expert? The consultant who have worked with
a system for a while, who maybe even answer to another consultant? Or the
employee who have worked with the domain for a several years, but lack coding skills?

This is really important, since CQRS is business-centric. CQRS and DDD doesn't
work without domain expertise. The result will become like playing the telephone
game and translating Shakespeare with Google Translate. BA:s are really good at
asking the right questions, but they are not domain experts.

As an exercise, Greg talked about Programming as Analysis, which I'd like to try.
The point is that you are supposed to build a system for a domain that you don't
know anything about. Timebox your access to the domain expert to two hours. In two
hours, you have to find out everything you need to build your system. The entire
system. Sure, you will fail, but, in doing so, you'll come up with a bunch of new
questions. So you throw everything away. Then do it all again. Two hours. Build
the entire system. Then again. Then again.

Greg concluded his talk with pointing out that the most certain way to fail with
CQRS is to lear CQRS by building a CQRS framework. Instead, you should focus on
the business values of applying CQRS. Greg finished his talk with calling frameworks
"crack for architects" and stating that frameworks are evil.

A great session!


## Rickard Öberg – Event Sourcing explained

Rickard talked Event Sourcing and started with describing a common architecture:

	Client <- Service facade <- Domain <- Storage

and how with Event Sourcing, things look a bit different:

	Client <- Service facade <- Domain (Commands -> Events) <- Event storage

Here, we don't store state, but events. This is a huge difference. By storing
events, we can replay all events that has affected an entity during its lifetime
and can build it up from scratch to its current state.

In order to avoid heavy build-up operations, we can use stored snapshots:

- Event (latest)
- Event
- Event
- Event
- Snapshot
- Event
- Event (oldest)

With snapshots, we build up our objects by starting with the lastest event in the
stack. As long as the item isn't a snapshot, we keep it for later. Once we reach
a snapshot, we grab it and apply all events that we have kept. This way, we don't 
have to replay the entire life of the entity, just a part of it.

Rickard then went on to talk a bit about his event sourcing framework. Obviously
suffering after Greg's recent framework burstout, Rickard had the balls to still
insist that his framework was really good and that he likes to use it. Tough crowd :)

Event sourcing makes report generation trivial, since you never loose data. If I
add 5000 to my bank account then withdraw 3000, and the system doesn't store any
events, all I know is that I now have 2000 more than when I started. Sure, maybe
the system writes transactional details to the log, but with event sourcing, the
event store becomes the log.

Due to its log-like nature, event sourcing simplifies debugging. Who did what and
when? With event sourcing, nothing gets thrown away. The events is what makes the
entities look the way they do. If an event accidentally is not saved, that is bad,
sure, but that is another problem for another discussion. My entity does not know
about it, since it does not exist.

Event sourcing continues to interest me. Maybe one day, I will get to use it?


