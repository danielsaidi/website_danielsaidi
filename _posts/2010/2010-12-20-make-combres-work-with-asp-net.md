---
title:	"Make Combres work with ASP.NET"
date:	2010-12-21 12:00:00 +0100
categories: dotnet
tags: 	asp-net combres
---


After having problems with [SquishIt](http://www.codethinked.com/post/2010/05/26/SquishIt-The-Friendly-ASPNET-JavaScript-and-CSS-Squisher.aspx) and JavaScript, I
decided to give [Combres](http://www.codeproject.com/KB/aspnet/combres2.aspx/) a
try instead.

However, while SquishIt works right out of the box, Combres must be configured a
little. [This page](http://www.codeproject.com/KB/aspnet/combres2.aspx) describes
how you configure it for your project.

In short, you need to:

- Create a custom Combres configuration file
- Add the following to web.config:
	- A Combres configSection tag
	- A Combres section tag (which you point to the Combres config file)
	- Two httpModule tags
- Add `RouteTable.Routes.AddCombresRoute(“Combres Route”);` to `Application_Start` in `Global.asax`
- Add `<%= WebExtensions.CombresLink(“…”) %>` wherever you want to add JS or CSS tags

With SquishIt, you only need to add a bundle tag to your page, which is easier.
However, Combres is said to perform better and handles JavaScript closures with
no problems at all.

With Combres up and running, I noticed that it did not work with IIS7 Integrated
Pipeline mode. If you run into this, remove all content in the Combres `section`
tag, so that it looks like this:

	<section name=”combres” type=”Combres.ConfigSectionSetting” />

Then, instead of:

	<httpModules>
	        <add name="ScriptModule" type="System.Web.Handlers.ScriptModule,
		System.Web.Extensions, Version=3.5.0.0, Culture=neutral,
		PublicKeyToken=31BF3856AD364E35"/>
	        <add name="UrlRoutingModule" type="System.Web.Routing.UrlRoutingModule,
		System.Web.Routing, Version=3.5.0.0, Culture=neutral,
		PublicKeyToken=31BF3856AD364E35"/>
	</httpModules>

you also have to add the modules to the `system.webServer/modules` tag, since it
is the one used by IIS7 (keep the ones above as well, though):

	<modules>
	        <add name="ScriptModule" type="System.Web.Handlers.ScriptModule,
		System.Web.Extensions, Version=3.5.0.0, Culture=neutral,
		PublicKeyToken=31BF3856AD364E35"/>
	        <add name="UrlRoutingModule" type="System.Web.Routing.UrlRoutingModule,
		System.Web.Routing, Version=3.5.0.0, Culture=neutral,
		PublicKeyToken=31BF3856AD364E35"/>
	</modules>
	
If you follow these steps, Combres should work.

