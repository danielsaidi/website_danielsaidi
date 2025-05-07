---
title:  Linking to native type extensions in DocC
date:   2024-03-21 04:00:00 +0000
tags:   swift docc

assets: /assets/blog/24/0321/
image:  /assets/blog/24/0321.jpg

article:  /blog/2024/03/10/automating-docc-for-a-swift-package-with-github-actions

tweet:  https://x.com/danielsaidi/status/1770708080885477395
toot:   https://mastodon.social/@danielsaidi/112132403260601545
---

In this post, we'll take a look at how we can use Xcode 15's improved DocC capabilities to link to native type extensions, to provide even better documentation to our users.


## Background

DocC lets you document your code in a way that makes the public API easily discoverable. The docs can be browsed directly in Xcode 15, and even hosted on e.g. [GitHub Pages]({{page.article}}).

While DocC was already great, Xcode 15 made it even better. You can now use metadata to describe your articles, create interactive tabs & grids, and much more.

Another new feature is being able to show and link to native type extensions, which wasn't possible before. Let's take a look at how this works.


## Xcode 15 DocC extension support

If your code has extensions to native types, like `String` or SwiftUI `View`, Xcode 15 will add links to all extended modules at the bottom of your documentation root:

![DocC links to extended frameworks]({{page.assets}}topics.jpg)

You can tap on any link to navigate to a page that lists all extended types in that module:

![DocC links to extended types]({{page.assets}}types.jpg)

You can then tap on any type to navigate to a page that lists all extensions for that type:

![DocC extended type documentation]({{page.assets}}type.jpg)

These are all amazing additions in their own, but what's even better is that you can link to any extended type, as well as any extension, directly from your documentation.


## How to link to extended modules and types

As you can see in the first screenshot, DocC now adds links to all extended modules to the bottom of the documentation root page.

In the the screenshot, you can see deprecated types above the module links. DocC seems to place deprecations before module extensions by default.

To fix this, we can manually add links to the extended modules to a manually curated topic section, by adding a header followed by the module links:

```swift
### Extensions

- ``CoreFoundation``
- ``Foundation``
- ``Speech``
- ``Swift``
- ``SwiftUI``
- ``UIKit``
```

This lets you place the extensions at a more discoverable place within your documentation:

![DocC links to extended types]({{page.assets}}topic.jpg)

You can use the same approach to link to an extended module in inline text, for instance:

```markdown
This SDK adds a bunch of view extensions to ``SwiftUI``.
```

or a certain extended type:

```markdown
This SDK adds a bunch extensions to ``SwiftUI/View``.
```

You can also link directly to a certain extension:

```markdown 
Toolbars can be styled with the ``SwiftUI/View/toolbarStyle(_:)`` view modifier.
```

These links will compile with the rest of your documentation, which gives you compile-time warnings if you remove an extension without updating the documentation.


## Conclusion

Since many SDKs provide SDK-specific extensions to native types, Xcode 15's native type extension support is a massive improvement to an already amazing documentation engine.

Being able to link to extended modules, types, and individual extensions makes it possible to create even better documentation for your users.

And since the extension documentation is compiled, removing extensions without updating your documentation will finally give you compile-time warnings for outdated references.