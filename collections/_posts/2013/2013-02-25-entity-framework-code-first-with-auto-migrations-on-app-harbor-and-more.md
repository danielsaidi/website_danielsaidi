---
title: Entity Framework Code First with auto migrations on AppHarbor...and more
date:  2013-02-25 10:55:00 +0100
tags:  archive
icon:  dotnet
---

I'm building an iOS app that has an ASP.NET MVC 4 backend that uses Entity Framework Code First with auto migrations and runs on App Harbor. We also have a site that is used to present more information. This is how we put it all together.


## Setting up the coding environment(s)

The web site is hosted on a private server, without any git integrations. As such, we chose to place our git repository at BeansTalk, which enables us to git push to our repository and have it deploy updates to the server using FTP.

The admin system is hosted on AppHarbor, which has a really nice git deploy integration in place. We chose to place our git repo at BitBucket and have service hooks for auto-deploy.

The iOS app will be deployed from Xcode. Since I'm the only iOS-developer, I started with a personal git repository on Dropbox and will go cloud when releasing the app.


## Creating and deploying the web site

For the web site, we created a simple web that we version control with git. When we want to publish any changes, we git push to BeansTalk, which triggers a build then a deploy.

Since we want people to be notified once we go live, we added a MailChimp signup form to the site. When users sign up, they're taken to a MailChimp page, where MC stuff happens.


## Creating the admin system

I wanted to build the admin system with Windows Azure Mobile Services. However, since we want to access the data in the app as well as on the site, and Azure MS did not yet support JavaScript-integration, I decided to use technoology I was already familiar with.

So, I chose an ASP.NET MVC 4 with Entity Framework Code First. Below, I will go through the steps involved to get it to run on AppHarbor.


## Creating the solution

If our project is called X, our solution is made up of the following projects:

* **X.Domain** – Contains our model, persistance tools, repositories, validation etc.
* **X.Domain.Tests** – NUnit/NSubstitute test suite for X.Domain
* **X.Language** – Resource files, translation classes, metadata providers etc.
* **X.Language.Tests** – NUnit/NSubstitute test suite for X.Language
* **X.Web** – The ASP.NET MVC 4 web site
* **X.Web.Utilities** – Web-related functions with no coupling to the web site or the domain
* **X.Web.Utilities.Tests** – NUnit/NSubstitute test suite for X.Web.Utilities


## Setting up Entity Framework Code First

This was the first time I used Entity Framework Code First, and while it's straightforward, I ran into some problems when publishing to AppHarbor.

I first created entity classes that inherit from an `EntityBase` class, that has base logic and a Guid `ID` property. After that, we setup the Code First context and other required classes.

I then defined a `Context` class that connects data entities to the database:

```csharp
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
```

The `ConnectionStringName` property is important! I first used the default constructor of the `DataContext` class, but this made it use a SQL Express database table named after the full class name. Don't fall into this trap, since it will make pushing to AppHarbor a nightmare.

I chose to make the `ConnectionStringName` static instead of using a constructor parameter, since constructor params require an `IDbContextFactory` implementation that instantiates the context when migrations kick in. I find the approach above to be simpler.

With the context in place, I connected it to a `Configuration` to enable auto migrations:

```csharp
internal sealed class Configuration : DbMigrationsConfiguration&lt;DataContext&gt;
{
   public Configuration()
   {
      AutomaticMigrationsEnabled = true;
   }
}
```

This is all you have to do to get Entity Framework Code First to work. I wrap the context in some repositories that implement certain interfaces that better describe what they are supposed to be used to achieve, so they can be replaced with other implementations.


## Localization

I always use resource files to separate text from the code. I will not go into detail on how I set this up, but I mostly do it this way:

* I first create a resource file in the X.Language project.
* I then setup a translator from the X.Language project.
* In global.asax, I replace the default metadata provider with [this one](https://github.com/danielsaidi/nextra/blob/master/NExtra.Mvc/Localization/LocalizedDataAnnotationsModelMetadataProvider.cs).
* I finally add an extension that lets me type Html.Translate("keyname") in my views.

I will probably take these classes I use and make a nice open source lib one day, but for now I hope that this will point you in the right direction.


## Bootstrapping the web site

I use `StructureMap` to manage dependencies, which means that I can configure it from one single place. I then use `AutoMapper` to map between domain and view models.


## Configuration transformations

Since `Web.config` must be modified when the admin system is pushed to AppHarbor, I use a rewrite approach to ensure that the file will be correct once the system is deployed.

For now, I only have the following rewrite in Web.Release.config:

```html
<connectionStrings>
   <add xdt:Locator="Condition([@name='DefaultConnection'])" providerName="System.Data.SqlClient" xdt:Transform="SetAttributes" />
</connectionStrings>
```

This may look strange, but it replaces a `DefaultConnection` in `Web.config`, if any, with an AppHarbor DB connection.

With this, things should work. The default database connection is used by the membership provider (if you have one) and the database data context, which means that everything will end up in the same database. If you want to use different databases, you can.


## Exposing data from the Admin system

To expose data from the AppHarbor-hosted admin system, we expose webservices via a JSON-based ReST API. It's not as neat as Azure Mobile Services, but works .


## Building the iOS apps

This is still a work in progress, but we will use `LRResty` to communicate with the  API. It's lightweight and easy to use, so I advice you to check it out.

**Update 2016:** Today, I would rather advice you to use Alamofire for API calls instead of LRResty. Together with AlamofireObjectMapper and Realm, it's amazing.

**Update 2022:** Today, I would rather advice you to use URLSession :)


## Conclusion

I haven't covered everything, but listed some nice takeaways. git lets me work in different ways in different projects. Git push to deploy may not be the best option for big, complex systems, but for smaller  hobby projects, it's really convenient.

Entity Framework Code First is very convenient, once you have auto migrations in place and learned about the default connection that is used by DbContext if none is set.



