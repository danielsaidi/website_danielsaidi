---
title: NuPack - a quick walkthrough
date:  2010-10-08 12:00:00 +0100
tags:  archive
icon:  dotnet

nuget: https://www.nuget.org
---

[NuPack]({{page.nuget}}) is a free, open source, developer-focused package
manager for .NET. It looks absolutely awesome, so I downloaded it and gave
it a try. Let's have a look at how to use it.

To get started with NuPack, just do the following:

* Visit the [NuPack]({{page.nuget}}) web site.
* Click the download button to download the latest release.
* Double click on the downloaded file to install NuPack.
* Open any .NET solution or create a new one.
* Right-click "references".
* Voilá! A new “Add Package Reference” context menu item exists!

I hope that you see the subtle irony. Getting NuPack up and running is a walk in
the park. After completing the steps above, this is what you should see:

![Reference context menu](/assets/blog/2010/101008-1.png "Add Package Reference context menu")

When you click “Add Package Reference”, a new window opens, where you can search
for packages:

![Package Reference Window](/assets/blog/2010/101008-2.png "The Add Package Reference window")
 
In the image above, I search for log4net, which is a nice logging tool for .NET.
If I click the Install button, the package is downloaded and added to my project:

![Added reference](/assets/blog/2010/101008-3.png "The package reference is added to References")

If we now look in the solution folder structure, NuPack has created a “packages”
folder, which contains the downloaded package:

![Packages folder](/assets/blog/2010/101008-4.png "A “packages” folder is added to the project")

That’s it! If you need `log4net` in another project or solution, just repeat these
steps. Simple, huh?

Something to pay attention to, is that I noticed that the added `log4net`
reference points to the GAC:

![GAC Reference](/assets/blog/2010/101008-5.png "Strange behavior. The GAC is used as reference")

Since I’d rather have a reference to a physical .dll file instead of a package
reference, I just add the NuPack fetched .dll file to version control, then
remove the package reference and refer to the .dll file.

Conclusion? NuPack rocks!