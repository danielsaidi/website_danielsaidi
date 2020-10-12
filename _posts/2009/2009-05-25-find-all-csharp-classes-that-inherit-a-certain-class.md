---
title:  "Find all classes that inherit a certain class"
date:   2009-05-25 20:23:00 +0100
tags: 	.net c#
---

In typed languages, it may be handy to retrieve all types that inherit a certain
class. This is not hard, but perhaps a bit obscure.

You first need to create an assembly reference, where the calling assembly is the
one executing your code. You can then get all types in that assembly, then filter
the collection to find the types that inherit the class of interest.

```csharp
public static IEnumerable<Type> GetClasses(Type baseType)
{
    var assembly = Assembly.GetCallingAssembly();
    var allTypes = assembly.GetTypes();
    return allTypes.Where(type => type.IsSubclassOf(baseType));
}
```

Note that this will only return the types in the provided assembly. If you have a
distributed system with multiple assemblies, you have to check in all of them.

I hope this helps.