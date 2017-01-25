---
title:  "ASP.NET and WebApi attributes with StructureMap"
date: 	2015-09-11 09:45:00 +0100
categories: dotnet
tags: 	asp-net webapi dependency-injection structuremap
---


After some time away from .NET, ASP.NET and WebApi, I'm having a great time when
setting up a new WebApi solution for a project at work.



## StructureMap

In this project, I am using StructureMap to wire up all dependencies (Dependency
Injection). However, at the time, the StructureMap site was a mess, with outdated
documentation and no pointers to documentation or tutorials for the new version.

While searching for an updated documentation that covered the new version, I was
sent a link to [this great post](http://structuremap.github.io) on Twitter. This
site was a great help and really helped me out.



## Authorization and authentication

I am setting up a WebApi solution with two levels of authorization. First of all,
all clients must provide a valid client token to be able to perform any requests.

This token is handled by a custom AuthorizationFilterAttribute, which is added to
a class that serves as the base class for all ApiControllers:


{% highlight c# %}
public class AuthorizeClientAttribute : AuthorizationFilterAttribute
{
   public override void OnAuthorization(HttpActionContext actionContext)
   {
      ...
   }

   ...
}
{% endhighlight%}


Second, the user must be properly authenticated, but only for some requests. The
authentication is handled by a second `AuthorizationFilterAttribute`.



## Problems with Attributes, StructureMap and dependencies

Both filters have dependencies to components that are wired up and resolved with
StructureMap. However, I ran into some problems when injecting dependencies into
the attributes. How should the dependencies be resolved?


### Option 1: Custom filter provider

I first tried to create a filter provider that builds up all attribute instances
on app start, but could not get it to work with the authorization attributes. If
this works, it's probably the best approach: Feel free to share if you have made
this work.


### Option 2: Constructor injection

My preferred way of injecting dependencies is constructor injection. The problem
with this approach and attributes, however, is that constructor parameters must
be specified each time you use the attribute. This makes this approach useless.

Having a default constructor would mean that your attribute would have to refer
to the IoC container to resolve any dependencies, then have a second constructor
with parameters, that you can use for unit tests, like this:


{% highlight c# %}
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
{% endhighlight%}


Do not walk down this path! It makes your attribute's dependencies point out and
forces it to become aware of the fact that an IoC container exists. This in turn
means that you will never be able to make the attribute general and reusable.


### Option 3: Property injection (on attribute instances)

I then tried injecting dependencies with instance property injection. This means
that your unit tests can set the property, but that the attribute has to resolve
the dependencies by calling the IoC container in other cases:


{% highlight c# %}
public class AuthorizeClientAttribute : AuthorizationFilterAttribute
{
   public IMyComponent Component { get; set; }
   
   public override void OnAuthorization(HttpActionContext actionContext)
   {
      var component = Component ?? Component = IoC.GetInstance<IMyComponent>();
   }
   ...
}
{% endhighlight%}


This is equally bad! Remember, the attributes should know about how a project is
setup and how dependencies are resolved. They should only be aware of the action
context and any dependencies we may require them to have.


### Option 4: Property injection (static)

I then tried injecting my dependencies using static property injection instead.
This allows us to wire up dependencies in a StructureMap registry, as well as in
our unit tests, as such:


{% highlight c# %}
public class AuthorizeClientAttribute : AuthorizationFilterAttribute
{
   public static IMyComponent Component { get; set; }
   
   ...
}
{% endhighlight%}


This approach works and will decouple attributes from the IoC container and from
knowing about how dependencies are resolved. Still, this is a terrible approach,
since it makes all requests use the same component instance, which is really bad.

Consider a component that accesses the database or performs a critical operation.
If we want a single instance, we should get a single instance only because that
is how it is setup in StructureMap - not as a side effect of using attributes.


## So, how did I solve it?

I decided to specify how I wanted my attributes to work:

* Dependency injection using constructor injection
* Unaware of anything outside the action context, besides injected dependencies
* Property scope should be handled by the IoC, not by the attribute

I then realised that there is another way to inject logic, and finally landed on
**function injecting**:


{% highlight c# %}
public class AuthorizeClientAttribute : AuthorizationFilterAttribute
{
   public static Func<MyComponent> GetComponent { get; set; }
   
   public override void OnAuthorization(HttpActionContext actionContext)
   {
      var component = GetComponent();
   }

   ...
}
{% endhighlight%}


This means that my unit tests can inject a function that returns a fake or a mock
of the interface...or whatever I want:


{% highlight c# %}
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
{% endhighlight%}


Meanwhile, the web api StructureMap registry can inject a function that resolves
any dependencies with the IoC container:


{% highlight c# %}
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
{% endhighlight%}


To protect yourself from an incorrect setup, make sure that your attributes crash
as early as possible, if these function properties are not set.

Hope this approach helps you out as well.

