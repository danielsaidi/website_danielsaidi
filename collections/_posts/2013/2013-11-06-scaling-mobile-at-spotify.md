---
title: Scaling mobile at Spotify
date:  2013-11-06 12:59:00 +0100
categories: conferences
tags:  conference

assets: /assets/blog/2013/131106/
image:  /assets/blog/2013/131106/spotify.jpg
---

This is a sum-up of a talk I attended at Øredev 2013, where Mattias Björnheden 
and  Per Eckerdal from Spotify talked about how Spotify scaled their mobile teams.

![Spotify logo]({{page.image}})


## Mattias Björnheden

Mattias talked about how Spotify started out with two small teams, one for iOS and
one for Android. With a shared core C library, the teams initially just wrote a thin
client layer around the core library. As things later took off and mobile got real, 
the number of developers grew to about 30 iOS and 20 Android devs.

Obviously, the team setup had to change. Spotify re-organized the teams into feature
oriented teams, which resulted in each team owning a certain feature for all clients
and environments. This is made possible by having several cross-functional teams and
means that there are no iOS or Android teams anymore, but rather teams that deliver
features to all supported platforms. The release cycle has been shortened to a minimum, 
compared to last year when new releases were painful.


## Per Eckerdal

After Mattias, Per covered Spotify's mobile architecture, with concrete examples like
the search function, to explain how the tech stack looked in early 2013 and how it
eventually became too painful to work with. Each layer grew dauntingly big, with only
one team understanding it. If you wanted to add things to Core, the Core team was the
only team that could do so. This caused a lot of waiting. And waiting sucks.

Spotify reached out to learn from other companies, like Netflix. This is something
Spotify excels in, and I'm not alone in being grateful for their part in Stockholm's
meetup scene. Inspired by Netflix, Spotify structured their data in a view model setup,
which means that each client just have to display the data it's given. The apps are all native, but their native code is rather small and have no decision logic of their own.
They just grab view data from the API, parse it, then use it in the native views.


## Wrapping up

Mattias came back up on stage and talked about global collaboration. Their first
step to move away from the old team setup was reorganize into feature teams, as
mentioned above. This caused branch merge complications, which at one time led
to that 2/3 of the features written for Android were never released. The current
setup is much better, though, and also allows for A/B testing, gradual rollouts
etc. Although several challenges remain, they are at a much better pace now than
they were a year ago.

Per wrapped up the session with a live demo of their QA tools, for instance load
time indicators, memory monitoring, feature flag management, face recording, an 
embedded log monitor etc. 

A perfect conclusion to a great session.

[Check out the video here](http://oredev.org/oredev2013/2013/videos.html).