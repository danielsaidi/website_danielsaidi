---
title: Upgrade .NET Core RC to 1.0
date:  2016-04-20 08:31:00 +0100
tags:  .net
---

With the release of [Visual Studio Code 1.0](https://code.visualstudio.com/blogs),
I upgraded .NET Core to the latest version. However, the older version wasn't
properly replaced, which did cause Visual Studio Code to behave quite strange.

After installing .NET Core 1.0 from [here](https://www.microsoft.com/net/core) and
upgrading Visual Studio Code from [here](https://code.visualstudio.com/Download),
I created a new .NET Core project with these lines:

```
mkdir hwapp
cd hwapp
dotnet new
dotnet restore
dotnet run
```

At first, the project seemed to run without problems. However, when I opened it in
Visual Studio Code, I got warnings that the project couldn't load, that the project
missed an .sln file, that Omnisharp couldn't find the "default" runtime etc.

After investigating this strange behavior, I found that older versions of dnx and
dnvm were not properly installed and conflicted with the new setup. I tried to solve
this by upgrading dnvm, removing all old versions and reinstalling the latest versions,
but this didn't work.

Turns out that in order to get the new setup to work, I also had to specify an alias:

```
dnvm update-self
dnvm list -detailed
dnvm uninstall VERSION -r coreclr
dnvm uninstall VERSION -r mono
dnvm install latest -r coreclr -alias default
dnvm install latest -r mono -alias default
```

After this, I could start Visual Studio Code and run Omnisharp without problems.
