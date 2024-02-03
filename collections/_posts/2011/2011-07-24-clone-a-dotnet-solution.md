---
title: Clone a .NET solution in no time
date:  2011-07-24 12:00:00 +0100
tags:  archive
icon:  dotnet
---

When working with .NET, I sometimes find myself wanting to just clone a solution
instead of setting everything up from scratch over and over again.

For instance, you may want to clone a project stub, where you can reuse code and
structure and setup that are not suitable to be extracted into a base library.

The biggest problem with cloning a .NET solution by copying it, is that you have
to replace everything that has to do with the old namespace. For instance, if you
have a solution X, where X is the base namespace, X can contain several projects,
such as X.Core, X.Domain etc. If you now clone X and call the clone Y, the new
solution and all its projects must be renamed as well. The same goes for any other
references to the name X.

I have therefore created an application that makes cloning a .NET solution easy.
It's currently in beta and can be downloaded [here](https://github.com/danielsaidi/cloney).

With Cloney, you just have to point out a source folder that contains a solution
you want to clone, as well as a target folder to where you want to clone it. When
you then press "Clone", Cloney will:

- Copy all folders and files from the source folder
- Ignore certain folders, such as bin, obj, .git, .svn, _Resharper*
- Ignore certain file types, such as *.suo, *.user, *,vssscc
- Replace the old namespace with the new one everywhere

You end up with a fresh, clean solution without any traces of old settings, version
control-related folders and files etc.

Feel free to download Cloney and give it a try and le me know what you think.