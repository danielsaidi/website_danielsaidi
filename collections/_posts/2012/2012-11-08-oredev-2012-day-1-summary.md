---
title: Øredev 2012 - Day 1
date:  2012-11-08 00:30:00 +0100
categories: conferences 
tags:  conferences
icon:  avatar
---

I am happy to once again attend Øredev in Malmö, Sweden. Three days with great talks and nice friends is just what is needed in the dark, Swedish November.


## David Rowan – Software won...now what?

[David Rowan](http://www.davidrowan.com) is the editor of Wired Magazine. We didn't catch the beginning, as we arrived from the airport, where he talked of using open data to fight corruption with transparency in places like Bangalore.

David mentioned how Random Hacks of Kindness can solve real world problems and then listed some areas where developers can disrupt, like **education** (Khan Academy, Udacity, girl dev initiatives etc.), **government** (real-life bug reports, parking apps, Code for America etc.) and **health**.

David then showed a video of the Dragon Project and said that real rebels should have a healthy disregard for the impossible, not back out if something proves not to work and that we should move fast and break things. 

Cliché bonanza deluxe.


## Glenn Block – Hypermedia and ASP.NET Web API

I really like Glenn Block and found this hypermedia talk very interesting, where he showed ASP.NET Web API and what hypermedia is all about.

Hypermedia is embedded links in HTTP responses (can be used as app state transitions) that is used to provide instructional, link-oriented api:s that can educate the client and link to actions that can be performed on given model.

Hypermedia was initially used by web browsers, but this has changed. Since it can provide many ways for the client to interact with the model, hypermedia APIs are more transparent and obvious than traditional APIs that only serve data.

Hypermedia APIs are surfaced by media types and headers, where the same content can be served in several media types (for instance, objects can be represented as XML, JSON, HTML etc.). Formatters can be helpful in mapping a model to a certain type of data. 

A cool thing with ASP.NET Web API is its ability to provide a self-hosted system. You don't need an IIS to run it,  which opens up for a lot of flexible setups.

Glenn finished off with demonstrating Collection+JSON, REST Agents, templates etc. This was a very interesting session that I recommend that you check out.


## Christian Johansen – Pure, Functional Javascript

This was a stuffed session, where Christian started with the difference between a function declaration and a functional expression, then went all in to convert hard-to-read JavaScript into compact, optimized, functional code.

JavaScript functions are first-class objects that can be passed around. Functions can have side effects, be pure functions (with no side-effects) or be high-order functions (that either return a function or takes a function as argument).

Christian then showed us how to replace if-loops with `array.forEach`, transformations with `array.map`, `reduce`, method shortcuts, etc. This part was great fun and got my JavaScript nerve itching.


## Glenn Block – Node.js in the Cloud with Windows Azure

This was another very interesting session with Glenn Block, where he demonstrated using Azure to host node.js. This talk was code-focused and extremely inspiring.

Glenn showed new CLI tools and the new HTML5 Azure Management Portal, as well as a service called “Backend as a Service”, which is a great solution for mobile or web apps that needs persistency of data etc. but do not want to develop a sophisticated backend.

Azure Backend as a Service gives us access to a backend without having to write a single line of code. It can be used to create apps from the Management Portal, or adjust already existing apps to use the service. Glenn demoed how to do this for iOS.

This session was really inspiring. Have a look at the video if you get a chance.


## Damien Edwards – ASP.NET 4.5

Damien started his talk by presenting a new responsive theme (which has been around for a while), new script features (like bundling), login services (using Facebook or Twitter), etc.

After describing .NET's async history, from APM (Asynchronous Programming Model, with the old Begin/End pattern), through EAP (Event-based Asynchronous Pattern) and via TAP (Task-based Asynchronous Pattern), Damien moved on to the new async features of .NET.

Damien showed how to use these features and the traps you can fall into. In the demo, he showed async page loads and new model binding functionality for ASP.NET data controls. All in all, this was a really good and interesting talk.


## Alex Papadimoulis – Ugly code

This was the best and most entertaining talk of the day. Alex is the founder of the **Daily WTF** (Worse Than Failure) and started his talk by playing a little game with us.

The game - "Ugly or not" - consisted of him showing us code, after which we would say if we found it ugly. Some examples were a piece of MUMPS code, which looked horrible (hold on, we'll get back to this), code for a flight simulator shaped like an airplane, etc.

Alex wrapped up the game by stating that beauty is in the eye of the beholder, while ugly is just plain ugly. He then listed five different types of ugly code:

* **Dilapidated and Decrepit** - code that has passed through too many non-caring
hands, and that should have died a long time ago.

* **Complete Clusterfrack** - code with high complexity, often the result
of a lot of thought, often a lot of wrong thought.

* **Maddening Mismatch** - code that consists of many good parts, that goes
bad together.

* **Disastrously Dishelved** - code that is just pure mayhem (illustrated with a
room filled to the brink with stuff), in which the creator finds his/her way.

* **Complex and Convoluted** - code with unnecessary, often dumb, complexity.

Unlike ugly code, however, we also have code that is just not that pretty:

* **Old and Over the Hill** - old, ugly code, that may however still work...maybe even well.

* **Freakishly Foreign** - code that we don't understand, since we're
not familiar with it.

This is where things got interesting. Returning to the MUMPS code, we can't tell if it's ugly or not, since we don't know how it’s supposed to look. Maybe it's amazing MUMPS code?

Alex then listed the developers who create ugly code:

* **Clueless coders** - They come, they work, they leave. They seldom care.

* **Cowboy coders** - The boss' best friend. Gets shit done without letting annoying things like regulations, processes etc. get in the way. Gets shit(!) done.

* **Clever coders** - They know what they do, they care about their craft...and
when they do, they create the worst pieces ugly code you can imagine.

So, why are clever coders so dangerous? Because cleverness is unavoidable, which Alex showed with some great examples of how bad things can go when you try to be clever. Clever coders tend to come up with complex solutions, since complex solutions are FUN!

Now that we have identified the developer types, how to we tame them?

* **Clueing-in the clueless coders** - Arrange fun activities with food and drinks,
where they can also learn new stuff.

* **Corral the cowboys** - Cowboy coders hate processes, so surround them by code
reviews, testing etc., until they adapt or leave.

* **Cease the cleverness** - Alex had a bike/glove analogy that I missed to jot down.

When you are about to remove pieces of ugly code, ask yourself if you are really (really) sure you know what you are doing. Do you understand what the code does? Can some unintentional side-effects be critical to the system?

If you still decide to proceed, stay committed! Rewriting is boring and tedious, with the end result being code that does exactly the same thing (should have zero noticable impact). To give up halfways in will make things worse!

All in all, a really awesome session  - check it out!


## Dennis Gustafsson – Optimizing Mobile Games

Dennis and I studied at the same university. Back then, I was always impressed by what he and his colleagues put together. For instance, they started a physics engine company that got acquired by NVIDIA, then created and sold Dresscode - a code analysis product, etc.

...while I coded away on my php-based "system" that never went anywhere.

Dennis talked about his mobile app game *Sprinkle*, which has been downloaded more than 7 million times. He described how the it grew from an idea to a successful multi-platform game, some design and technology choices they made during development, and how they implemented things like the water physics.

Dennis' talk was very detailed. Where other developers may have kept the inner workings of a successful app a secret, Dennis described everything with a super-high level of detail. Input abstractions, audio, graphics, aspect ratio handling, CPU optimizations, the particle system design - nothing was left out, not even the amazing water simulation.

This great talk was focused on technology, so if you create, or want to create, advanced games for mobile devices, I really suggest that you check it out.