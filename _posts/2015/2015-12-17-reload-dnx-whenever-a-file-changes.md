---
title:  "Reload DNX whenever a file changes"
date:   2015-12-17 20:39:00 +0100
categories: dotnet
tags: 	dotnet-core visual-studio-code
---

I am currently developing a console app in .NET Core on my Mac. It's a rather nice
experience, although I miss a lot of stuff from Visual Studio - expecially R#.

In Visual Studio Code, I find it tedious to write a bunch of code, then having to
leave Visual Studio Code to run `dnx run` from the terminal to verify that my code
compiles, then run `dnx test` to verify that my unit tests still pass.

Luckily, there is a tool called *dnx-watch*, that will reload dnx as soon as any
file in your project changes.

To install *dnx-watch*, make sure that you have updated your stack with the latest
versions, using *dnvm*, then install dnx-watch with this command:

```
dnu commands install Microsoft.Dnx.Watcher 1.0.0
```

In my console app, I have a console app project and a test project. Since the test
project runs code from the app project, I just have to run `dnx-watch test` in the
test project's root folder. dnx-watch will then fire as soon as I change a file in
either the app or the test project.

I can now stay in Visual Studio Code, write a bunch of code and see my tests run
each time I save a file in either project.