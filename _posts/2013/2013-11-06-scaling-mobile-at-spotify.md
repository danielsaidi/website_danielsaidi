---
title:  "Scaling mobile at Spotify"
date: 	2013-11-06 12:59:00 +0100
categories: conferences
tags: 	conference
---


![Spotify logo](/assets/blog/2013-11-06-spotify.jpg)


After the morning's non-tech keynote, I went to watch Mattias Björnheden and Per
Eckerdal from Spotify talk about how Spotify managed to scale their mobile teams.


## Mattias Björnheden

Mattias talked about how Spotify started out with two small mobile teams, one for
iOS and one for Android. With a core C library that both teams shared, the teams
initially just wrote a thin client layer on top of the core library. Then, things
took off and mobile got real. The number of developers grew fast, to about 30 iOS
developers and 20 Android developers.

Obviously, the team setup eventually had to change. Spotify then re-organized the
teams into featured-oriented teams, which resulted in each team owning a certain
feature for all clients and environments. This is made possible by having several
cross-functional teams. This means that there are no iOS team and no Android team
anymore, but rather teams that deliver features to all supported platforms.
The release cycles has been shortened to a minimum, which was not the situation
last year, when new releases were painful.


## Per Eckerdal

After Mattias, Per covered Spotify's mobile architecture, with concrete examples
like the search function, to explain how the tech stack looked in early 2013 and
how it eventually became too painful to work with. Each layer grew dauntingly big,
with only one team understanding each layer. If you wanted to add things to Core,
the Core team was the only team that could do so, which caused a lot of waiting.

And waiting sucks.

Spotify reached out to learn from other companies, like Netflix. This is something
Spotify ecels in, and I am not alone to be grateful for their part in Stockholm's
vibrant meetup scene. Inspired by Netflix, Spotify structured their data in a view
model setup, which meant that each client just have to display the data it's given.
The apps are all native, but their native code is rather small and have no decision
logic of their own. They just grab view data from the API, parse it, then use it
in the native views.


## Wrapping up

Mattias came back up on stage and talked about global collaboration. Their first
step to move away from the old team setup was reorganize into feature teams, as
mentioned above. This caused branch merging complications, which at one time led
to that 2/3 of the features written for Android were never released. The current
setup is much better, though, and also allows for A/B testing, gradual rollouts
etc. Although several challenges remain, they are at a much better pace now than
they were a year ago.

Per wrapped up the session with a live demo of their QA tools, for instance load
time indicators, memory monitoring, feature flag management, face recording, an 
embedded log monitor etc. A perfect conclusion to a great session.

[Check out the video here](http://oredev.org/oredev2013/2013/videos.html).


