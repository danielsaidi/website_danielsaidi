---
title: Using Moq with NUnit
date:  2009-08-19 13:28:00 +0100
tags:  testing
icon:  dotnet
---

As I continue to work with unit tests, I have noticed that some of my development patterns have started to change...for the better.

In the NerdDinner tutorial, I create test classes with private members that are initialized in a `TextFixtureSetUp` method. 

If we consider that I am to test a `DinnerController` with a mock repository, I thus do this:

```csharp
private DinnerController controller;
private Mock<IDinnerRepository> repository;

[TestFixtureSetUp]
public void Init()
{
    repository = MockClasses.GetMockDinnerRepository();
    controller = new DinnerController(repository.Object);
}
```

However, consider the following tests:

```csharp
[Test]
public void Details_ShouldDisplayDetailsForValidDinner()
{
    ViewResult result = (ViewResult)controller.Details(1);
    Dinner data = (Dinner)result.ViewData.Model;

    Assert.That(result.ViewName, Is.EqualTo("Details"));
    Assert.That(data.IsValid, Is.EqualTo(true));

    repository.Verify(x => x.Get(1), Times.Once());
}

[Test]
public void Edit_ShouldDisplayEditForValidDinner()
{
    ViewResult result = (ViewResult)controller.Edit(1);
    Dinner data = (Dinner)result.ViewData.Model;

    Assert.That(result.ViewName, Is.EqualTo("Edit"));
    Assert.That(data.IsValid, Is.EqualTo(true));

    repository.Verify(x => x.Get(1), Times.Once());
}
```

Since the two test cases use the same object instances, their verifications will not work as expected. The repository's `Get` function will be called twice, since both test cases call it.

The solution is to replace `[TextFixtureSetUp]` with `[SetUp]`. The former is called once, prior to all tests, which the latter is called before each test case.