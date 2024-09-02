---
title: Jimmy Nilsson & Jon Gyllensward - Less is more! When it comes to art and software
date:  2013-11-08 09:54:00 +0100
categories: conferences
tags:  conferences

image: /assets/blog/13/oredev.jpg

video: http://oredev.org/oredev2013/2013/videos.html
---

This talk was a case study from Sirius International Jon Gyllenswärd and Jimmy Nilsson from factor 10 talked about a two year long change process they 
managed together.

![Øredev logo]({{page.image}})

This process involved a complete system redesign, moving to an optimal code base, cross functional teams and to, sometimes controversially, always choose the simplest solution.

Since this process shares many of the challenges we at eBay Sweden has faced, this talk was highly relevant for me and my colleagues. We have come a really long way, but some of the choices we made have been quite different from the ones Sirius have made.

Before starting this transition, Sirius suffered from the technical debt of old, rigid systems. It held them back and some things were virtually impossible to do. 

They decided to build new features as separate satellite components, decoupled from their other systems. As this proved to work, they didn't have to propose a system redesign. Instead, management eventually requested it, referring to the satellite project architectures.

Their core principle has been "when in doubt, try something very simple". This has led to unconventional solutions and choices, like at times ditching databases, writing data directly to disk and loading everything into memory. 

If it's easier to erase all data and recalculate than it is to store, why store? Dumping data to file with protobuf instead of storing it in a database made operations lighting fast, but also required additional code for interpreting the binary data.

If the easy way works, go with it. If things go wrong, take the hit and act, Dirty Harry style.

Having experienced many of the things Jon and Jimmy talked about, I enjoyed this talks and its unconventional approaches. The session was broad and at times specific to Sirius, but than again, it was a case study.

If you work in a situation like the one Sirius has undergone, [this video]({{page.video}}) is a good watch.