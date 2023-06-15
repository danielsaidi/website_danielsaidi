---
title:  SwiftUI Prefers Semantics
date:   2023-06-15 06:00:00 +0000
tags:   swiftui open-source

icon:   swiftui

post:   https://danielsaidi.com/blog/2020/12/25/building-a-video-streaming-app-for-ios-in-swiftui
---

SwiftUI receives amazing updates every year. If you however have to support old OS versions, you may be unable to use the new tools for some years, or jump through hoops to make it work. In this post, let’s look at a semantic approach to use new, non-critical features in apps that targets old OS versions.

As an example, consider [a video streaming app]({{page.post}}) I built a few years ago. Although it worked great, some users did report that the bottom home indicator line was annoying when watching movies, since it didn't fade out when the video player was presented.

This is very easy to fix in UIKit, but since the app used the new SwiftUI 2 `@main` and `App` APIs instead of a `UIViewController`, and SwiftUI didn't have any native APIs for hiding the home indicator, the app did have to resort to hacks to hide it, or leave it visible until SwiftUI added a native way to hide it.

As SwiftUI 3 then introduced a new `.persistentSystemOverlays(.hidden)` view modifier, hiding the home indicator went from very complicated to trivial. However, since the app had to support older iOS versions, we could not use the view modifier directly.

When I find myself in these situations, where bumping the deployment target is not an option, and the feature is not critical, I prefer to add a view extension that expresses the intent and applies the feature if it's available, while simply ignoring it if it's not.

In the `.persistentSystemOverlays(.hidden)` case, this means that I would add this view extension:

```swift
func prefersPersistentSystemOverlaysHidden() -> some View {
    if #available(iOS 16.0, macOS 13.0, tvOS 16.0, watchOS 9.0, *) {
        return self.persistentSystemOverlays(.hidden)
    } else {
        return self
    }
}
```

The `prefers` prefix clearly indicates that the view *prefers* the view modifier, but that it's not required. Users on new OS versions will then get the modifier, while users on older OS versions will have a less optimal experience. 

As a developer, you now only have to add a `prefers` before the API you want to use, and don't have to add a bunch of OS checks in your code. Just make sure to test both execution paths before shipping. To make this easier, I have a global flag that I set to `false` to make all extentions return the original view.

I have added some of these `prefers` extensions to [SwiftUIKit]({{site.swiftuikit}}). Check them out if you too struggle with these kinds of OS checks in your code from time to time.