---
title:  "Øredev 2011 in the rear-view mirror – Part 5"
date:    2012-01-20 12:00:00 +0100
categories: conferences
tags: 	conference
---


This is the fifth part of my sum-up of Øredev 2011. These sum-ups waere supposed
to be rather short, but have grown out of proportions. I will try to keep it down.

 

## 2:7 – Greg Young – How to get productive in a project in 24h

In his second talk, Greg Young talked about how to kick-start yourself for a new
project. First of all, as a sonsultant, do you understand what the company does?
Have you used your their product or service before? If you have no understanding
of these fundamental facts, how will you deliver value?

Greg then described some tricks he use to get started quickly. He usually starts
off by inspecting the CVS. Projects that have been around for a while, and still
have tons of checkins, could possibly be suffering from a lot of bugs. A certain
area that have massive amounts of checkins could possibly be a bug hive.

So look there first. It could quickly tell you where a project hurts. Naturally,
a lot of checkins do not automatically indicate bugs or problems. The team could
just be developing stuff. However, at the very least, many checkins do mean that
people are working in that part of the project.

This is a very simple step, but will make you able to discuss the project after
just an hour or so with the CVS, and maybe even pin-pointing some problems. This
will give your customer the impression that you are clairvoyant or at least that
you know what you are doing...which is why they pay you.

If the project does not have continuous integration, at least set it up locally.
It doesn't take long and will help you out tremendously. To be able to yell at
someone who breaks the build the second they do it...well, at least it gives you
pleasure.

Greg then went on to demonstrate how you can dig even deeper, using NDepend. His
demo was awesome. Patrick should consider giving him...well, a hug at least.

Using NDepend, Greg demonstrated how to use the various metrics, like cyclomatic
complexity and afferent/efferent coupling. He showed us various graphs that make
NDepend great then told us to specially keep an eye out for black squares in the
dependency matrix (they indicate circular references...bad) and rigid couplings
(they should be broken up).

All in all a great session that gave me a lot of things to aim for when holding
presentations myself. As a consultant, you should not miss this video.

 

## 3:1 – Keynote – Jeff Atwood – Stack Overflow: Social Software for the Anti-Social Part II: Electric Boogaloo

I will not attempt to cover everything said in this keynote. Instead, you should
watch the video. It is filled with fun gems, like when Jeff describes how things
that are accepted in a web context would be really strange if applied in the real
life. For instance, FB lets you keep a list of friends. Who has a physical list
of friends in real life?

Jeff then talked gamification and how we can design our services like a game, by
defining a set of rules that describe how the service is to be used, then reward
those who adapt the rules and punish the ones that do not. The basic premise is
that games have rules and games are fun, so if we design our services as a game,
they should become fun as well. Right?

Well, at least rules tells us how we are supposed to behave. It doesn't work for
all sites or services, but for social software, gamification should be considered.
Games generally make social interaction non-scary, since everyone has to conform
to the rules. Just look at the world.

When designing Stack Overflow, Jeff and Joel therefore did so with gamification
in mind. You may not notice it at first, but everything is carefully considered.
For instance, people used to complain that you cannot add a new question at the 
start page. This is intentional. Before you add a question, Stack Overflow wants
you to read other questions, see how people interact and learn the rules.

Stack Overflow adapts several concepts from the gaming world. "Good" players are
rewarded with achievements and level up as they progress. The site has tutorials,
unlockables etc. Without first realizing it, Jeff and Joel did end up creating a
Q&A game that consists of several layers:

- The game – ask and answer questions
- The meta-game – receive badges, level up, become an administrator etc.
- The end-game – make the Internet a little better

This design makes it possible for Stack Overflow to allow anonymous users, unlike
Facebook who decided to only allow real names in order to filter out the “idiots”.
Since Stack Overflow award good players, bad players are automatically sorted out.
The community is self-sanitizing. People are awarded with admin privileges if they
play good enough. It’s just like Counter Strike, where you are forced to be a team
player. If you are not, the game will kill you 🙂

I could go on and on, but I recommend you to watch the video instead.

 

## 3:2 – Tim Huckaby – Building HTML5 Applications with Visual Studio 11 for Windows 8

Tim has worked with (not at) Microsoft for a loooong time and is one charismatic
guy, I must say. What I really appreciated with his session was that it seemed a
bit improvised, unlike most sessions at Øredev. 

What I didn't like quite as much, though, was that it seemed too improvised. Due
to lack of time and hardware issues, Tim failed to demonstrate what I came to see:
HTML5 applications with VS11.

Tim begun with stating that he hates HTML, but that he loves HTML5, which is now
“crossing the chasm”. This means that it is a safe technology to bet on, because
it will be adapted. 

How do we know this? Well, this graph illustrates when a technology is “crossing
the chasm” in relation to how people adapt it:

![The Chasm Graph](/assets/blog/2012-01-20.png "The Chasm Graph")

When a technology is “crossing the chasm”, get to work – cause it will be used 🙂
I wonder how the graph would have looked for HD-DVD?

Tim thanked Apple for inventing the iPadm since thanks to the iPhone and the iPad,
Flash and plugins are out and HTML5 is in.

Large parts of this talk were fun anecdotes, like when he talked about how Adobe
once published a “we ♥ Apple” campaign, to which Apple responded with “we MISSING
PLUGIN Adobe”. Hilarious, but did we learn anything?

Tim went through some browser statistics, explained why IE6 is still widely used
(damn those piracy copies of Win XP in China) and ended up with some small demos,
but faced massive hardware problems and promised more if we stayed a while, so I
stayed but the demos were not that wow.

 

## 3:3 – Tim Huckaby – Delivering Improved User Experience with Metro Style Win 8 Applications

Tim started this talk with NUI **Natural User Interfaces** and some new features
of Windows 8, like **semantic zoom**, a desktop mode behind Metro (it looks great,
just like Win 7!), smart touch and a new task manager (he was kinda ironic here).

Tim demonstrated Tobii on a really cool laptop with two cameras, which allows it
to see in 3D. The rest of the session was...enjoyable. I cannot put my finger on
it, but I had fun, although I was disappointed by the demos. The Kinect demo was
semi-cool, a great Swedish screen was also interesting and Tim also hinted about
how the new XBOX Loop and a new Kinect will become a small revolution.

I really do not know what to say about this. Watch the video. You will have fun.


