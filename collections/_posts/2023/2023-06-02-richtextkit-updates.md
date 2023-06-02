---
title:  RichTextKit updates
date:   2023-06-02 06:00:00 +0000
tags:   swiftui rich-text

icon:   swiftui
assets: /assets/blog/2023/2023-06-02/

richtextkit: https://github.com/danielsaidi/RichTextKit
release: https://github.com/danielsaidi/RichTextKit/releases/tag/0.6.0
---

After a long time away, I finally had some time to revisit RichTextKit and add a couple of new features, like indentation support and grouped controls.

RichTextKit is a fun project that aims to simplify adding rich text editing support to iOS, macOS, tvOS and watchOS. However, since I don't work with rich text editing on a daily basis, I get little time to work on it.

Thanks to a client project, I've had the opportunity to revisit the project, and am happy to release a new version that adds some new features from the community.

One such feature is support for increasing and decreasing the text indent level. There are also new context functions for setting and resetting the rich text and extensions to handle image attachments.

I have also redesigned the format sheet on iOS and the format sidebar on macOS, which now group related buttons. This is what it looks like on iOS:

![Sheet with grouped buttons]({{page.assets}}format-sheet.jpg){:width="450px"}

And this is what it looks like on macOS:

![Sidebar with grouped buttons]({{page.assets}}format-sidebar.jpg){:width="450px"}

Other than that, there are a bunch of tweaks and bug fixes here and there. This can all be found in the 0.6 release, For more information, see the [project repository]({{page.richtextkit}}) and the [release notes]({{page.release}}).