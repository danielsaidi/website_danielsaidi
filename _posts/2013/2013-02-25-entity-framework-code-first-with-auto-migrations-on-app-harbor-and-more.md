---
title:  "Entity Framework Code First with auto migrations on AppHarbor...and more"
date: 	2013-02-25 10:55:00 +0100
categories: dotnet
tags: 	asp-net entity-framework appharbor api rest
---


I am working on an iOS app that is powered by an ASP.NET MVC 4 admin system that
uses Entity Framework Code First with auto migrations RUNNING on App Harbor. The
project also involves a web site for presentation and information.

This is how we put it all together.


## Setting up the coding environment(s)

The web site will be hosted on a private server, without any git integrations in
place. As such, we chose to place our git repository at BeansTalk, which enables
us to git-push to our repository, then deploy UPDATES to the server with FTP.

The admin system will be hosted on AppHarbor, which has a really nice git deploy
integration in place. As such, we chose to place our git repository at BitBucket
and enable service hooks for automatic deployment.

The iOS app(s) will be deployed from Xcode. Since I am the single iOS-developer,
I have chosen to start with a personal git repository on Dropbox and go cloud if
I have to share the code with others.

As you can see, we have rather different setup for each project, although git is
the only source control system that we use.



## Creating and deploying the web site

For the web site, we created a simple web that we version control with git. When
we want to publish any changes, we git push to BeansTalk, which triggers a build
and (if the build succeeds) deploy.

Since we want people to be able to signup to be notified once we go live, we did
add a MailChimp-based signup form to the page. When a user signs up with the form,
he/she is taken to a skinned MailChimp page, where the MC magic happens.



## Creating the admin system

When I started building the admin system, I wanted to give Windows Azure Mobile
Services a try. However, since we want to access the data in the app as well as
on the site, and Azure MS did not yet support JavaScript-integration, I decided
to use technoology I was already familiar with.

So, I chose to setup an ASP.NET MVC 4 web application with Entity Framework Code
First. Below, I will go through the steps involved to get it to run on AppHarbor.


### Create the solution

If our project is called X, our solution is made up of the following projects:

* **X.Domain** – Contains our model, persistance tools, repositories, validation etc.
* **X.Domain.Tests** – NUnit/NSubstitute test suite for X.Domain
* **X.Language** – Resource files, translation classes, metadata providers etc.
* **X.Language.Tests** – NUnit/NSubstitute test suite for X.Language
* **X.Web** – The ASP.NET MVC 4 web site
* **X.Web.Utilities** – Web-related functions with no coupling to the web site or the domain
* **X.Web.Utilities.Tests** – NUnit/NSubstitute test suite for X.Web.Utilities


### Setup Entity Framework Code First with auto migrations

This is a rather long section, but I think it is important to describe how to do
thia. This was the first time I used Entity Framework Code First, and while it's
straightforward, I ran into some problems when publishing to AppHarbor.

I first created our entity classes, which inherit from an EntityBase base class,
that has base logic and a Guid ID property. After that, we setup the Code First
context and other required classes.

I then defined a Context class, that connects the data entities to the database.
It looks like this:


{% highlight csharp %}
public class DataContext : DbContext
{
   //Can be set if it needs to be modified
   public static string ConnectionStringName = "DefaultConnection";

   public DataContext()
      : base(ConnectionStringName)
   {
   }

   //Setup all your entity sets here
   public DbSet<X> Xs { get; set; }

   //This is required for auto migrations
   protected override void OnModelCreating(DbModelBuilder modelBuilder)
   {
      Database.SetInitializer(new MigrateDatabaseToLatestVersion<DataContext, Configuration>());
      base.OnModelCreating(modelBuilder);
   }
}
{% endhighlight %}


The `ConnectionStringName` property is very important! I first used the default
constructor of the `DataContext` class, but this made it class use a SQL Express
database table that was named after the class' **full** class name. Do not fall
into this trap, since it will make pushing to AppHarbor a nightmare.

I chose to make the `ConnectionStringName` static instead of using a constructor
parameter, since constructor parameters require an IDbContextFactory<DataContext>
implementation that instantiates the context when migrations kick in. I find the
approach above to be simpler.

With the Context in place, we have to connect it to a `Configuration` to enable
auto migrations, like this:


{% highlight csharp %}
internal sealed class Configuration : DbMigrationsConfiguration&lt;DataContext&gt;
{
   public Configuration()
   {
      AutomaticMigrationsEnabled = true;
   }
}
{% endhighlight %}


This is all you have to do to get Entity Framework Code First auto migrations to
work. However, being a repository-lover, I wrap the Context in some repositories
that implement certain interfaces that better describe what they are supposed to
be used to achieve, and so that they can be replaced with other implementations.

### Localization

Even if the site is small, I always use resource files to separate text from the
UI. I will not go into detail on how I set this up, but I mostly do it this way:

* I first create a resource file in the X.Language project
* I then setup a translator from the X.Language project
* In global.asax, I replace the default metadata provider with [this one](https://github.com/danielsaidi/nextra/blob/master/NExtra.Mvc/Localization/LocalizedDataAnnotationsModelMetadataProvider.cs).
* I finally add an extension that lets me type Html.Translate("keyname") in my views

I will probably take these classes I use and make a nice open source lib one day,
but for now I hope that this will point you in the right direction.


### Bootstrap the web site

I use StructureMap to wire up all dependencies in the system, which means that I
can configure the entire system from one single place. I then use AutoMapper for
mapping between domain and view models.


### Configuration transformations

Since web.config configuration file will have to be modified as the admin system
is pushed to AppHarbor, I use the standard Web.config rewrite approach to ensure
that the Web.config file will be correct once the system is deployed.

For now, I only have the following rewrite in Web.Release.config:

{% highlight html %}
<connectionStrings>
   <add xdt:Locator="Condition([@name='DefaultConnection'])" providerName="System.Data.SqlClient" xdt:Transform="SetAttributes" />
</connectionStrings>
{% endhighlight %}


This may look strange, but will replace the DefaultConnection connection string
in Web.config (make sure that you have one) with an AppHarbor DB connection that
is called  DefaultConnection (make sure that you set its alias).

After this, everything should work. The default database connection will be used
by both the membership provider (if you have one) and the database data context,
which means that everything will end up in the same database. If you want to use
different databases, well...I expect you know how to do it.


## Exposing data from the Admin system

To expose data from the AppHarbor-hosted admin system, we expose webservices via
a JSON-based ReST API. It's not as neat as Azure Mobile Services, but works .



## Building the iOS apps

This is a work in progress, since I have not started yet, but I can at least say
that I will be using `LRResty` to communicate with the API. It's lightweight and
really easy to use, so I advice you to check it out.

**Update 2016:** Today, I would rather advice you to use Alamofire for API calls
instead of LRResty. Together with AlamofireObjectMapper and Realm, it's amazing.



## Conclusion

I have not covered all the things involved in this latest project, but some nice
takeaways (so far) is that git lets me work in quite different ways in different
projects. Git pushing to deply may not be the best alternative for big, complex
systems, but for smaller hobby projects, it's really convenient.

Entity Framework Code First is very convenient, once you have auto migrations in
place and learned about the default connection that is used by DbContext if none
is set.



