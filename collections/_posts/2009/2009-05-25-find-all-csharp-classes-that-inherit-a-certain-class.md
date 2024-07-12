---
title: Find all classes that inherit a certain class
date:  2009-05-25 20:23:00 +0100
tags:  archive
icon:  dotnet
---

In a typed language, it can be handy to retrieve all types that inherit a certain base class. Let's have a look at how to do this.

You first need to create an assembly reference, where the calling assembly is the one that is executing your code.

You can then retrieve all types in that assembly and filter the collection to find all types that inherit the class of interest.

```csharp
public static IEnumerable<Type> GetClasses(Type baseType)
{
    var assembly = Assembly.GetCallingAssembly();
    var allTypes = assembly.GetTypes();
    return allTypes.Where(type => type.IsSubclassOf(baseType));
}
```

Note that this will only return the types in the provided assembly. If you have a distributed system with multiple assemblies, you have to check all of them.