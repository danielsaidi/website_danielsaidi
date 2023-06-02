---
title:  Creating a bordered button group
date:   2023-06-01 06:00:00 +0000
tags:   swiftui

icon:   swiftui
assets: /assets/blog/2023/2023-06-01/

tweet:  https://twitter.com/danielsaidi/status/1664403889699934208?s=20
toot:   https://mastodon.social/@danielsaidi/110471400509944102

swiftuikit: https://github.com/danielsaidi/SwiftUIKit
---

The same way SwiftUI buttons can be given a `.bordered` button style to apply a nice, round border, you can use `ControlGroup` to group several buttons together in a bordered group. Let's look at how.

As an example, consider this rich text control panel, where the bottom row has two buttons to affect the text indentation level, as well as a segmented picker to change text alignment:

![Toolbar with indentation buttons]({{page.assets}}toolbar-buttons.jpg)

Both buttons use a `.bordered` button style, which makes them not as tall as the picker. I think it would be nice if the buttons and picker aligned better, and to group the two buttons together, since they affect the same text property.

To fix this, you can wrap the buttons in a `ControlGroup` to make them look like a segmented picker:

```swift
ControlGroup {
    Button("1") {}
    Button("2") {}
}
```

Since the control group is greedy, it will take up as much horizontal space as it can. If you don't want this greedy behavior, you can just add a `frame(width:)` to the group.

We can now group the indentation buttons together in a group that aligns with the segmented picker:

![Toolbar with a segmented indentation button group]({{page.assets}}toolbar-buttongroup-segmented.jpg)

If we want to take this even further, we can group the style buttons on the second row as well:

![Toolbar with a segmented style toggle group]({{page.assets}}toolbar-stylegroup-segmented.jpg)

Although the rightmost stepper controls are a bit taller, I think that these groups much clearer indicate which buttons that belong together.