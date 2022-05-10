---
title:  Building a rich text editor for UIKit, AppKit and SwiftUI
date:   2022-07-01 06:00:00 +0000
tags:   article swift swiftui

richtextkit:    https://github.com/danielsaidi/RichTextKit
---

In this post, we'll look at how to build a rich text editor for UIKit, AppKit and SwiftUI. We'll create new types to bridge the various platforms and extend a bunch of native types to extend the foundation support for rich text.


## Conclusion

Although UIKit and AppKit has a bunch of build-in support for rich text, a lot is still missing. Also, some things work very different in UIKit and AppKit, and SwiftUI has no support at all as this is written.

If you're interested in the source code, you can find it in my [RichTextKit]({{page.richtextkit}}) library. Don't hesitate to comment or reach out with any thoughts you may have. I'd love to hear your thoughts on this.