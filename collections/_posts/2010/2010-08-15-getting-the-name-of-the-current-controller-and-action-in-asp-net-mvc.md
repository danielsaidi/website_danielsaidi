---
title: Getting the name of the current controller and action in ASP.NET MVC
date:  2010-08-15 12:00:00 +0100
tags:  .net c# web
---

I have been trying to find info on how to get the name of the current controller
and action in ASP.NET MVC. Eventually, I found out how to:

```csharp
string controllername = ValueProvider.GetValue("controller").RawValue.ToString();
string actionname = ValueProvider.GetValue("action").RawValue.ToString();
```

It's not that straightforward, but it works. Maybe it's time to create a helper :)