---
title: Scaling mobile at Spotify
date:  2013-11-06 12:59:00 +0100
categories: conferences
tags:  conferences

assets: /assets/blog/13/1106/
image:  /assets/blog/13/1106.jpg
---

This is a sum-up of a talk I attended at Øredev 2013, where Mattias Björnheden and Per Eckerdal from Spotify talked about how Spotify scaled their mobile teams.

![Spotify logo]({{page.image}})


## Mattias Björnheden

Mattias talked about how Spotify started with two teams, one for iOS and one for Android. With a shared C library, the teams initially wrote a thin client layer around it. As things later took off, the number of devs grew to around 30 for iOS and 20 for Android.
 
Obviously, the team setup had to change. They reorganized the teams into feature oriented teams, whith each team owning a certain feature for all clients and environments. 

This was made possible by cross-functional teams and means that there are no longer any iOS or Android teams, rather teams that deliver features to all platforms. The release cycle has been shortened to a minimum, compared to last year when new releases were painful.


## Per Eckerdal
 
Per covered Spotify's mobile architecture, with concrete examples like the search function, to explain how the tech stack looked in early 2013 and how it grew too painful to work with. 

Every layer grew dauntingly big, with only one team understanding it. If you wanted to add things to Core, the Core team was the only one that could do so. This led to much waiting.
 
Spotify reached out to learn from other companies, like Netflix. Inspired by Netflix, Spotify structured their data into view models, which means that each client just have to display the data it's given. The apps are all native, but the native code is small and has no decision logic. Each client just grab view data from the API, parse it, then use it in the native views.


## Wrapping up

Mattias came back on stage and talked about global collaboration. Their first step to move away from the old team setup was organize into feature teams, as mentioned above. This caused branch merge complications, which at one time led to that 2/3 of features written for Android were never released. 

The current setup is much better, and allows for A/B testing, gradual rollouts etc. Although several challenges remain, they are at a much better pace now than they were a year ago.

Per wrapped up with a live demo of their QA tools, like load time indicators, handy memory monitoring tools, feature flag management, face recording, an embedded log monitor etc. A perfect conclusion to a great session.

[Check out the video here](http://oredev.org/oredev2013/2013/videos.html).