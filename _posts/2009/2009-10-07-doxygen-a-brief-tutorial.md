---
title: Doxygen - A brief tutorial
date:  2009-10-07 08:00:00 +0100
tags:  .net documentation
---

I am using Doxygen to generate a web-based documentation from my well-documented
NExtra project's source code. Doxygen also supports extracting the documentation
in various formats (HTML, LaTex, .man, XML etc.). I'll go with HTML for now.

![Doxygen Wizard](/assets/blog/2009-10-07.png)

To extract a simple HTML site from your documentation, just follow these steps:

* [Download](http://www.stack.nl/~dimitri/doxygen/) and install Doxygen
* Run the Doxygen wizard, which opens a small window with the Wizard tab selected
* Open the "Project" section
* Define Project name (e.g. ".NET Extensions")
* Define Project version or id (e.g. "1.0.0.0")
* Define Source code directory (your source code root)
* Check the "Scan recursively" check box
* Define Destination dictionary (where you want the documentation to be extracted)
* Under the "Mode" section, select All entities and Optimize for Java or C#
* Under the "Output" section, select the various output formats you want Doxygen to create.

Since you selected "All entities", you may think all entities will be extracted.
Well, sadly, no. For instance, Doxygen will not extract documentation for static
classes, unless you explicitly tell it to.

So, select the "Expert" tab (wow, an expert already huh?) and make sure that the
`EXTRACT_STATIC` checkbox is checked. If not, you will find that static parts of
your application are left out of the documentation. You can also check the other
boxes to configure Doxygen in detail.

Finally, you are now ready to select the "Run" tab and click "Run Doxygen". This
will start Doxygen, which will place documentation in your specified destination
folder.

If you think that the documentation is too plain and boring in HTML format, skin
it with CSS. The possibilities are endless.