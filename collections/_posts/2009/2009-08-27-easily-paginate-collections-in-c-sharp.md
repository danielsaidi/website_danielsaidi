---
title: Easily paginate collections in C#
date:  2009-08-27 08:00:00 +0100
tags:  .net c#
icon:  dotnet
---

This post looks at how to easily paginate collections in C#, which can be easily
achieved with two very basic extensions.

When paginating a collection in C#, I find the following extensions useful:

```csharp
public static IEnumerable<TSource> Paginate<TSource>(this IEnumerable<TSource> source, int? page, int pageSize)
{
    return source.Skip((page ?? 0) * pageSize).Take(pageSize);
}
```

```csharp
public static IQueryable<TSource> Paginate<TSource>(this IQueryable<TSource> source, int? page, int pageSize)
{
    return source.Skip((page ?? 0) * pageSize).Take(pageSize);
}
```

By adding them to an extension class within a certain namespace, all `IQueryable`
and `IEnumerable` instances will automatically receives this functionality:

```csharp
List<string> strings = new List<string> { "a","b","c","d","e","f" };
strings.Paginate(2, 2);
```

The pagination code above will return a list that contains “c” and “d”.