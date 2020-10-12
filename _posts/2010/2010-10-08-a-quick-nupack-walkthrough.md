---
title: A quick NuPack walkthrough
date:  2010-10-08 12:00:00 +0100
tags:  .net
---

Yesterday, I got a demonstration of [NuPack](http://nuget.codeplex.com/): a free,
open source, developer-focused package manager for .NET. It looked awesome, so I
visited the web site, downloaded NuPack and gave it a try. This is how you do it:

* Visit the [NuPack](http://nuget.codeplex.com/) web site
* Click the download button to download the latest release
* Double click on the downloaded file to install NuPacl
* Open any .NET solution or create a new one
* Right-click "references"
* Voilá! A new “Add Package Reference” context menu item exists!

I hope that you see the subtle irony. Getting NuPack up and running is a walk in
the park. After completing the steps above, this is what you should see:

![Reference context menu](/assets/blog/2010-10-08-1.png "Add Package Reference context menu")

When you click “Add Package Reference”, a new window opens, where you can search
for packages:

![Package Reference Window](/assets/blog/2010-10-08-2.png "The Add Package Reference window")
 
In the image above, I search for log4net, which is a nice logging tool for .NET.
If I click the Install button, the package is downloaded and added to my project:

![Added reference](/assets/blog/2010-10-08-3.png "The package reference is added to References")

If we now look in the solution folder structure, NuPack has created a “packages”
folder, which contains the downloaded package:

![Packages folder](/assets/blog/2010-10-08-4.png "A “packages” folder is added to the project")

That’s it! If you need log4net in another project or solution, just repeat these
steps. Simple, huh?

Something to pay attention to, however, is that I noticed that the added log4net
reference points to the GAC:

![GAC Reference](/assets/blog/2010-10-08-5.png "Strange behavior. The GAC is used as reference")

Since I’d rather have a reference to the physical .dll file instead of a package
reference, I can just make sure the NuPack downloaded .dll file is under version
control, then remove the package reference and refer to the .dll file instead.

Conclusion? NuPack rocks!