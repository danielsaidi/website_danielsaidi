---
title:  Detecting SwiftUI's preview mode
date:   2022-05-27 01:00:00 +0000
tags:   swiftui

icon:   swiftui
---

In this article, we'll take a look at how to determine if code is running in a SwiftUI preview, which may be nice if you need to disable certain things, like network calls.

{% include kankoda/data/open-source.html name="SwiftUIKit" %}

This information can be fetched from the `ProcessInfo` `environment` dictionary, by checking if the `XCODE_RUNNING_FOR_PREVIEWS` key has the string value `"1"`:

```swift
public extension ProcessInfo {
    
    var isSwiftUIPreview: Bool {
        environment["XCODE_RUNNING_FOR_PREVIEWS"] == "1"
    }
}
```

You shouldn't misuse this information, but if you have a preview problem that you need to work around, it may be a good alternative.


## How to make the code testable and easier to use

If you want to make this functionality easier to to find and be able to unit test and mock this information, I suggest adding a protocol that describes this capability:

```swift
protocol SwiftPreviewInspector {

    var isSwiftUIPreview: Bool { get }
}
```

You can then let `ProcessInfo` implement it:

```swift
extension ProcessInfo: SwiftPreviewInspector {}
```

You can now use `ProcessInfo` as the inspector in your app, to avoid relying on it directly.

## Conclusion

This was a short post, but I hope you found it helpful. You can find the source code in the [SwiftUIKit]({{project.url}}) library. Feel free to try it out and let me know what you think.