---
title:  "JSON serialization in C#"
date:   2009-08-24 09:08:00 +0100
tags: 	.net c#
---


From time to time, I forget where the various classes are defined. So, here is a
short tutorial to how you get JSON working in C#:

* Add a reference to `System.Web`
* Add a reference to `System.Web.Extensions`
* Add `using System.Web.Script.Serialization` to your class.

You can now serialize and deserialize objects to/from JSON as such:

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

You can now place these functions in a static class, or in a class that implement
a serialization interface etc. If you want testable code, create an interface that
describes a serializer and implement a JSON serializer that uses the code above.

**Addon** These days, Newtonsoft JSON is more or less a standard when it comes to
working with JSON in .NET. Grab it from NuGet.