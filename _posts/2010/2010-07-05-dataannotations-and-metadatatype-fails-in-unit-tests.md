---
title:	"DataAnnotations and MetadataType fails in unit tests"
date:	2010-07-05 12:00:00 +0100
tags: 	.net c# entity-framework unit-testing
---


This post describes how to solve the problem that model validation will not work
for ASP.NET MVC 2 (.NET 4.0), when testing a model that uses DataAnnotations and
MetadataType to describe for its validation.

First of all, `ModelState.IsValid` is always true, since the function that sets
it to false for invalid models is never executed during the unit test execution.
This will cause your controllers to behave incorrectly during your tests.

Second, any `MetadataType` bindings are ignored during the test execution. This
causes the validation within it to be ignored as well, which in turn will cause
the model to be valid although an object is invalid.


## My situation

I am currently writing tests for a Create method in one of my controllers. I use
NUnit as test framework. I have an Entity Framework 4 Entity Model, in which
I have a set of entities, e.g. an `Employee` entity with `FirstName`, `LastName`
and `Ssn` properties.

To enable model validation during a unit test run, I create a partial `Employee`
class in the same namespace as the entity. I then create a `MetadataType` class,
which handles validation for the class.

This approach is fully described in [this blog post](http://weblogs.asp.net/scottgu/archive/2010/01/15/asp-net-mvc-2-model-validation.aspx).

In my `EmployeeController` class, I then have a `Create` function, that takes an
employee and tries to save it. If `ModelState` is not valid, the controller will
return the Create view and display any errors to the user. If the model is valid,
however, I create the employee and return the employee list. 

Easy enough, right? However, when I started to write tests for these classes, it
turned out that `ModelState.IsValid` was always true, even if the tests received
invalid employees. Turns out that model validation is not triggered by the tests.


## Trigger model validation within a test

[This blog post](http://blog.overridethis.com/blog/post/2010/04/22/MVC2-Model-Validation-and-Testing-Scenarios.aspx)
describes the model state problem then presents a nice solution to this problem:
the `CallWithModelValidation` Controller extension method.

I added this extension method to my MVC 2 project and used it instead of calling
`Create`. The test code that looked like this:

	var result = controller.Create(new Employee());

thus became:
	
	var result = controller.CallWithModelValidation(c => c.Create(new Employee()), new Employee());


This makes my unit tests properly trigger model validation, which means that the
test suite can now test that the controller behaves correctly for invalid models.

The only problem with this approach, is that the model validation does not catch
any errors within the model, even if the model is invalid. After some testing, I
noticed that this does only occur for partial objects that use `MetadataType` to
specify model validation. Turns out that `MetadataType` is ignored within a test
context. Thus, the model is always considered to be valid.

Before we proceed, make sure to note that classes that describe their validation
attributes directly are correctly validated. The next part is only relevant when
you use `MetadataType`.


## Register MetadataType connections before testing

[This page](http://stackoverflow.com/questions/2657358/net-4-rtm-metadatatype-attribute-ignored-when-using-validator)
discusses the `MetadataType` problem and `InstallForThisAssembly` as a solution.

This method must be placed within the same assembly as the model, in other words
not the test project. I place it in a `ControllerExtensions` class file and call
it at the beginning of `CallWithModelValidation`. This will not work if you move
the extension to another project, so make sure to have it in the correct project.

Run it before your tests, and everything should work.

Hope this helps.

