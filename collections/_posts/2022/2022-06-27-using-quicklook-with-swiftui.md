---
title:  Using QuickLook in SwiftUI
date:   2022-06-27 08:00:00 +0000
tags:   swiftui

icon:   swiftui
assets: /assets/blog/22/0627/

tweet:  https://twitter.com/danielsaidi/status/1541363945465942016?s=20&t=pHrBeDbqn-sQh8qvy0pmCg
---

SwiftUI is growing with every release, and new amazing features keep on arriving. Today, let's take a quick look at `QuickLook`!

In SwiftUI, you can use QuickLook to preview URL-based content, like PDFs, images etc. To use it, just import the `QuickLook` framework, apply a `.quickLookPreview` view modifier and bind it to a URL.

Consider that we have a SwiftUI app that has an image called `meadow.jpg` in its bundle. This is how easy it is to enable quick look preview:

```swift
import SwiftUI
import QuickLook

struct ContentView: View {

    @State
    var url: URL?

    var body: some View {
        Button("Preview") {
            url = Bundle.main.url(forResource: "meadow", withExtension: "jpg")
        }
        .quickLookPreview($url)
    }
}
```

Setting the `url` property will make the `.quickLookPreview` modifier present the image at the url in a preview, in a way that suits the platform.

In iOS, the button presents the image in a full screen cover:

![A screenshot of using QuickLook in iOS]({{page.assets}}ios.png){:class="plain" width="450px"}

If we run the same app on macOS, tapping the button presents the image in a nice modal:

![A screenshot of using QuickLook in iOS]({{page.assets}}macos.png){:class="plain" width="650px"}

QuickLook is only available in iOS & macOS, not tvOS & watchOS. You can use `#if os(iOS)` and `#if os(macOS)` to conditionally enable it in multi-platform apps and SDKs.