---
title:  "Easily paginate collections in C#"
date:   2009-08-27 08:00:00 +0100
tags:	.net c#
---


When paginating collections in C#, I find the following extension methods useful:

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
and `IEnumerable` objects automatically receives this functionality.

You will then be able to write (for instance):

```csharp
List<string> strings = new List<string> { "a","b","c","d","e","f" };
strings.Paginate(2, 2);
```

And get a list that contains “c” and “d”.