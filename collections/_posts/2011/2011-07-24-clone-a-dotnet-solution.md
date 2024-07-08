---
title: Clone a .NET solution in no time
date:  2011-07-24 12:00:00 +0100
tags:  archive
icon:  dotnet

link:  https://github.com/danielsaidi/cloney
---

When working with .NET, I sometimes find myself wanting to just clone a solution instead of setting everything up from scratch over and over again.

For instance, you may want to clone a project stub, where you can reuse code, structure and setup that isn't suitable for a shared library.

The biggest problem with cloning a .NET solution by manually copying it, is that you have to replace everything that has to do with the old namespace, which takes a LOT of time. 

For instance, consider a solution `X`, where `X` is the base namespace, that has several projects like `X.Core`, `X.Domain`, etc. To clone `X` into a new solution called `Y`, we must rename the solution, all its projects, and all references to `X`, including in code and text.

Since manually doing this takes time and is error-prone, I have created an application that makes cloning .NET solutions easy. It's called Cloney and can be downloaded [here]({{page.link}}).

With Cloney, you just have to point out a source folder that contains the solution to clone, as well as a target folder to where you want to clone it. 

When you press the "Clone" button, Cloney will:

- Copy all folders and files from the source folder
- Ignore certain folders, such as bin, obj, .git, .svn, _Resharper*
- Ignore certain file types, such as *.suo, *.user, *,vssscc
- Replace the old namespace with the new one everywhere

You will end up with a new, clean solution without any traces of any old settings, version
control-related folders & files, etc.

Feel free to download Cloney [here]({{page.link}}) and let me know what you think.