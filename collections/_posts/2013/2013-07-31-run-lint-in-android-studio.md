---
title: Run Lint in Android Studio
date:  2013-07-31 14:29:00 +0100
tags:  android lint

assets: /assets/blog/2013/130731/
image: /assets/blog/2013/130731/header.png

linting: https://en.wikipedia.org/wiki/Lint_(software)
---

As I have just started learning Android, I was happy to see that linting is such 
an integral part of the Android development process. Let's see how to set it up 
in Android Studio.

![Image of an Android teacher]({{page.image}})

For those of you who don't know about linting, it's a way to enforce general or
custom code conventions and raise warnings or errors for badly formatted code. It's
a great tool, that's however not as common in the iOS toolchain and Xcode has no
built-in tools for it. [Read more about linting here]({{page.linting}}).

Android Studio comes with built-in support for lint. To runt lint and analyze an
Android project, simply select `Analyze > Inspect Code`. In the popup window that
appears, you can set the scope of the analysis, such as which projects to analyze
and which to skip. 

For my test, I just chose to analyze everything and ended up with this nice summary:

![Android Lint Summary]({{page.assets}}lint.png)

When you browse through the report, you'll notice that many warnings can just be
ignored, such as spelling (which catches a lot of "typos" in strings). The project 
name, for instance, can be reported as a typo. The report will also include invalid 
XML in generated files, over which you have no control.

However, you can specify rules that you want to ignore and even add your own rules,
which means that you can tailor this tool to your own needs. You can automate it
to run during the build process, when generating APKs etc. For a newbie like me, it
also provides a way to learn the Android conventions.

If you're new to linting, I really recommend checking it out. It's a great tool
that will make your code a lot more consistent, adn can even help you find bugs.