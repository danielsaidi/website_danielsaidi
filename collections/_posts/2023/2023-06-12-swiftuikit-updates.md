---
title:  SwiftUIKit Updates
date:   2023-06-12 06:00:00 +0000
tags:   swiftui open-source

image:  /assets/headers/swiftuikit.png

release:    https://github.com/danielsaidi/SwiftUIKit/releases/tag/3.3.0
---

SwiftUIKit 3.3 is out! It has some nice additions and changes, like more tools for using `Codable` with `AppStorage` and new list utils. Let's take a look at what's new in this minor update.

![SwiftUIKit logo]({{page.image}})


## Support for using Codable with @AppStorage

SwiftUI doesn't support using the `@AppStorage` property wrapper on `Codable` properties. As a result, persisting codable types involves writing much more code than when using `@AppStorage`.

To address this, SwiftUIKit has had a `@Persisted` property wrapper that can be applied to any codable type. I however never liked using a different property wrapper just because the type was codable.

To improve things, SwiftUIKit 3.3 adds new extensions that make it possible to apply `@AppStorage` to any `Codable` property. The old `@Persisted` wrapper is now deprecated.


## New list utilities

SwiftUIKit 3.3 also adds new list-specific views, like `ListBadgeIcon`,`ListCard`, `ListDragHandle`, `ListSectionTitle` and `ListShelfSection`.

These views will most probably be moved to a separate library in the future, but until then, they're here to let you build even better lists in SwiftUI.


## New text editor style

SwiftUI currently doesn't support styling text editors like you can with text fields. SwiftUIKit 3.3 therefore adds a new `TextEditorStyle` that lets you style a `TextEditor` like a `TextField`.


## New image extensions

SwiftUIKit 3.3 adds more image utilities. For instance, the multi-platform `ImageRepresentable` has new resizing tools, and `Image.symbol(...)` is a new shorthand for `Image(systemName:)`.


## No more demo application

From SwiftUIKit 3.3, I have decided to remove the demo application and instead put more effort into the source code previews.


## Conclusion

Other than what's mentioned above, SwiftUIKit 3.3 also has some bug fixes and tweaks to improve things here and there. For more information, see the [project repository]({{site.swiftuikit}}) and the 3.3 [release notes]({{page.release}}).