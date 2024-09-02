---
title: NuPack - a quick walkthrough
date:  2010-10-08 12:00:00 +0100
tags:  archive

nuget: https://www.nuget.org
---

[NuPack]({{page.nuget}}) is a free, open source, developer-focused package manager for .NET. It looks great, so I downloaded it and gave it a try. Let's have a look at how to use it.

To get started with NuPack, just do the following:

* Visit the [NuPack]({{page.nuget}}) web site.
* Click the download button to download the latest release.
* Double click on the downloaded file to install NuPack.
* Open any .NET solution or create a new one.
* Right-click "references".
* Voilá! A new “Add Package Reference” context menu item exists!

Getting NuPack working was easy. After completing the steps above, you should see this:

![Reference context menu](/assets/blog/10/1008-1.png "Add Package Reference context menu")

Click “Add Package Reference” opens a modal where you can search for packages:

![Package Reference Window](/assets/blog/10/1008-2.png "The Add Package Reference window")
 
In the image above, I search for `log4net`, which is a nice logging tool for .NET. If I click the Install button, the package is downloaded and added to my project:

![Added reference](/assets/blog/10/1008-3.png "The package reference is added to References")

If we look in the solution folder structure, NuPack has created a “packages” folder, which contains the downloaded package:

![Packages folder](/assets/blog/10/1008-4.png "A “packages” folder is added to the project")

If you need `log4net` in another project or solution, just repeat these steps. Simple, huh?

Something to note, is that I noticed that the added `log4net` reference points to the GAC:

![GAC Reference](/assets/blog/10/1008-5.png "Strange behavior. The GAC is used as reference")

Since I’d rather have a reference to a physical .dll file, I just add the NuPack fetched .dll file to version control, then remove the package reference and refer to the .dll file.

Conclusion? NuPack rocks!