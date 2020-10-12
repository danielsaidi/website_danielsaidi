---
title:  "NUnit BadImageFormatException"
date:   2011-05-27 12:00:00 +0100
tags: 	.net testing
---


While developing a unit tested hobby project in .NET, everything has worked great
until now. Suddenly, NUnit thinks that there is something wrong with an assembly:

![BadImageFormatException](/assets/blog/2011-05-27.png)

So far, this assembly only contains two classes, so the easiest would be to just
delete it and create a new project and hope for the best...but I have this thing
for wanting to know what is causing this problem.

However, my ambitions were abandoned when deleting and recreating these projects
solved all my problems. I would have posted a solution here, but now we’ll never
know what caused this problem in the first place.

Or will we? After writing this blog post, Mikey posted a comment that pointed me
in the right direction. Turns out that I must have disabled one architecture and
thus caused the test project to fail when using the project.

Thanks Mikey!