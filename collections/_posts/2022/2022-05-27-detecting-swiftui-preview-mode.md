---
title:  How to detect SwiftUI preview mode
date:   2022-05-27 01:00:00 +0000
tags:   swiftui

icon:   swiftui

swiftuikit: https://github.com/danielsaidi/SwiftUIKit
---

In this post, we'll take a quick look at how to determine if code is running in a SwiftUI preview or not.

This information can be fetched from the `ProcessInfo` `environment` dictionary, by checking if the `XCODE_RUNNING_FOR_PREVIEWS` key has the string value `"1"`:

```swift
public extension ProcessInfo {
    
    var isSwiftUIPreview: Bool {
        environment["XCODE_RUNNING_FOR_PREVIEWS"] == "1"
    }
}
```

You shouldn't misuse this information, but if you have a preview problem that you need to work around, this property may be a good last resort.

For instance, I have used this information to solve various early SwiftUI problems, e.g. when previews crashed while using assets from external Swift packages. I have also used it to disable functionality that should not be triggered by previews, such as validating source code licenses.


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

then create a standard implementation that uses `ProcessInfo`:

```swift
class StandardSwiftPreviewInspector: SwiftPreviewInspector {

    public var isSwiftUIPreview: Bool {
        ProcessInfo.processInfo.isSwiftUIPreview
    }
}
```

## Conclusion

This was a short post, but I hope you found it helpful. You can find the source code in my [SwiftUIKit]({{page.swiftuikit}}) library. Feel free to try it out and let me know what you think.