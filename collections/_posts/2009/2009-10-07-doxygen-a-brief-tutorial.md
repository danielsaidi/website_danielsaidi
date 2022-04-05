---
title: Doxygen - A brief tutorial
date:  2009-10-07 08:00:00 +0100
tags:  .net documentation

image: /assets/blog/2009/10-07.png
---

I'm using Doxygen to generate a web-based documentation for various .NET projects.
This short post will show you how to configure Doxygen to achieve this.

![Doxygen Wizard]({{page.image}})

Doxygen supports extracting the documentation in various formats (HTML, LaTex, 
.man, XML etc.). To extract a basic HTML site from your documentation, just follow
these steps:

* Download and install Doxygen.
* Run the Doxygen wizard, which opens a small window with the Wizard tab selected.
* Open the "Project" section.
* Define Project name (e.g. ".NET Extensions").
* Define Project version or id (e.g. "1.0.0.0").
* Define Source code directory (your source code root).
* Check the "Scan recursively" check box.
* Define Destination dictionary (where you want the documentation to be extracted).
* Under the "Mode" section, select All entities and Optimize for Java or C#.
* Under the "Output" section, select the various output formats you want Doxygen to create.

Since you selected "All entities", you may think all entities will be extracted.
Well, sadly, no. For instance, Doxygen will not extract documentation for static
classes, unless you explicitly tell it to.

To fix this, select the "Expert" tab and make sure that `EXTRACT_STATIC` is checked. 
If not, you will find that static members are left out of the documentation. 

You can check other boxes to configure Doxygen in detail, then select the "Run" tab
and click "Run Doxygen" to start Doxygen, which will generate the documentation in the
specified destination folder.

If you think that the documentation is too plain and boring in HTML format, you can
skin it with CSS. The possibilities are endless.