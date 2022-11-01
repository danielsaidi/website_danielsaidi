---
title:  Detecting SwiftUI preview mode
date:   2022-05-27 01:00:00 +0000
tags:   swiftui swiftui-previews

icon:   swiftui

swiftuikit: https://github.com/danielsaidi/SwiftUIKit
---

In this post, we'll take a quick look at how to detect whether or not a SwiftUI view is being generated in a preview or not, which can be used to solve various problems.

There may come a time when you need to know whether or not a view is being rendered in a preview or not, or rather if an Xcode process is running for an SwiftUI preview or not. I have used this information to solve various early SwiftUI problems, e.g. when previews crashed while rendering view hierarchies that used assets from external Swift packages.

This info can be provided by a class called `ProcessInfo`, and can be retrieved from its `environment` dictionary using the `XCODE_RUNNING_FOR_PREVIEWS` key:

```swift
public extension ProcessInfo {
    
    var isSwiftUIPreview: Bool {
        environment["XCODE_RUNNING_FOR_PREVIEWS"] == "1"
    }
}
```

You shouldn't misuse this information, but if you have a preview problem that you need to work around, this property may be a good last resort.


## Conclusion

This was a short post, but I hope you found it helpful. You can find the source code in my [SwiftUIKit]({{page.swiftuikit}}) library. Feel free to try it out and let me know what you think.