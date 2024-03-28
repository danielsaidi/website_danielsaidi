---
title:  How to define a custom file header for a Swift Package
date:   2024-03-21 04:00:00 +0000
tags:   swift spm

assets: /assets/blog/2024/240328/
image:  /assets/blog/2024/240328/title-uncached.jpg

post_project_header: https://useyourloaf.com/blog/changing-xcode-header-comment/
so: /blog/2024/03/10/automating-docc-for-a-swift-package-with-github-actions
furtherreading: https://gist.github.com/brennanMKE/660171e9dfa16892b41f4bc0b3a7410f

tweet:  https://x.com/danielsaidi/status/1773253724154007634
toot:   https://mastodon.social/@danielsaidi/112132403260601545
---

In this post, we'll take a look at how to define a custom file header for a Swift package, that will then be used for all new files that we create for that package.


## Background

When you add a new file to a Swift package, the default package configuration will add the following file header to the file:

![A screenshot of Xcode's default file header for a Swift package]({{page.assets}}newfile_package.png){:class="screenshot"}

Compare this with the default header that we get when adding files to an Xcode project:

![A screenshot of Xcode's default file header for an Xcode project]({{page.assets}}newfile_project.png){:class="screenshot"}

Regardless of what header format you prefer, the project default is at least more complete, since it includes the project name, copyright info, etc. The package header is just...broken. 

With the default configuration, we have to adjust or remove the header for all new files that we add to the package. Very tedious.


## How to customize the file header for an Xcode project

You can set up a custom file header for any Xcode project. This involves creating a custom `IDETemplateMacros.plist` file, which is thoroughly described in [this post]({{page.post_project_header}}).

A project can also specify additional information, like the organization name, which will add a bottommost copyright line to the header, as you saw in the screenshot above.

Turns out that we can do the same for a Swift package, just in a slightly different way.


## How to customize the file header for a Swift package

To set up a custom file header for a Swift package, just add the `IDETemplateMacros.plist` file to this location instead:

```
 <Package Root>/.swiftpm/xcode/package.xcworkspace/xcshareddata
```

Here's an example of a header that adds the file & package name, followed by the author & creation date, as well as an additional copyright line:

```
<?xml version="1.0" encoding="UTF-8"?>
<!DOCTYPE plist PUBLIC "-//Apple//DTD PLIST 1.0//EN" 
"http://www.apple.com/DTDs/PropertyList-1.0.dtd">
<plist version="1.0">
<dict>
    <key>FILEHEADER</key>
    <string>
//  ___FILENAME___
//  ___WORKSPACENAME___
//
//  Created by ___FULLUSERNAME___ on ___DATE___.
//  Copyright © ___YEAR___ ___FULLUSERNAME___. All rights reserved.
//</string>
</dict>
</plist>
```

See [this gist]({{page.furtherreading}}) for a list of all the available variables that you can use in your custom header.

Make sure to check `.gitignore` so that the package doesn't ignore the file, if you want it to be used all across your package.