---
title:	"ConstraintException thrown when posting empty data for non-nullable properties"
date:	2010-07-05 12:00:00 +0100
tags: 	.net entity-framework
---


I am currently working with model validation in ASP.NET, using an Entity Framework 4
entity model, `DataAnnotations` and partial classes with `MetadataType` connections.

In my model, I have an `Employee` entity for which some properties are non-nullable.
I have also created a partial class and a meta data class for this class, to be used
with model validation, as described in [this blog post](http://weblogs.asp.net/scottgu/archive/2010/01/15/asp-net-mvc-2-model-validation.aspx#7311799).

This approach works great. The Employee class is validated properly, with a minimum
effort. My entities are validated with standard validation attributes, as well as a
set of custom ones. Lovely.

However, the application crashes when I post empty text input elements in the Create
and Edit views. A `ConstraintException` is thrown before any view controller actions
are executed, which means that I cannot act on the constraint exception in my action.

The exception occurs since the empty posted data causes the corresponding properties
on the `Employee` class to be set to `null`, which conflicts with the non-null setup
in the entity model.

However, since I have custom validation classes where I use `Required` attributes to
make properties mandatory, I do not need the non-nullable attributes in my model. As
such, I set the nullable property to (None)...and the ConstraintException is history!