---
title:  SwiftUI 4 is killing my open-source projects
date:   2022-06-10 10:00:00 +0000
tags:   swiftui

icon:   swiftui
tweet:  https://twitter.com/danielsaidi/status/1535381264651685888?s=20&t=zYlDmBc3hsA5zmLXletIDg

bottomsheet: https://github.com/danielsaidi/BottomSheet
swiftuikit: https://github.com/danielsaidi/SwiftUIKit

mecid: https://twitter.com/mecid
mecid-post: https://swiftwithmajid.com/2019/12/11/building-bottom-sheet-in-swiftui/
---

SwiftUI 4 is killing my open-source projects...and I love it! The more that's added to SwiftUI, the less we have to pull in 3rd party dependencies to solve common use-cases. This is a huge win for everyone.

WWDC'22 has wrapped up, and while I haven't been able to follow it as closely as previous years, I've tried to keep myself up to date through blogs and tweets. One thing that people seem to be loving is all the amazing additions to SwiftUI, which really step up its game this year.

One example of a great new feature is that the resizable sheets that were added to UIKit last year, finally made their way to SwiftUI this year. You can now use the `presentationDetents` (strange name) view modifier to define the available sheet sizes that a view should support. You can use declarative sizes like `.medium` and `.large`, as well as points and fractions of the screen.

This is more or less exactly what I did in my [BottomSheet]({{page.bottomsheet}}) library, which was based on [Majid Jabrayilov's]({{page.mecid}}) sample code that he posted in [this great blog post]({{page.mecid-post}}) in 2019. This means that with SwiftUI 4, BottomSheet will no longer be needed, although there is still a use for it if your app targets iOS 15 and earlier.

There are also a bunch of new features that replace utilities and extensions that I have in my [SwiftUIKit]({{page.swiftuikit}}) library. For instance, the `ShareSheet` will be replaced with the new, native `ShareLink`. This means that I'll have to deprecate this kind of now obsolete functionality in this library as well, and point users to these new, native additions. This is all good, since the less we have to implement ourselves, the better. 

However, I now consider how to best deprecate the obsolete functionality. I could use the `@available` attribute and instruct developers to use the native SwiftUI functionality instead, but that would be really annoying for people who still depend on these deprecated parts in apps that target iOS 15 and earlier, since using deprecated logic will generate build warnings.

For [BottomSheet]({{page.bottomsheet}}), I think I'll just freeze the GitHub project and update the readme with an initial text that describes why the project is abandoned, then point people to SwiftUI 4. I will then keep the project for a few years, until I eventually remove it. For the deprecated functionality in [SwiftUIKit]({{page.swiftuikit}}), I can just deprecate and ask people to copy it to their own projects if they still intend to use it. They will then have a choice to remove the build warnings. If you have any other ideas, I'd love to hear them.

All in all, I'm really excited about all the big and small new features that arrive in SwiftUI 4. I'll install the developer betas on my devices, verify that my open-source projects still behave as they should, then get started on bumping my apps to the latest standards. Last year's addition of async/await, local packages and a bunch of great Swift features, together with this year's SwiftUI additions are great tools for making this happen. However, since promises tend to render ambitions stale, I will not make any promises about getting there. Time will tell. Until then, I'll just enjoy all these new features.

Thank you Apple!