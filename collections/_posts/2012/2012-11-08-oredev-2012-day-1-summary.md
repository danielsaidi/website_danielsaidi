---
title: Øredev 2012 - Day 1
date:  2012-11-08 00:30:00 +0100
categories: conferences 
tags:  conference
icon:  avatar
---

I am happy to once again attend Øredev in Malmö, Sweden. Three days with great
speakers and nice friends is just what is needed in the dark, Swedish November.


## David Rowan – Software won...now what?

[David Rowan](http://www.davidrowan.com) is the editor of Wired Magazine. We 
didn't catch his first topic, but as we arrived from the airport, he talked 
about using open data to fight corruption with transparency in places like 
Bangalore.

David mentioned how Random Hacks of Kindness can solve real world and listed 
some areas where developers can disrupt, like **education** (Khan Academy, 
Udacity, girl dev initiatives etc.), **government** (real life bug reports, 
parking apps, Code for America etc.) and **health**.

He then showed a video of the Dragon Project and told us to, in order to be real
rebels, have a healthy disregard for the impossible, to not back out if something
we try prove not to work and that we should move fast and break things. Cliché
bonanza deluxe. 

All in all, I wasn't too impressed.


## Glenn Block – Hypermedia and ASP.NET Web API

I really like Glenn Block and found this hypermedia session very interesting,
where he demonstrated ASP.NET Web API and what hypermedia is all about.

Hypermedia is embedded links in HTTP responses (which should used as app state
transitions) and is used to provide instructional, link-oriented api:s that can
educate the client and provide links to actions that can be performed on given model.

Hypermedia was initially used by web browsers, but this has changed. Since it
can provide many ways for the client to interact with the model, hypermedia api:s
are much more transparent and obvious than traditional api:s that only serve data.

When it comes to implementing Hypermedia api:s, they are surfaced by media types
and headers. The same content can be served in several media types (for instance,
an object can be represented as XML, JSON, HTML etc.). Formatters can be helpful
in mapping a model to a certain type of data. A cool thing with ASP.NET Web API
is its ability to provide a self-hosted system. You don't need an IIS to run it, 
which opens up for a lot of flexible setups.

Glenn finished off with demonstrating Collection+JSON, REST Agents, templates etc.
This was a very interesting session that I recommend that you check out.


## Christian Johansen – Pure, Functional Javascript

This was a stuffed session, where Christian started with the difference between a
function declaration and a functional expression, then went into crazy stunts to
optimize hard-to-read JavaScript and convert it into compact, function-based code.

In JavaScript, functions are first-class objects that can be passed around. This
provides some powerful features. Functions can either have side effects, be pure
functions (with no side-effects) or be high-order functions (that either return a
function or takes a function as argument).

Christian showed how to replace if-loops with `array.forEach`, transformations 
with `array.map`, `reduce`, method shortcuts etc. This part was a great fun and 
got my JavaScript nerve itching.


## Glenn Block – Node.js in the Cloud with Windows Azure

Another very interesting session with Glenn Block, where he demonstrated using 
Azure to host node.js. It was code focused and extremely inspiring.

Glenn demonstrated new cli tools and the new HTML5 Azure Management Portal, as
well as a service called “Backend as a Service”, which is a great solution for 
mobile or web apps that needs persistency of data etc. but do not want to develop 
a sophisticated backend.

Azure Backend as a Service gives systems access to a great backend without writing
a single line of code, although you can if you need to. With it, you can create apps 
from within the Management Portal or adjust already existing apps to use the service. 
Glenn then demoed how to do this in iOS.

This session was really inspiring. Have a look at the video if you get a chance.


## Damien Edwards – ASP.NET 4.5

Resisting the urge to avoid WebForms for all eternity, I went to this talk to
get up to date with ASP.NET 4.5, which was released when I was on parental leave.

Damien started by presenting a new responsive theme (which has been around for a
while), new script features (like bundling), login services (e.g. using Facebook
or Twitter) etc.

After describing .NET:s async history, from APM (Asynchronous Programming Model,
with the old Begin/End pattern) via EAP (Event-based Asynchronous Pattern, with
the BackgroundWorker class etc.) to TAP (Task-based Asynchronous Pattern), Damien
moved on to the new async features of .NET.

Damien's demo demonstrated how to work with these features and the traps you can
fall into if you're not careful. In the demo, he also presented async page loads
and new model binding functionality for ASP.NET data controls.

All in all a really good presentation.


## Alex Papadimoulis – Ugly code

Before I begin, let me just say that this was the best and most entertaining talk
of the day. Alex is the founder of the **Daily WTF** (Worse Than Failure) and
started the talk by playing a little game with us.

The game - "Is this ugly or not" - consisted of him showing us code, after which
we would raise our hands if we found it ugly. The first snippet was a piece of
MUMPS code, which looked absolutely terrible (hold on, we'll get back to this).
Then, code for a flight simulator shaped like an airplane. And so on...

Alex wrapped up the game by stating that beauty is in the eye of the beholder,
while ugly is just plain ugly. He then listed five different types of ugly code:

* **Dilapidated and Decrepit** - code that has passed through too many non-caring
hands...and that should have died a long time ago.

* **Complete Clusterfrack** - code with infinite complexity, often is the result
of a lot of thought...often a lot of wrong thought.

* **Maddening Mismatch** - code that consists of many good parts, that have gone
bad together.

* **Disastrously Dishelved** - code that is just pure mayhem (illustrated with a
room filled to the brink with stuff), but in which the creator finds his/her way.

* **Complex and Convoluted** - code with unnecessary complexity...and often dumb.

Unlike ugly code, however, we also have code that is just not that pretty:

* **Old and Over the Hill** - it's old and not that beautiful, but may still work
...and may even work well.

* **Freakishly Foreign** - code that we simply do not understand, because we are
not familiar with it.

This is where things got interesting. Returning to MUMPS, we really don’t know if 
the code was ugly, since we don't know how it’s supposed to look, since we don’t 
know it. Maybe the code we saw was really good MUMPS code?

Alex then listed the developers who create ugly code:

* **Clueless coders** - They come, they work, they leave...they seldom care.

* **Cowboy coders** - A boss' best friend. Gets shit done without letting things
like regulations, processes etc. get in the way. Gets shit(!) done.

* **Clever coders** - They know what they do, they care about their craft...and
when they do, they create the worst pieces ugly code you can imagine.

So, why are clever coders so dangerous? Because cleverness is unavoidable, which
Alex demonstrated with some great examples of how bad things can go when you try
to be clever. Clever coders tend to come up with complex solutions, since complex
solutions are fun, right?

Now that we have identified the developer types, how to we tame them?

* **Clueing-in the clueless coders** - Arrange fun activities with food and drinks,
where they can also learn new stuff.

* **Corral the cowboys** - Cowboy coders hate processes, so surround them by code
reviews, testing etc. until they adapt or leave.

* **Cease the cleverness** - Alex had a bike/glove analogy that I missed to jot down.

When you are about to remove pieces of ugly code, ask yourself if you are really
(really) sure you know what you are doing. Do you understand what the code does?
Can some unintentional side-effects be critical to the system?

If you still decide that you want to proceed, stay committed! Rewriting is boring
and tedious, with the result being code that does exactly the same thing (should
have zero noticable impact). Giving up halfway in will make things worse!

All in all, a really awesome session  - check it out!


## Dennis Gustafsson – Optimizing Mobile Games

Dennis and me studied at the same university. Back then, I was always impressed
by what he and his colleagues put together. For instance, they started a physics
engine company that got acquired by NVIDIA, then created and sold Dresscode - a
code analysis product etc...while I coded away on my php-based "system".

Today, Dennis talked about his mobile game *Sprinkle*, which has been downloaded 
more than 7 million times. He described how the game grew from an idea to a 
successful multi-platform game, some design and technology choices they made 
during development, how they implemented the water physics etc.

Dennis' talk was extremely detailed. Where other developers may have kept to the
inner workings of their successful app a secret, Dennis described everything with
a super-high level of detail! Everything from input abstractions, audio, graphics,
aspect ratio handling, CPU optimizations, particle design...nothing was left out,
not even the water simulation, which was completely dissected.

This talk was really focused on technology. I will not go into details, but if 
you are creating advanced games for mobile devices, I really suggest that you
check it out.