---
title: Getting the name of the current controller and action in ASP.NET MVC
date:  2010-08-15 12:00:00 +0100
tags:  .net c# web
icon:  dotnet
---

I've been trying to find out how to get the name of the current controller
and action in ASP.NET MVC. If you're curious on how to do it, do read on.

Although it's not that nicely implemented in the framework, you can easily
find this info by inspecting the `ValueProvider` like this:

```csharp
string controllername = ValueProvider.GetValue("controller").RawValue.ToString();
string actionname = ValueProvider.GetValue("action").RawValue.ToString();
```

It's not type safe and may change in future versions of ASP.NET MVC, but
it works for now. Just keep an eye out for future changes.