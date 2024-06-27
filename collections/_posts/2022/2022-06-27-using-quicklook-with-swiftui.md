---
title:  Using QuickLook in SwiftUI
date:   2022-06-27 08:00:00 +0000
tags:   swiftui quick-look

icon:   swiftui
assets: /assets/blog/22/0627/

tweet:  https://twitter.com/danielsaidi/status/1541363945465942016?s=20&t=pHrBeDbqn-sQh8qvy0pmCg
---

SwiftUI is growing with every release, but there are still old treasures to be found in various frameworks, that aren't part of core SwiftUI. Let's take a quick look at `QuickLook`!

I didn't know about SwiftUI's support for QuickLook, and stumbled upon it by chance. You can use it to preview any previewable URL content, such as PDFs, images etc.

To use QuickLook with SwiftUI, you must first import the `QuickLook` framework. You can then apply the `.quickLookPreview` view modifier and bind it to a URL.

Consider that we have a multi-platform SwiftUI app for iOS & macOS, that has an bundle image called `meadow.jpg`. This is how easy it is to enable quick look preview:

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

Setting the `url` property will make the `.quickLookPreview` modifier present the content at the provided url in a preview, in a way that suits the platform.

In iOS, the button presents the image in a full screen cover:

![A screenshot of using QuickLook in iOS]({{page.assets}}ios.png){:class="plain" width="450px"}

If we run the same app on macOS, tapping the button presents the image in a nice modal:

![A screenshot of using QuickLook in iOS]({{page.assets}}macos.png){:class="plain" width="650px"}

QuickLook is only available in iOS & macOS, not tvOS & watchOS. You can use `#if os(iOS)` and `#if os(macOS)` to conditionally enable it in multi-platform apps and SDKs.