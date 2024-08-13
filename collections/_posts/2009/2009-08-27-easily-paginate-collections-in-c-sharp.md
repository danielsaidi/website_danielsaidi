---
title: Easily paginate collections in C#
date:  2009-08-27 08:00:00 +0100
tags:  archive
---

In this post, we'll create two extensions that lets us easily paginate any collection in C#.

When paginating a collection in C#, I find the following extensions useful:

```csharp
public static IEnumerable<TSource> Paginate<TSource>(
    this IEnumerable<TSource> source, 
    int? page, 
    int pageSize
) {
    return source.Skip((page ?? 0) * pageSize).Take(pageSize);
}
```

```csharp
public static IQueryable<TSource> Paginate<TSource>(
    this IQueryable<TSource> source, 
    int? page,
    int pageSize
) {
    return source.Skip((page ?? 0) * pageSize).Take(pageSize);
}
```

With this, all `IQueryable` and `IEnumerable` instances automatically gets this functionality:

```csharp
List<string> strings = new List<string> { "a","b","c","d","e","f" };
strings.Paginate(2, 2);
```

The pagination code above will return a list that contains “c” and “d”.