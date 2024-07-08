---
title: Adding Spark to ASP.NET MVC 2
date:  2011-01-13 12:00:00 +0100
tags:  archive
icon:  dotnet
---

I finally got some time to look at the Spark View Engine. Since the Razor View Engine will be shipped with ASP.NET MVC 3, I decided to give it a try.


## Get Spark up and running

To get started, we must first add Spark to our ASP.NET project. Grab latest release, unzip the zip, and add it to the solution, together with `Spark.dll` and `Spark.Web.Mvc.dll`.

If you use [NuGet](https://www.nuget.org), you can just right-click the references folder and choose "Add package reference...". Search for "Spark", then install `sparkmvc`.

For Spark to work, we must also make it launch together with the application. This is done by adding the following to `Application_Start` in `global.asax.cs`:

```csharp
ViewEngines.Engines.Add(new SparkViewFactory());
```

I also recommend adding the following to web.config, although it is optional:

```xml
<configSections>
	<section name="spark" type="Spark.Configuration.SparkSectionHandler, Spark"/>
</configSections>
<spark>
	<compilation debug="true" />
	<pages automaticEncoding="true" />
</spark>
```

That's it! Spark is now added to your web app and runs alongside the default Web Forms view engine. This means that any views that don't use Spark will still work.


## Converting views to Spark

To convert your views (master pages, user controls & pages) for Spark, you just have to:

- Rename `Site.master` to `Application.spark`.
- Rename all user controls to `_NAME.spark`.
- Rename all pages to NAME.spark.
- In all master page, user control and page files, remove the topmost tag.
- In Application.spark, replace all ContentPlaceHolders tags with use spark tags.
- In all pages, replace all Content tags with contentspark tags.

The name pattern is used to make it possible to add user controls as HTML elements.

You also have to replace `<%` with `${`.

For strongly types views, you can refer to `ViewData` and the `Model` like this:

```xml
<viewdata Message="string" model="SparkTemplate.Models.LogOnModel" />
```

Note that the ViewData key is defined as is, while the model tag is defined with lowercase. 

You can then access the model like this:

```csharp
ViewData.Model
```

You should also use `!` instead of `$` for `ValidationSummary` and `ValidationMessageFor`. If you don't, empty strings will result in the full Spark expression being rendered.

Other than that, everything just seem to work out of the box.