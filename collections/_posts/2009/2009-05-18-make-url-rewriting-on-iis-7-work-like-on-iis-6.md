---
title: Make URL rewriting in IIS 7 work like in IIS 6
date:  2009-05-18 08:10:00 +0100
tags:  archive
icon:  dotnet

article: http://www.improve.dk/blog/2006/12/11/making-url-rewriting-on-iis7-work-like-iis6
---

I've had a lot of problems with getting URL rewriting to work with Windows Vista and IIS 7. Compared to IIS 6, virtual paths in IIS 7 don't allow extensions like .js, .css, by default. 

This is problematic if you have shared files in virtual paths, which is discussed [here]({{page.article}}). It may seem long, but has fixes that only takes a minute or so to get in place:

* Check out `web.config` (non-exclusive) if you have it under version control.
* Open IIS and select the correct application pool (or create one if needed).
* Switch from Managed Pipeline Mode to Classic under Basic Settings.
* Select the correct web site and open the "HandlerMappings" section.
* Open the StaticFile handler. 
* Change its Request Path to *.* and its mapping from "File or Folder" to "File".
* Click "Add Script Path" and add an `aspnet_isapi` handler. 
* Change its Request Path to * and give it a name of your choice.
* Finally, click "View Ordered List" and move the `ISAPI handler` to the very bottom.

After this, your web application will, hopefully, behave correctly.

Make sure to have a dedicated application pool for the web app when you perform these changes, otherwise they will affect the default application pool, which is not recommended.