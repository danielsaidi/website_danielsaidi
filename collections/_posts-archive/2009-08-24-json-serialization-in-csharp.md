---
title: JSON serialization in C#
date:  2009-08-24 09:08:00 +0100
tags:  archive

nuget: https://www.nuget.org
---

From time to time, I forget where various classes in the .NET framework are defined.
For my future self and those of you who also struggle, here's a short tutorial on
how to get JSON working in C#.

To get JSON working in C#, you just have to do this:

* Add a reference to `System.Web`.
* Add a reference to `System.Web.Extensions`.
* Add `using System.Web.Script.Serialization` to your class.

You can now serialize and deserialize objects to/from JSON like this:

```csharp
public static T Deserialize<T>(String str)
{
   return (new JavaScriptSerializer()).Deserialize<T>(str);
}

public static String Serialize(Object obj)
{
   return (new JavaScriptSerializer()).Serialize(obj);
}
```

These days, Newtonsoft JSON is more or less standard when it comes to working with
JSON in .NET. You can get it from [NuGet]({{page.nuget}}).