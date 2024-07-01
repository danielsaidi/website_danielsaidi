---
title: Christian Horsdal - Layers Considered Harmful
date:  2013-11-07 17:40:00 +0100
categories: conferences
tags:  conferences

image: /assets/blog/13/oredev.jpg

video: http://oredev.org/oredev2013/2013/videos.html
---

In this Øredev 2013 session, Christian Horsdal talked about layers, careful to 
distinguish between layers (logical) and tiers (physical).

![Øredev logo]({{page.image}})

Christian started with drawing the architecture of a system he once worked with.  It was told to be a four-layer architecture, but as he dissected it, it became clear that is actually was a 17 layer one. Although the code base for each layer was well written, thoroughly tested and created in a modern, agile fashion, the outcome was a highly complex architecture. 

He showed us other architectures from Microsoft and Oracle and asked "why?". Why have all these layers? Can we do it differently? With layers, we lose speed to delegation. The desire to separate layers to let them work independently of each other, introduces a need for DTOs, causing sluggish communication. Adding physical layers makes it even worse.

Moving down the stack, we often see code become more and more stable. This stability comes with the cost of this base being hard to change, since the layers above depend on it. New features and changes to existing ones can therefore be really hard to implement.
 
Christian calls some layers "wasted", like taking height for portability by abstracting away the file system, to be able to run the system on another kind of machine. When the code is .NET or Java, how likely is that scenario (today, I'd say very likely)? We abstract away the database layer to easily be able to switch from one database to another. When have we ever done that (well, I have and often do)? Can an abstraction be different? 

And what about reuse? Rather, use before reuse. How many times have you designed a component to be reusable, when the very nature of the component is to be used just  in that specific context? First, create something that is actually used, then design for reusability. Now, I don't know what contexts Christian works in, but I often do these kind of reuses.

According to Christian, we sometimes follow best practices. Hopefully, our needs are the same as the needs of the ones these best practices applied to. More likely, they are not.

Having defined what he thinks went wrong, he asked "what now"? Where do we move from here? Prioritize by business value and risk, deliver early and often. Realize that YAGNI. Favor simplicity. Apply JIT. Start working, then step back and find the abstractions as you go along. Grow incrementally and slice vertically, instead of just adding more layers.

Vertical slices simplify partial deployment. Slices can become so small and thin, that they are easy to throw away or rewrite. Other gains are smaller and more understandable units, that are easier to test and easier to combine into new applications.

This was a packed and interesting session. I don't agree with everything (and looking back, the future proved Christian wrong in many areas), but if system architecture gets your juice flowing, you can [watch the video here]({{page.video}}).