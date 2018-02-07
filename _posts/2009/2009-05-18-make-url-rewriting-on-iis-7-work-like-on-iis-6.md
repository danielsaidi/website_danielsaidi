---
title:  "Make URL rewriting in IIS 7 work like in IIS 6"
date:   2009-05-18 08:10:00 +0100
tags: 	.net windows
---


I have had a lot of problems with getting URL rewriting to work in Windows Vista,
running IIS 7. Compared to IIS 6, virtual paths in IIS 7 will not allow extensions
like .js, .css, which is really problematic if you are working on systems that use
shared files in virtual paths.

This problem is discussed in [this excellent article](http://www.improve.dk/blog/2006/12/11/making-url-rewriting-on-iis7-work-like-iis6).

The article may seem long, but presents some simple fixes that just takes a minute
or so:

* Check out web.config (non-exclusive) if you have it under version control, then open IIS.
* Select the correct application pool (create one if required) and switch from Managed Pipeline Mode to Classic under Basic Settings.
* Select the correct web site and open the “HandlerMappings” section.
* Open the StaticFile handler. Change its Request Path to *.* and its mapping from "File or Folder" to "File".
* Click "Add Script Path" and add an `aspnet_isapi` handler. Change its Request Path to * and give it a name of your choice.
* Finally, click "View Ordered List" and move the `ISAPI handler` to the very bottom.

After this, your web application will, hopefully, behave correctly.

Make sure that you have a dedicated application pool for the web application when
you perform these, otherwise the changes will affect the default application pool,
which is not recommended.

Note that you should not check in the changes that are made to web.config, since
these changes will add IIS 7 specific parameters into it. If the web.config changes,
simply reset the changes and repeat the commands above.