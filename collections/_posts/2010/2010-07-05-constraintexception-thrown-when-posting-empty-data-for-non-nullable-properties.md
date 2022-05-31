---
title: ConstraintException is thrown when posting empty data for non-nullable properties
date:  2010-07-05 12:00:00 +0100
tags:  .net entity-framework
icon:  dotnet
---

I am working with model validation in .NET, Entity Framework 4, `DataAnnotations` and
partial classes with `MetadataType` connections and now have a problem where exceptions
are thrown when posting empty data for non-nullable properties.

In my model, I have an `Employee` entity for which some properties are non-nullable.
I also have a partial class and a metadata class, to be used with model validation, 
as described in [this blog post](http://weblogs.asp.net/scottgu/archive/2010/01/15/asp-net-mvc-2-model-validation.aspx#7311799).

This approach works great. `Employee` is validated properly, with minimum effort. My
entities are validated with standard validation attributes as well as a set of custom
ones. Lovely.

However, the application crashes when I post empty text. A `ConstraintException` is
thrown before any view controller actions are executed, which means that I can't act
on the exception.

The exception occurs since the empty posted data causes the corresponding properties
on the `Employee` class to be set to `null`, which conflicts with the non-null setup
in the entity model. However, since I have custom validation classes that use 
`Required` attributes to make properties mandatory, I don't need the non-nullable
attributes in my model. 

As such, I can set the nullable property to (None) and the exception is now history!