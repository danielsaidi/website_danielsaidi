---
title:	"Adding Spark to ASP.NET MVC 2"
date:	2011-01-13 12:00:00 +0100
tags: 	.net c#
---


After much curiosity, with other stuff stealing my time, I finally got some time
to look at the Spark View Engine. Since Razor will be shipped with ASP.NET MVC 3,
I decided to give Spark a try before trying Razor.

[The Spark web site](http://sparkviewengine.com/) has some good info about Spark,
what it does, what problems it solves etc. Have a look, since I will not go into
details on Spark in this post. Also, [this blog](http://nyveldt.com/blog/post/Exploring-Spark-View-Engine-for-ASPNET-MVC-e28093-Part-1.aspx) helped me a lot.


## Get Spark up and running

To get started, we first have to add Spark to our ASP.NET application. Visit the
[Spark web site](http://sparkviewengine.com/), and download the latest release.
Unzip the release bundle and add it to the solution, together with references to
`Spark.dll` and `Spark.Web.Mvc.dll`.

If you use [NuGet](http://nuget.codeplex.com/), you just have to right-click the
references folder and choose *Add package reference...*, search for "Spark" then
install `sparkmvc`. Yeah, NuGet is really convenient!

For Spark to work, we also have to make it launch together with the application.
Do this by adding the following to `Application_Start` in `global.asax.cs`:

	ViewEngines.Engines.Add(new SparkViewFactory());

Finally, I recommend adding the following to web.config, although it is optional:

	<configSections>
	  <section name="spark" type="Spark.Configuration.SparkSectionHandler, Spark"/>
	</configSections>
	<spark>
	  <compilation debug="true" />
	  <pages automaticEncoding="true" />
	</spark>

That's it! Spark is now added to your web app and runs alongside the default Web
Forms view engine. This means that any views not using Spark will still work.


## Converting views to Spark

All you have to do to convert your views (master pages, user controls and pages)
to Spark is to:

- Rename `Site.master` to `Application.spark`
- Rename all user controls to `_NAME.spark`
- Rename all pages to NAME.spark
- In all master page, user control and page files, remove the topmost tag
- In Application.spark, replace all ContentPlaceHolders tags with use spark tags
- In all pages, replace all Content tags with contentspark tags

The user control underline name pattern is used to make it possible to add user
controls as HTML elements.

You also have to replace `<%` with `${`.

For strongly types views, you can refer to `ViewData` and the `Model` like this:

	<viewdata Message="string" model="SparkTemplate.Models.LogOnModel" />

Note that the ViewData key is defined as is, while the model tag is defined with
lowercase. You can then access the model like this:

	ViewData.Model

Other than that, everything just seem to work out of the box.

Worth noticing is that you should use `!` instead of `$` for `ValidationSummary`
and `ValidationMessageFor`. Otherwise, any empty strings will result in the full
Spark expression being rendered.