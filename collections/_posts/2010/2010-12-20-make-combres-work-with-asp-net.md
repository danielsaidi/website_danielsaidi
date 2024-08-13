---
title: Make Combres work with ASP.NET
date:  2010-12-20 12:00:00 +0100
tags:  archive

combres:	http://www.codeproject.com/KB/aspnet/combres2.aspx/
squishit: 	http://www.codethinked.com/post/2010/05/26/SquishIt-The-Friendly-ASPNET-JavaScript-and-CSS-Squisher.aspx
post:		http://www.codeproject.com/KB/aspnet/combres2.aspx
---

After having problems with [SquishIt]({{page.squishit}}) when bundling JavaScript, I decided to give [Combres]({{page.combres}}) a try. However, while SquishIt works right away, Combres must be configured quite a bit.

[This page]({{page.post}}) describes how to configure Combres for your project. 

In short, you need to:

- Create a custom Combres configuration file.
- Add the following to `Web.config`:
  - A Combres `configSection` tag.
  - A Combres `section` tag (which you point to the Combres config file).
  - Two `httpModule` tags.
- Add the following to `Global.asax`:
  -  `RouteTable.Routes.AddCombresRoute(“Combres Route”);` in `Application_Start`.
- Add `<%= WebExtensions.CombresLink(“…”) %>` where add JS or CSS tags.

With SquishIt, you only need to add a bundle tag to your page, which is easier. However, Combres is said to perform better and handles JavaScript closures better.

With Combres in place, I noticed that it didn't work with IIS7 Integrated Pipeline mode. 

If you run into this, remove all content in the Combres `section` tag, so that it looks like this:

```xml
<section name=”combres” type=”Combres.ConfigSectionSetting” />
```

Then, instead of:

```xml
<httpModules>
  <add name="ScriptModule" type="System.Web.Handlers.ScriptModule,
    System.Web.Extensions, Version=3.5.0.0, Culture=neutral,
    PublicKeyToken=31BF3856AD364E35" />
  <add name="UrlRoutingModule" type="System.Web.Routing.UrlRoutingModule,
    System.Web.Routing, Version=3.5.0.0, Culture=neutral,
    PublicKeyToken=31BF3856AD364E35" />
</httpModules>
```

you must add the modules to the `system.webServer/modules` tag, since it's the one that is used by IIS7 (keep the tags above as well, though):

```xml
<modules>
  <add name="ScriptModule" type="System.Web.Handlers.ScriptModule,
    System.Web.Extensions, Version=3.5.0.0, Culture=neutral,
    PublicKeyToken=31BF3856AD364E35"/>
  <add name="UrlRoutingModule" type="System.Web.Routing.UrlRoutingModule,
    System.Web.Routing, Version=3.5.0.0, Culture=neutral,
    PublicKeyToken=31BF3856AD364E35"/>
</modules>
```

If you follow these steps, Combres should work.