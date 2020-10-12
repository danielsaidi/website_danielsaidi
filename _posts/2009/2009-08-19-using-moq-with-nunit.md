---
title: Using Moq with NUnit
date:  2009-08-19 13:28:00 +0100
tags:  .net testing mocking
---

As I continue to work on the NerdDinner tutorial, I have discovered that some of
my development patterns has to change. Surprise? One thing that has to change is
how I write tests with NUnit, when testing my repositories.

I use to create test classes that have private objects that are initialized in a
`TextFixtureSetUp` method. If we consider that I am to test the DinnerController,
using a mock repository, the approach would be as such:

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

Since the two test cases use the same object instances, their verifications will
not work as expected. The repository's Get function will be called twice, since
both test cases call it.

The solution is (thanks, Micke!) to replace `[TextFixtureSetUp]` with `[SetUp]`.
The first is called once, prior to all tests, which the second is called before
each test case.