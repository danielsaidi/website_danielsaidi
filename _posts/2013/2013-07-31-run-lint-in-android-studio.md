---
title:  "Run Lint in Android Studio"
date: 	2013-07-31 14:29:00 +0100
categories: mobile
tags: 	android lint
---


![Image of an Android teacher](/assets/img/blog/2013-08-05-android.png)


As I have just started learning Android, I was happy to see that lint is such an
integral part of the development process. Lint can be used to enforce general or
custom code conventions and raise warnings or errors for badly formatted code. A
great tool, in other words. [Read more about linting here](https://en.wikipedia.org/wiki/Lint_(software))

Android Studio comes with built-in support for lint. To runt lint and analyze an
Android project, simply select `Analyze > Inspect Code`.

In the popup window that appears, you can set the scope of the analysis, such as
which projects to analyze and which to skip. For my test, I just chose to analyze
everything and ended up with this nice summary:

![Android Lint Summary](/assets/img/blog/2013-07-31-lint.png)

When you browse through the resulting report, you will notice that many warnings
can just be ignored, such as spelling (which catches a lot of "typos" in strings).
The project name, for instance, can be reported as a typo. The report will also
include invalid XML in generated files, over which you have no control.

However, things like the items under Android lint and Declaration redundancy are
highly interesting, and will be a great help for a newbie like me.