---
title:  Building a rich text editor for UIKit, AppKit and SwiftUI
date:   2022-07-01 06:00:00 +0000
tags:   swift swiftui

icon:   swiftui
assets: /assets/blog/2022/2022-07-01/

oribi:         https://www.oribi.se/en
oribi-writer:  https://oribi.se/en/apps/oribi-writer/
richtextkit:   https://github.com/danielsaidi/RichTextKit
---

In this post, we'll look at how to build a rich text editor for UIKit, AppKit and SwiftUI. We'll extend a bunch of native types to extend the foundation support for rich text, create new types to bridge the various platforms and make sure that we have a nice, working foundation that can be expanded further.


## Background

Rich text editing on Apple's platforms is pretty straightforward in its easiest form. Just create a `UITextView` in UIKit (iOS and tvOS) and an `NSTextView` in AppKit (macOS) and stick to attributed strings, and you're good to go. The text view will automatically support different text styles, font sizes, alignments etc. and can even insert images and other kind of rich content.

You could think that that's all it takes, but unfortunately it's not that simple. Many basic tasks are pretty complicated to achieve with these views and attributed strings, and requires the same wheel to be discovered and invented by every developer who take on this task. Throwing in multi-platform support makes the problem even worse, since the various platforms handles strings, attributes, attachments etc. differently, which means that we have to find a way to design around the differences of each platform.

Another complication is SwiftUI, where we have to find a way to embed and bridge the platform-specific text views in a way that works on all platforms. We also need some way of letting SwiftUI affect the rich text through the underlying views, and for the platform-specific views and their delegates to make SwiftUI update correctly, using observable state.

All in all, this is a pretty complicated task, which is why I'm happy to announce that my client [Oribi]({{page.oribi}}) has given me permission to open-source the rich text engine that I created for them as part of building a brand new version of their text editor [Oribi Writer]({{page.oribi-writer}}).

![OribiWriter screenshot]({{page.assets}}oribi-writer.jpg)

OribiWriter is a rich text editor that's written in SwiftUI, using the new document app capabilities. It lets you view and edit rich text, with additional features like spellchecking, word prediction, text-to-speech, lexicon etc. The first version supports toggling bold, italic and underline, adjusting font size and alignment, inserting images, highlighting text, scroll to follow the text being read etc. The app is out for iOS and will soon be released as a native macOS app as well.

When building this new version of the app (the previous was 10+ year old and written in Objective-C), I started with an iOS version for iPhone and iPad. Although the app was the same for both platforms, the UI was quite different depending on if you were using the app on a small or large screen. I was careful to design the foundation as platform-agnostic as possible, since I wanted to build an macOS version later. Designing for the unknown is often a vain task, but in this case, I just designed the system in a way that made sense for both UIKit and AppKit.

Long story short, the iOS version of Oribi Writer launched earlier this year, with a new minor update soon being released to improve a bunch of things. For the macOS version, I made a few assumptions that didn't hold true, such as assuming that both a UITextView and an NSTextView scrolls in the same way (spoiler alert - they do not). Turns out that UIKit and AppKit are different in many ways, as are many other parts of the foundation. However, having a platfrom-agnostic engine made the required adjustments pretty easy to implement, without having to change the application layer all that much.

In this post, we'll start from the beginning with nothing but the native foundation support that we get from apple. We'll then work our way towards a library that has a basic rich text engine that works with UIKit, AppKit and SwiftUI and has the capability to present and edit rich text, apply text styles, change font size and alignment and sync any changes between SwiftUI and the underlying platforms. We will leave images for a future post, since this is a pretty complicated topic.

The result will be released as an open-source library called [RichTextKit]({{page.richtextkit}}), which you'll be able to use in your own apps. When you read this, the library already exists, although it may not yet have all the features that it will have later. I will keep expanding the library over time, and will try to cover interesting topics in future blog posts.

With all that said, let's code.


## Creating a rich text view

Although `UITextView` and `NSTextView` contains a bunch of functionality, we will probably want to extend them with more functionality later on. As such, let's start by creating a new `RichTextView` view for UIKit and AppKit:

```swift
#if os(macOS)
import AppKit

/**
 This view inherits `NSTextView` and applies additional rich
 text capabilities to it.
 */
public class RichTextView: NSTextView {
}
#endif
```

```swift
#if os(iOS) || os(tvOS)
import UIKit

/**
 This view inherits `UITextView` and applies additional rich
 text capabilities to it.
 */
public class RichTextView: UITextView {
}
#endif
```

As you can see, we can only implement this view for `macOS`, `iOS` and `tvOS`, since `UITextView` is not available on `watchOS`. However, many of the native extensions that we will add later *will* be supported by watchOS, which means that the library will make it easier to work with rich text on that platform as well. We will just have a text editor. 






## Conclusion

Although UIKit and AppKit has a bunch of build-in support for rich text, a lot is still missing. Also, some things work very different in UIKit and AppKit, and SwiftUI has no support at all as this is written.

If you're interested in the source code, you can find it in my [RichTextKit]({{page.richtextkit}}) library. Don't hesitate to comment or reach out with any thoughts you may have. I'd love to hear your thoughts on this.