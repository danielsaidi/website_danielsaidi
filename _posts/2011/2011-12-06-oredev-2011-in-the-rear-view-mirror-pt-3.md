---
title:  "Øredev 2011 in the rear-view mirror – Part 3"
date:    2011-12-06 12:00:00 +0100
categories: conferences oredev
tags: 	cqrs ddd event-sourcing
---


This is the third part of ny sum-up of Øredev 2011. I will label every talk with
day:order to satisfy all structure freaks (myself included) that read this.



## 2:1 – Dan North – Embracing Uncertainty - the Hardest Pattern of All

If there is one session I really regret not attending to, it has to be this one.
Everyone I spoke to were totally blown away by Dan's keynote about how we humans
strive to avoid uncertainty, even in situations where uncertainty would a better
state than being certain.

Uhm...I'll just point you guys straight to [Daniel Lee's excellent blog](https://danlimerick.wordpress.com/2011/11/10/redev-2011-day-2-rollercoaster-ride/),
where he write more about this session.

The quotes “Fear leads to risk, risk leads to process, process leads to hate and
suffering and Gantt charts” and "We would rather be wrong than be uncertain" and
the way Dan reasons about how faith becomes religion, makes this the top session
that will keep me waiting for the Øredev videos.



## 2:2 – Greg Young – How to not apply CQRS

I just love Greg. After this session, I stormed out of the room and bursted out 
I think it's SO cool that Greg is the Henry Rollins of software engineering", on
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

Greg then talked about CQRS and DDD, and the dangers of imaginary domain experts.
Who can be considered to be a domain expert? The consultant who have worked with
a system for a while, who maybe even answer to another consultant? Or is it that
employee who have worked with the domain for a several years?

This is really important, since CQRS is business-centric. CQRS and DDD does not
work without domain expertise. The result will become like playing the telephone
game and translating Shakespeare with Google Translate. BA:s are really good at
asking the right questions, but they are not domain experts.

As an exercise, Greg talked about Programming as Analysis, which I'd like to try.
The point is that you are supposed to build a system for a domain that you do not
know anything about. Now, timebox your access to the domain expert to two hours.
That is it. In two hours, you have to find out everything you need to build your
system. The entire system. Sure, you will fail, but, in doing so, you'll come up
with a bunch of new questions. So you throw everything away. Then do it all again.
Two hours. Build the entire system. Then again. Then again.

Greg concluded his talk with pointing out the most certain way to fail with CQRS
- learning CQRS by building a CQRS framework. Instead, you should focus on the
business values of applying CQRS. Greg finished his talk with calling frameworks 
"crack for architects" and stating that frameworks are evil. Way to go! :)


## 2:3 – Rickard Öberg – Event Sourcing explained

Rickard talked Event Sourcing and started with describing a common architecture:

	Client <- Service facade <- Domain <- Storage

and how with Event Sourcing, things look a bit different:

	Client <- Service facade <- Domain (Commands -> Events) <- Event storage

We do not store state; we store events. This is a huuuuge difference. By storing
events, we can replay all events that has affected an entity during its lifetime,
and in such a way build it up from scratch to its current state.

In order to avoid heavy build-up operations, we can use stored snapshots:

- Event (latest)
- Event
- Event
- Event
- Snapshot
- Event
- Event (oldest)

With snapshots, we build up our objects by starting with the lastest event in the
stack. As long as the item is not a snapshot, we keep it for later. Once we reach
a snapshot, we grab that representation, then apply all events that we have kept
for later. This way, we do not have to replay the entire life of the entity, just
a part of it.

That is basically it.

Rickard, however, then went on to talk a bit about his event sourcing framework.
Obviously suffering after Greg's recent framework burstout, Rickard had the balls
to still insist that his framework was really good...and that he likes to use it.

Tough crowd :)

Event sourcing makes report generation trivial, since you never loose data. If I
add 5000 to my bank account then withdraw 3000, and the system does not store any
events, all I know is that I now have 2000 more than when I started. Sure, maybe
the system writes transactional details to the log, but with event sourcing, the
event storage becomes the log.

Due to its log-like nature, event sourcing simplifies debugging. Who did what and
when? With event sourcing, nothing gets thrown away. The events is what makes the
entities look the way they do. If an event accidentally is not saved, that is bad,
sure, but that is another problem for another discussion. My entity does not know
about it, since it does not exist.

Event sourcing continues to interest me. Maybe one day, I will get to use it?


