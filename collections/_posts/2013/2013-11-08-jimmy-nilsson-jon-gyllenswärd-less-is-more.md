---
title: Jimmy Nilsson & Jon Gyllensward - Less is more! When it comes to art and software
date:  2013-11-08 09:54:00 +0100
categories: conferences
tags:  conferences

image: /assets/blog/2013/131111/oredev.jpg

video: http://oredev.org/oredev2013/2013/videos.html
---

This Øredev 2013 session was a case study from Sirius International Jon Gyllenswärd 
and Jimmy Nilsson from factor 10 talked about a two year long change process they 
managed together.

![Øredev logo]({{page.image}})

This process involved a complete system redesign, moving to a business optimal code 
base, cross functional teams and to, sometimes controversially, always choose the 
simplest solution possible.

Since this process shares so many of the challenges we at eBay Sweden faced during 
our own change process, this session was highly relevant for me and my colleagues. 
We have come a really long way, but some of the choices we made have been quite 
different from the ones Sirius have made.

Before starting this huge change process, Sirius suffered from the technical debt
of old, rigid systems. It held them back and some things were virtually impossible
to achieve. 

Initially, they decided to build new features as totally separate satellite components,
entirely decoupled from all other systems. As this model proved to work, they didn't
have to propose a system redesign to management. Instead, management eventually requested 
a complete system redesign, referring to the satellite project architectures.

Their core principle has been "when in doubt, try something very simple". This has
led to unconventional solutions and choices, like at times ditching databases and
instead write data directly to disk and load everything into memory. If it's easier
to erase all data and recalculate than it is to store, why store? Dumping data to
file with protobuf instead of storing it in a database made operations lighting fast, 
but required additional code for interpreting the binary data.

If the easy way works, go with it. If things go wrong, take the hit and act directly,
Dirty Harry style.

Having experienced many of the things Jon and Jimmy talked about, I enjoyed this
talks and to hear about some of the more unconventional things the team tried out.
The session was broad and at times very specific to Sirius, but than again, it was
a case study. If you work in a situation like the one Sirius has undergone in the
last two years, [this video]({{page.video}}) is a good watch.