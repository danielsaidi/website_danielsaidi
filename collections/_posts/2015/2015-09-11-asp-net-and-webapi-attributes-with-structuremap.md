---
title: ASP.NET and WebApi attributes with StructureMap
date:  2015-09-11 09:45:00 +0100
tags:  archive
icon:  dotnet
---

After some time away from .NET, ASP.NET and WebApi, I have a great time setting up a new WebApi solution for a project at work.


## StructureMap

I'm using StructureMap to wire up dependencies. However, the StructureMap website had outdated documentation and no pointers to documentation or tutorials for the new version.

While searching for an updated documentation that cover the new version, I got a link to [this page](http://structuremap.github.io) on Twitter. It was a great help and really helped me out.


## Authorization and authentication

I'm setting up a WebApi solution with two levels of authorization. First of all, all clients must provide a valid client token to be able to perform any requests.

The token is handled by a custom `AuthorizationFilterAttribute` that is added to a class that serves as the base class for all `ApiController` types:

```csharp
public class AuthorizeClientAttribute : AuthorizationFilterAttribute
{
   public override void OnAuthorization(HttpActionContext actionContext)
   {
      ...
   }

   ...
}
```

Second, the user must be properly authenticated for some requests. This authentication is handled by a second `AuthorizationFilterAttribute`.


## Problems with Attributes, StructureMap and dependencies

Both filters have dependencies to components that are resolved with StructureMap. I ran into problems when injecting dependencies into attributes. How should they be resolved?

### Option 1: Custom filter provider

I first tried to create a filter provider that builds up all attribute instances
on app start, but could not get it to work with the authorization attributes.

If I could get this to work, using a custom filter provider is probably the best approach.

### Option 2: Constructor injection

My preferred way of injecting dependencies is with constructor injection. The problem with attributes is that constructor parameters must be specified each time you use the attribute.

Having a default constructor would force attributes to refer to the IoC container to resolve any dependencies, then have a second constructor with parameters, for e.g. unit tests:

```csharp
public class AuthorizeClientAttribute : AuthorizationFilterAttribute
{
   public AuthorizeClientAttribute()
      : this(IoC.Container.InstanceOf<IMyComponent>())
   {
   }

   public AuthorizeClientAttribute(IMyComponent component)
   {
      this.component = component;
   }

   IMyComponent component;
}
```

Don't do this! It makes your attribute dependencies point out and forces the attribute to be aware of an IoC container. This means that you can't make the attribute reusable.

### Option 3: Property injection (on attribute instances)

I tried injecting dependencies via instance properties. This means you can set the property, but the attribute has to resolve the dependencies by calling the IoC container:

```csharp
public class AuthorizeClientAttribute : AuthorizationFilterAttribute
{
   public IMyComponent Component { get; set; }
   
   public override void OnAuthorization(HttpActionContext actionContext)
   {
      var component = Component ?? Component = IoC.GetInstance<IMyComponent>();
   }
   ...
}
```

This is equally bad! Attributes shouldn't know about the project and how dependencies are resolved, only of the action context and any dependencies we may require them to have.

### Option 4: Property injection (static)

I tried injecting my dependencies using static property injection instead.
This allows us to wire up dependencies in a StructureMap registry, as well as in
our unit tests, as such:


```csharp
public class AuthorizeClientAttribute : AuthorizationFilterAttribute
{
   public static IMyComponent Component { get; set; }
   
   ...
}
```

This works and decouples attributes from the IoC container and from knowing about how dependencies are resolved. Still, it makes all requests use the same component instance.

Consider a component that accesses the database or performs a critical operation. If we use a single instance, we could experience thread locks.

A single instance (singleton) should be an intentional choice, not a side effect of attributes.


## Solution

After exploring these options, I decided to specify how I wanted my attributes to work:

* Dependency injection using constructor injection
* Unaware of anything outside the action context, besides injected dependencies
* Property scope should be handled by the IoC, not by the attribute

I realised that there is another way to inject logic, and finally landed on **function injecting**:

```csharp
public class AuthorizeClientAttribute : AuthorizationFilterAttribute
{
   public static Func<MyComponent> GetComponent { get; set; }
   
   public override void OnAuthorization(HttpActionContext actionContext)
   {
      var component = GetComponent();
   }

   ...
}
```

This means that my tests can inject a function that returns a fake or a mock...or whatever:

```csharp
[SetUp]
public void Setup() 
{
   _attribute = new AuthorizeClientAttribute();
   AuthorizeClientAttribute.GetComponent = GetComponent;
}

private IMyComponent GetComponent()
{
   return Substitute.For<IMyComponent>();
}

...
```

StructureMap can then inject a function that resolves dependencies from the IoC container:

```csharp
public class SecurityRegistry : Registry
{
   public SecurityRegistry() 
   {
      AuthorizeClientAttribute.GetComponent = GetComponent;
   }

   private static MyComponent GetComponent()
   {
      return IoC.Container.GetInstance<IMyComponent>();
   }
}
```

Make sure that your attributes crash as early as possible, if these properties are not set.