---
title:  RichTextKit updates
date:   2023-06-02 06:00:00 +0000
tags:   swiftui rich-text open-source

icon:   swiftui
assets: /assets/blog/2023/230602/
image:  /assets/headers/richtextkit.png
---

{% include kankoda/data/open-source.html name="RichTextKit" version="0.6.0" %}After a long time away from the project, I finally had some time to revisit [RichTextKit]({{project.url}}) and add some new features, like indentation support and grouped controls.

![SwiftUIKit logo]({{page.image}})

[RichTextKit]({{project.url}}) aims to make rich text editing easier in SwiftUI & UIKit, on iOS, macOS, tvOS & watchOS. However, since I don't work with rich text apps on a daily basis, I get very little time to work on the library.

Thanks to a client project, I've had the opportunity to revisit the project, and am happy to release a new version that adds some new features from the community.

One such feature is support for increasing and decreasing the text indent level. There are also new context functions for setting and resetting the rich text and extensions to handle image attachments.

I have also redesigned the format sheet on iOS and the format sidebar on macOS, so that it now groups related buttons. This is what it looks like on iOS:

![Sheet with grouped buttons]({{page.assets}}format-sheet.jpg){:width="450px"}

...and this is what it looks like on macOS:

![Sidebar with grouped buttons]({{page.assets}}format-sidebar.jpg){:width="450px"}

Other than that, there are many other tweaks and bug fixes in this update. For more info, visit the [project site]({{project.url}}) and see the [release notes]({{project-version}}).