---
title:  "Build HTML documentation from C# XML comments"
date:   2009-05-25 11:01:00 +0100
categories: dotnet
tags: 	csharp documentation html doxygen
---


When developing .NET applications, XML comments is a good way of documenting the
code (looking back in 2015, my advice would be to not state the obvious, though).

If you are not familiar with documenting your C# code with XML comments, have a
look at [this page](http://en.wikipedia.org/wiki/C_Sharp_(programming_language)#XML_documentation_system).

XML comments is just one way of documenting your work. Use these kind of comments
for documenting classes and functions that you want to be made publicly available,
and more expressive (since you can describe properties, attributes etc. as well).
XML comments will be parsed by Visual Studio as well, and be used to provide you
with IntelliSense for your own code.

Other ways of documenting your code, is to use plain text files (classic readmes),
wikis etc. as well as common inline comments:

{% highlight csharp %}
// This is an inline comment that can be placed anywhere within your code
{% endhighlight %}

However, when you want to be able to automatically parse documentation from your
.NET source code, XML comments is the way to, and this is how you do it:



## Generate an XML file from XML comments

If you have documented your code with XML-comments, you can tell Visual Studio to
export the documentation to a separate .xml file each time you build your project.

This is easily done under `Project/Properties/Build`.

If you enable .xml file extraction, Visual Studio will generate an .xml file each
time you build your project. This file can then be parsed by various software, to
generate documentation of various types, e.g. help files, HTML documentation etc.

However, if you want to expose the documentation for a wide audience, e.g. if you
want to publish the api documentation for a framework or library, .xml files only
takes you so far. If so, you are better off generating an HTML-based documentation
directly from your source code instead.



## Generate HTML documentation from XML comments

Previous versions of Visual Studio had built-in support for generating HTML-based
documentation from C# code. However, I think Visual Studio 2003 was the last version
to have it. Removing this support is rather strange, so let's find a substitute!

After browsing around for a .NET version of [phpDoc](http://www.phpdoc.org) and
[JavaDoc](http://www.google.se/url?q=http://java.sun.com/j2se/javadoc), I found
[Doxygen](http://www.stack.nl/~dimitri/doxygen/). After giving it a try, I found
this walkthrough to work well:



## Configure Doxygen

To get started, first *download*, *install* and *start* [Doxygen](http://www.stack.nl/~dimitri/doxygen/),
then configure it like this:

*Wizard/Project*
* Enter *project name* and *version* - this will be used as page title
* Point out the *source code root folder*, which is where your source code is
* Check *Scan recursively* to make sure that all namespace folders are parsed
* Point out the *documentation destination folder*, which is where your HTML documentation will end up

*Wizard/Mode*
* Instead of Documented Entities, select "All Entities" - this will also extract a nice namespace list
* Since we are extracting documentation for C#, select "Optimize for Java or C# output"

*Wizard/Output*
* Make sure that *HTML* is checked and choose the format you prefer (I like the frame/tree format, but this is just a matter of taste :)
* Latex, Man pages, RTF and XML are fully optional options, not described in this post

*Wizard/Diagrams*
* Select "Use built-in class diagram generator"

*Expert/Project*
* Uncheck "FULL_PATH_NAMES" to avoid displaying file paths in the documentation

*Expert/Build*
* Check "EXTRACT_STATIC" to include static classes

*Run*
* Click "Run doxygen" to generate the HTML documentation. When done, view the result
by clicking "Show HTML output". 

If you are happy with the outcome, save the config for future use, so you don't
have to go through all the steps above each time.

When the HTML documentation is built, you can just upload it to wherever you want
your documentation to be available.

Hope this helps!