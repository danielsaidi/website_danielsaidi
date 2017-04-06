---
title:  "Scheduling NDepend for a set of solutions"
date:    2011-10-05 12:00:00 +0100
categories: dotnet
tags: 	ndepend
---


In a project that I am currently working on, I use NDepend to continuously run a
scheduled code analysis on a bunch of solutions that make up a large part of the
software infrastructure of a major Swedish company.

By scheduling these analyses to run once a week, using the previous results as a
baseline for comparison, I hope that this will make it easier to detect patterns
that we want to avoid and pin-point good practices that we want to embrace.

Although we use Team City as build server, I have setup these scheduled analyses
to run from my personal computer during this first test phase. It is not optimal,
but for now it will do.

The analyses are triggered from a simple bat script, that does the following:

- It first checks out each solution from source control
- It then builds each solution with `devenv`
- It then run a pre-created NDepend analysis for each solution
- Each analysis is configured to publish the HTML report to a web server that is available for everyone within the project

Once I had created the script, I scheduled it using the `Task Scheduler`.  I set
it to run every Monday morning at 8.30. Since it runs from my personal computer,
I have to be early at work, but with two kids at home, I always am 🙂

This setup works like a charm. The analyses runs each week and everyone is happy
(at least I am). Already after the first analysis, we noticed some areas that we
could tweak to drastically improve the architecture, reduce branch / merge hell,
avoid code duplication etc.


## One small tweak

During the setup phase, the bat script sometimes could not get NDepend to launch
and run these analyses. If the code was unchanged, NDepend just would not run. I
however noticed that the solution was simple – under `Tools / Options / Anaysis`,
you can tell NDepend to always run a full analysis:

![NDepend](/assets/img/blog/2011-10-05-2.png "NDepend")

In most cases this default setting is correct, since it will run a full analysis
at least once per day. However, in this case, I decided to keep the “Always Run
Full Analysis” option selected.


## One final, small problem – help needed!

One small problem that still is an issue, is that the NDepend projects sometimes
begin complaining that the solution DLLs are invalid even when they are not. The
last time this happened (after the major architectural changes), I could not get
this to work even by deleting and re-adding the DLL:s. The NDepend project still
considered the DLLs to be invalid.

To solve this problem, I had to delete the NDepend projects, then re-create them
from scratch.

Has anyone had this problem?