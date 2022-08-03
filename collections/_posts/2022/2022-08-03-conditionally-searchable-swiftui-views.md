---
title:  Conditionally searchable SwiftUI views
date:   2022-08-03 06:00:00 +0000
tags:   swiftui

icon:   swiftui

tweet:  https://twitter.com/danielsaidi/status/1554961934268653576?s=20&t=QiuVINngfCiBPi7cMSCP4w

marco: https://twitter.com/MarcoEidinger
marco-discussion: https://twitter.com/MarcoEidinger/status/1554992426846064640?s=20&t=SXaP0DWK018TSzID1OxMtg

skeuomorphism: https://en.wikipedia.org/wiki/Skeuomorph
swiftuikit: https://github.com/danielsaidi/SwiftUIKit
---

SwiftUI 3 added the `searchable` view modifier, which makes it possible to add a search field to any SwiftUI view. In this post, let's take a quick look at how to make this modifier conditional, which is an approach you can apply to other extensions as well.

Using the `searchable` view modifier is really easy. You just have to provide it with a `text` binding, a `prompt` that will serve as a placeholder text, as well as an optional `placement`:

```swift
struct MyView: View {

    @State
    private var searchQuery = ""

    List {
        ...
    }.searchable(text: $searchQuery, prompt: "Search")
}
```

This will place a search bar above the list on iPhone and as a trailing navigation toolbar item on iPad, if you have a navigation bar. The behavior of the modifier is meant to fit each platform, which means that it works differently depending on where you apply it.


## Making searchable conditional

In an app that I'm working on, I want a [skeuomorphic]({{page.skeuomorphism}}) user experience, which means that I want as little UI as possible. However, having a search bar can really help people find content in various lists.

To take both the skeuomorphism as well as the user's potential search needs into consideration, I have chosen to make the search field optional, so that users with a lot of content can toggle the search field on for various types of content.

To fix this, I decided to implement a conditional `searchable` modifier, that takes a boolean condition and either returns the plain view or a searchable variant, based on the condition.

This is by no means a sophisticated solution, and I actually considered not writing this blog post since it's so basic, but this is an approach that I often return to and one that perhaps can inspire some of you.

The extension is very basic:

```swift
@available(iOS 15.0, macOS 12.0, tvOS 15.0, watchOS 8.0, *)
extension View {

    @ViewBuilder
    func searchable(
        if condition: Bool,
        text: Binding<String>,
        placement: SearchFieldPlacement = .automatic,
        prompt: String
    ) -> some View {
        if condition {
            self.searchable(
                text: text,
                placement: placement,
                prompt: prompt)
        } else {
            self
        }
    }
}
```

I have added this extension to [SwiftUIKit]({{page.swiftuikit}}). Feel free to try it out. If you think this is too basic content for this blog, let me know and I'll refrain from writing these kind of trivial posts.


## Important about conditional views

As [Marco Eidinger]({{page.marco}}) points out on [Twitter]({{page.marco-discussion}}), conditional views must not be misused in SwiftUI. Changing the condition will recreate the part of the view hierarchy that is wrapped by the if/else clause, which will lead to animations breaking and other potential problems. 

However, I think that this is something to be aware of and use when it makes sense, rather than to have a hard rule to never use conditional views. In my case, I use it to make it possible for users to toggle searchability in various parts of the app, which will eventually involve either applying the `searchable` modifier or not, which means that a conditional view is inevitable.

A better approach would have been if the `searchable` modifier would have a `placement` option that hides the search field. This would have let you change the behavior of the modifier instead of omitting it altogether, which would have been a lot better.

However, in my case, toggling searchability from another screen than where it's applied, means that this really doesn't matter, since user will only see the effect of toggling this once they return to the screen where the modifier is used. All in all, it's all about your use case. Just be careful and only use conditional modifiers them when you know the effect they will have and when it fits your use case.

Thanks Marco for pointing this out!