---
title:  Setting up a custom Swift Package file header
date:   2024-03-28 04:00:00 +0000
tags:   swift spm

redirect_from: /blog/2024/03/28/how-to-define-a-custom-file-header-for-a-swift-package

assets: /assets/blog/24/0328/
image:  /assets/blog/24/0328.jpg
image-show: 0

post_project_header: https://useyourloaf.com/blog/changing-xcode-header-comment/
so: /blog/2024/03/10/automating-docc-for-a-swift-package-with-github-actions
furtherreading: https://gist.github.com/brennanMKE/660171e9dfa16892b41f4bc0b3a7410f

tweet:  https://x.com/danielsaidi/status/1773253724154007634
toot:   https://mastodon.social/@danielsaidi/112132403260601545
---

In this post, we'll take a look at how to set up a custom Swift Package file header, which is then used for all new Swift files that we create in that package.


## Background

When you add a new Swift file to a Swift Package, Xcode will by default add the following header comment to the file:

![A screenshot of Xcode's default file header for a Swift package]({{page.assets}}newfile_package.png){:class="screenshot"}

Compare this with the default header that we get when we add a file to an Xcode project:

![A screenshot of Xcode's default file header for an Xcode project]({{page.assets}}newfile_project.png){:class="screenshot"}

Regardless of which format you prefer, the project default is at least complete, while the package header is just...broken. Let's fix that!


## How to customize the file header for an Xcode project

You can set up a custom file header for any Xcode project. This involves creating a custom `IDETemplateMacros.plist` file, which is thoroughly described in [this post]({{page.post_project_header}}).

A project can also specify additional information, like the organization name, which will add a bottommost copyright line to the header comment, as you saw above.

Turns out that we can do the same for a Swift package, only in a slightly different way.


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

Finally, make sure to check that your Swift Package's `.gitignore` doesn't ignore the file.