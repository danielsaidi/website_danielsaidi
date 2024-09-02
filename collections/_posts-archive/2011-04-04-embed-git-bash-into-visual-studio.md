---
title: Embed Git Bash into Visual Studio
date:  2011-04-04 12:00:00 +0100
tags:  archive

post:  http://coderjournal.com/2011/03/adding-git-command-line-to-visual-studio
---

I have started to use git in my .NET projects. It works really well, but I'm having
problems with integrating it in Visual Studio. Turns out there's a way to add a Git
Bash window to Visual Studio.

Even though I use graphical tools like `TFS` and `AnkhSVN` for other version control
systems, I don't like to work with git through Git Extensions. I instead prefer to
use the OS X Terminal, which is how I would like to work with git on Windows as well.

I however find it annoying to have the git command prompt in a separate window when
working in Visual Studio, when IDE:s like Aptana Studio embeds it so well.

After a quick search, I found [an article]({{page.post}}) that shows you how to embed
git in Visual Studio, which means that I will hopefully be able to add a git panel
bottommost in Visual Studio.