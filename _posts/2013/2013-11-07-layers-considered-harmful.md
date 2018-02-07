---
title:  "Christian Horsdal: Layers Considered Harmful"
date: 	2013-11-07 17:40:00 +0100
categories: conferences
tags: 	conference
---


In this session, Christian Horsdal talked about layers, carefully to distinguish
between layers (logical) and tiers (physical).

Christian started with a drawing of the architecture of a system he once worked
with. It was communicated to be a four layer architecture, but as he dissected it,
it became clear that the layer depth was actually 17. Although the code base for
each particular layer was well written, thoroughly tested and created in a modern,
agile fashion, the outcome was a highly complex architecture. 

Christian then showed us other architecture schematics from Microsoft and Oracle,
only to ask us..."why?". Why are all these layers there, and how could we do it
differently? With layers, we lose speed to delegation. The desire to separate the
layers to let them work independently of each other, introduces the need for DTOs,
causing communication to become sluggish. Introducing physical layer tiers makes
the situation even worse.

Moving down the layer stack, we often see components become more and more stable.
This stability comes with the cost of these base components being hard to change,
since the layers above depend on them. New features and changes to existing ones
can therefore be really hard to implement.

Christian calls some layers wasted layers, like taking height for portability by
abstracting away the file system, to be able to run the system on another kind of
machine. When the code is .NET or Java, how likely is that scenario? We abstract
away the database layer to easily be able to switch from one database to another.
When have we ever (I have!)? Could the abstraction instead be different? And what
about reuse? Rather, use before reuse. How many times have you designed a component
to be reusable, when the very nature of the component is to be used just in that
specific context? First, create something that is actually used, then design for
reusability.

Why do we do all this? According to Christian, we sometimes follow best practices.
Hopefully, our needs are the same as the needs of the ones these best practices
applied to. More likely, they are not.

Having defined what he thinks went wrong, Christian then asked "what now"? Where
do we move from here? Prioritize by business value and risk, then deliver early
and often. Realize that YAGNI. Favor simplicity. Apply JIT. Start working, then
step back and find the abstractions as you go along. Grow incrementally and try
to vertically slice your system, instead of just adding more layers just for the
sake of it.

Vertical slices simplify partial deployment. Your slices can become so small and
thin, that they are easy to throw away and rewrite. Other gains are smaller and
more understandable units, that are easier to test and easier to combine into new
applications.

This was a packed and interesting session. If system architecture gets your juices
flowing (ewww, sorry about that), [watch the video here](http://oredev.org/oredev2013/2013/videos.html).

