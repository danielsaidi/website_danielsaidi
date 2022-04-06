---
title: Scheduling NDepend for a set of solutions
date:  2011-10-05 12:00:00 +0100
tags:  .net ndepend
---

In a project of mine, I use NDepend to continuously run a scheduled code analysis
on a bunch of solutions that make up a large part of the software infrastructure
of a major Swedish company.

By scheduling these analyses to run once a week, using previous results as a
baseline for comparison, it will be easier to detect patterns to avoid and pin-point
good practices that we want to embrace.

Although we use Team City as build server, I have setup these scheduled analyses
to run from my personal computer during this first test phase. It's not optimal,
but will do for now.

The analyses are triggered from a simple bat script, that does the following:

- It first checks out each solution from source control.
- It then builds each solution with `devenv`.
- It then run a pre-created NDepend analysis for each solution.
- Each analysis is configured to publish the HTML report to a web serve.
- The web server is available for everyone in the project.

Once I had the script, I scheduled it using the `Task Scheduler`.  I set it to
run every Monday at 08.30. Since it runs from my computer, I have to be early at
work, but with two kids at home, I always am 🙂

This setup works like a charm. The analyses runs each week and everyone is happy
(at least I am). Already after the first analysis, we noticed some areas that we
could tweak to drastically improve the architecture, reduce branch / merge hell,
avoid code duplication etc.


## One small tweak

During the setup, the bat script sometimes couldn't get NDepend to launch. If the
code was unchanged, NDepend just wouldn't start. The solution was however simple.
Under `Tools / Options / Anaysis`, you can tell NDepend to always run a full analysis:

![NDepend](/assets/blog/2011/2011-10-05-2.png "NDepend")

In most cases the default setting is correct, since it will run a full analysis
at least once per day. However, in this case, I decided to keep the “Always Run
Full Analysis” option selected.


## One final, small problem – help needed!

One remaining problem is that NDepend sometimes incorrectly complains that the
solution DLLs are invalid. The last time this happened (after a major architectural
change), I couldn't get it to work even by deleting and re-adding the DLL:s. NDepend
still considered the DLLs to be invalid.

To solve this, I had to delete the NDepend project and re-create it from scratch.
It's not ideal, so I hope that I will find a solution for it.