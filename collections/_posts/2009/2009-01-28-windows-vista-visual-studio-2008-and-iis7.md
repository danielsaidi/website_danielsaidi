---
title:  Windows Vista, Visual Studio 2008 and IIS7
date:   2009-01-28 15:12:00 +0100
tags: 	.net
icon:   dotnet
---

After upgrading to Windows Vista, I've had many problems with running Visual Studio
2008 and IIS 7 on it. This blog post discusses some problems and how to solve them.

Vista seems to focus more on security than developer functionality. This means that
you may have to install a lot of additional software to make IIS 7 and Visual Studio
2008 behave the way you expect.

For instance, IIS URL Rewriting now requires that you have the following installed:

* Visual Studio 2008 SP1 (a big recommendation if you use VS 2008)
* Web Management Tools (activated in the Control Panel)
* IIS Management Compatibility (activated in the Control Panel)
* IIS Metabase and IIS 6 configuration compatibility (activated in the Control Panel)
* IIS Management Console
* IIS Management Script and Tools
* IIS Management Service
* World Wide Web Services
* Application Development Features
* .NET Extensibility
* ASP.NET
* ISAPI Extensions
* ISAPI Filters
* Security
* Basic Authentication
* Windows Authentication

If you have the same problems as I have had with URL rewriting, I hope that installing
and configuring the tools above helps you out.