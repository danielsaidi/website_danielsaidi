---
title:  Using QuickLook with SwiftUI
date:   2022-06-27 08:00:00 +0000
tags:   swiftui quicklook

icon:   swiftui
assets: /assets/blog/2022/2022-06-27/
tweet:  https://twitter.com/danielsaidi/status/1541363945465942016?s=20&t=pHrBeDbqn-sQh8qvy0pmCg
---

SwiftUI is growing with every new release, but there are still old treasures to be found in various Apple frameworks, that aren't part of the core SwiftUI library. One example is `MapView` in MapKit, another is the amazing `quickLookPreview` view modifier in the QuickLook framework. Let's take a quick look.

SwiftUI support for QuickLook is a feature that I hadn't heard anything about, until I stumbled upon it by chance. With it, you can let users preview any previewable content at a certain URL, such as PDF files, images etc.

To use QuickLook with SwiftUI, you must first import the QuickLook framework. You can then apply the `quickLookPreview` view modifier and bind it to a mutable URL.

To see it in action, consider that we have a multi-platform SwiftUI app for iOS and macOS, that has an embedded image called `meadow.jpg`. Our `ContentView` looks like this:

```swift
import SwiftUI
import QuickLook

struct ContentView: View {

    @State
    var url: URL?

    var body: some View {
        Button("Preview") {
            url = Bundle.main.url(forResource: "meadow", withExtension: "jpg")
        }.quickLookPreview($url)
    }
}
```

That's actually all there is to it. Setting the `url` property will make the `quickLookPreview` view modifier present the previewable content at the provided url, in a way that suits the platform.

If we run this app in iOS, tapping the button presents the image in a full screen cover:

![A screenshot of using QuickLook in iOS]({{page.assets}}ios.png){:class="plain" width="450px"}

If we then run the same app on macOS, tapping the button presents the image in a nice modal:

![A screenshot of using QuickLook in iOS]({{page.assets}}macos.png){:class="plain" width="650px"}

Note that using QuickLook in SwiftUI is only available in iOS and macOS, not tvOS or watchOS.