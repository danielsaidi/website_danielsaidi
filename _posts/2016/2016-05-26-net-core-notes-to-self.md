---
title:  ".NET Core notes to self"
date:   2016-05-26 07:52:00 +0100
tags: 	.net unit-testing
---


I have been playing around with .NET Core since the early betas, but since I do
so with rather long times in between, things break each time I decide to pick up
from where I started.

This means that I often have to start from scratch, since the different versions
have conflicts, old versions are not being unlinked etc. This can cause really
frustrating problems, since the new versions you've installed are not being used,
or worse, being **partially** used.

So, this is just a quick brain dump of what I have to do when I bumped my .NET
Core environment up to the RC2 Preview 1.


## Links

First, some useful links:

 * [The .NET Core home page](https://www.microsoft.com/net/core)
 * [The ASP.NET Core home page](https://www.asp.net/core)
 * [A nice getting started guide for ASP.NET RC2 Preview 1](https://docs.asp.net/en/1.0.0-rc2/getting-started.html)


## Gettings Started

Before getting started, keep the following in mind:

 * Make sure to completely remove `dnx` and `dnvm`
 * Remove any previous versions of `.NET Core`, using the scripts Microsoft provides
 * Ensure that the `dnx`, `dnvm`, and `dotnet` are completely removed

Once this is done, do the following:

 * Install the latest .NET Core release
 * Install the latest Visual Studio Code release
 * Install the C# plugin for Visual Studio Code (`cmd+p` then search for `c#`)

The fact that C# is a plugin to Visual Studio Code always escapes me, causing me
to have a full .NET Core environment, but no IntelliSense. This is (at the time
of writing) nowhere to be found in the getting started guides (correct me if I'm
wrong).


## Unit Test

To setup your project to use XUnit as a test runner, add these dependencies:

 * "xunit": "2.1.0"
 * "dotnet-test-xunit": "1.0.0-rc2-build10015"

Add XUnit as a test runner by adding this below the dependencies node:

 * "testRunner": "xunit"

Then, finally add the following import for netcoreapp1.0:

 * "portable-net45+win8"

Now, you should be able to run unit tests, using the `dotnet test` command.


## Watch

If you don't want to have to manually run the `dotnet run` and the `dotnet test`
commands each time you make a code change, you can add DotNet Watcher to your project.

To add it, add a new `tools` node, or add the watcher tool to the tools node if
you already have it:

```
"tools": {
   "Microsoft.DotNet.Watcher.Tools": {
      "version": "1.0.0-*",
      "imports": "portable-net451+win8"
   }
}
```

You should now be able to run `dotnet watch run` and `dotnet watch test` to
automatically restart your application or re-run your tests as soon as any code
in your project changes.




