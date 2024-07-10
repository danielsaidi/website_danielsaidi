---
title: DataAnnotations and MetadataType fails in unit tests
date:  2010-07-05 12:00:00 +0100
tags:  dotnet
icon:  dotnet
---

This post describes how to fix model validation for ASP.NET MVC 2, when testing a model that uses DataAnnotations and MetadataType to describe for its validation.

In this case, `ModelState.IsValid` is always `true` by default, since the function that sets it to false for invalid models is never executed by the unit tests. 

This will cause controllers to behave incorrectly while testing, since the valid flag will return the same value all the time.

`MetadataType` bindings are also ignored during test execution. This causes the validation to be ignored as well, which will cause the model to be valid although an object is invalid.


## My situation

I'm currently writing tests for a Create controller. I use NUnit and have an Entity Framework 4 Entity Model with entities, e.g. an `Employee` with `FirstName`, `LastName` and `Ssn`.

To enable model validation in unit tests, I create a partial `Employee` class within the same namespace as the entity and a `MetadataType` class that handles validation for the class.

This is described in more detail [here](http://weblogs.asp.net/scottgu/archive/2010/01/15/asp-net-mvc-2-model-validation.aspx).

My `EmployeeController` has a `Create` function that takes an employee and tries to save it. If the `ModelState` is invalid, the controller returns the Create view and displays the error.

However, if the model state valid, the controller instead creates the employee and returns the employee list.

When I started to write tests for these classes, `ModelState.IsValid` was always true, even if the tests used invalid employees. Turns out that model validation isn't triggered by tests.


## Trigger model validation within a test

To trigger model validation in a test, you can use the `CallWithModelValidation` Controller extension method. Just rewrite this test code:

```csharp
var result = controller.Create(new Employee());
```

to instead look like this:

```csharp
var result = controller.CallWithModelValidation(c => c.Create(new Employee()), new Employee());
```

This makes the unit test properly trigger model validation, which means that the test suite can now test that the controller behaves correctly for invalid models.

The only problem with this approach, is that the model validation doesn't catch errors in the model, even if the model is invalid. 

After some testing, I noticed that this only occurs for partial objects that use `MetadataType` to specify model validation. Turns out that `MetadataType` is ignored by the unit tests.

Before we proceed, make sure to note that classes that describe their validation attributes directly are correctly validated. The next part is only relevant when you use `MetadataType`.


## Register MetadataType connections before testing

[This page](http://stackoverflow.com/questions/2657358/net-4-rtm-metadatatype-attribute-ignored-when-using-validator) discusses the `MetadataType` problem and how `InstallForThisAssembly` can be used to fix this problem.

This method must be placed within the same assembly as the model. I placed it inside a `ControllerExtensions` class file and call it at the beginning of `CallWithModelValidation`.

This will not work if you move the extension to another project, so make sure to have it in the correct project. Run it before your tests, and everything should work.