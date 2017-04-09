---
title:	"Flush the Visual Studio Template Cache"
date:	2011-01-14 12:00:00 +0100
categories: dotnet
tags: 	visual-studio
---


After my last blog post, where I wrote about adding the Spark template engine to
an ASP.NET MVC 2 project, I decided to create a project template that uses Spark,
instead of the default Web Forms view engine.

However, even though I added this template to the Visual Studio project template
folder, it did not turn up in the project list, when creating a new project.

If this happens to you, you must flush the `Visual Studio Template Cache`, which
will make Visual Studio update the list of project templates that are available.

It is really easy to do. In the command prompt, just execute the following:

	devenv /installvstemplates

If your command prompt does not find `devenv`, navigate to the .exe file, which
should be in a folder like:

   C:\Program Files (x86)\Microsoft Visual Studio 10.0\Common7\IDE>

After flushing the cache, open Visual Studio and choose to create a new project.
The new Spark template should appear in the list.