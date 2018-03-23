---
title:  "Find all classes that inherit a certain class"
date:   2009-05-25 20:23:00 +0100
tags: 	.net c#
---



Sometimes, it may be handy to retrieve all classes that inherit a certain
class. This is not hard, but perhaps a bit obscure:

```csharp
public static IEnumerable<Type> GetClasses(Type baseType)
{
    var assembly = Assembly.GetCallingAssembly();
    return assembly.GetTypes().Where(type => type.IsSubclassOf(baseType));
}
```

I hope this helps.