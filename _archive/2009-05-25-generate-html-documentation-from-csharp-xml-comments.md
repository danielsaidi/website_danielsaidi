---
title: Generate HTML documentation from C# comments
date:  2009-05-25 11:01:00 +0100
tags:  archive
---

In .NET and C#, XML comments is a good way of documenting the code. These comments can then be used to generate HTML documentation. Let's have a look at how.

XML comments lets you document your code in a way that makes it possible to generate a complete documentation from it, using many different output formats.

XML comments are also automatically used by Visual Studio to power utils like IntelliSense and code completion.

Another benefit with coupling your documentation with your source code, is that it's always up to date, unlike text files or wikis which can become stale as the code evolves.

This is how you do it.


## Generate an XML file from your XML comments

If you have documented your code, Visual Studio can export documentation to a separate .xml file when you build your project. This is enabled  under `Project/Properties/Build`.

If you enable `.xml file extraction`, Visual Studio will generate an .xml file each time you build your project. This file can then be parsed to generate help files, documentation, etc.

If you want to publish your documentation online, you can generate HTML documentation instead of XML. You can then host the documentation on any website.


## Generate HTML documentation from XML comments

Previous versions of Visual Studio had built-in support for generating HTML documentation from C# code. However, I think Visual Studio 2003 was the last version to have it.

After looking for a .NET version of [phpDoc](http://www.phpdoc.org) and [JavaDoc](http://www.google.se/url?q=http://java.sun.com/j2se/javadoc), I found [Doxygen](http://www.stack.nl/~dimitri/doxygen/). After giving it a try, I found this walkthrough to work well:


## Configure Doxygen

To get started, download, install and start [Doxygen](http://www.stack.nl/~dimitri/doxygen/), then configure it like this:

*Wizard/Project*
* Enter *project name* and *version* - this will be used as page title
* Point out the *source code root folder*, which is where your source code is
* Check *Scan recursively* to make sure that all namespace folders are parsed
* Pick a *documentation destination folder*, where your HTML documentation will end up

*Wizard/Mode*
* Instead of "Documented Entities", select "All Entities".
* "All Entities" will also extract a nice namespace list.
* Since we are extracting documentation for C#, select "Optimize for Java or C# output"

*Wizard/Output*
* Make sure that *HTML* is checked and choose the format you prefer.
* I like the frame/tree format, but this is just a matter of taste :)
* Latex, Man pages, RTF and XML are optional and not described in this post.

*Wizard/Diagrams*
* Select "Use built-in class diagram generator".

*Expert/Project*
* Uncheck "FULL_PATH_NAMES" to avoid displaying file paths in the documentation.

*Expert/Build*
* Check "EXTRACT_STATIC" to include static classes.

*Run*
* Click "Run doxygen" to generate the HTML documentation. 
* When it's done, view the result by clicking "Show HTML output". 

When the HTML documentation is built, you can just upload it to wherever you want it to be available. If you're happy with the outcome, save the config for future use.

Hope this helps!