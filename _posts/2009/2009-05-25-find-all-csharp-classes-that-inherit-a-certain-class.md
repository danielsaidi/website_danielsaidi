---
title:  "Find all classes that inherit a certain class"
date:   2009-05-25 20:23:00 +0100
tags: 	.net c#
---



In some situations, it may be handy to retrieve all classes that inherit
a certain base class.

This is by no means hard, but perhaps a bit obscure:

```csharp
public static IEnumerable<Type> GetClasses(Type baseType)
{
    var assembly = Assembly.GetCallingAssembly();
    return assembly.GetTypes().Where(type => type.IsSubclassOf(baseType));
}
```

Hope this helps.