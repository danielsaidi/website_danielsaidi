---
title:  "Windows Vista, Visual Studio 2008 and IIS7"
date:   2009-01-28 15:12:00 +0100
tags: 	.net windows
---

After upgrading to Windows Vista, I have had many problems to run Visual Studio
2008 and IIS 7 on it. For instance, IIS URL Rewriting does not work as it did earlier.

Windows Vista seems to focus more on security than on developer functionality.
This makes sense, but means that you have to install a lot of additional software
to make IIS 7 and Visual Studio 2008 behave the way you want them to.

You may want to confirm that you have the following installed:

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

If you have found yourself having the same problems as I have, I really hope that
installing and configuring the stuff above helps you out.