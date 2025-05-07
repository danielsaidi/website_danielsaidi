---
title:  Creating a bordered button group in SwiftUI
date:   2023-06-01 06:00:00 +0000
tags:   swiftui

assets: /assets/blog/23/0601/
image:  /assets/blog/23/0601/image.jpg

tweet:  https://twitter.com/danielsaidi/status/1664403889699934208?s=20
toot:   https://mastodon.social/@danielsaidi/110471400509944102
---

Just like how SwiftUI buttons can use a `.bordered` button style, you can use `ControlGroup` to group several buttons together in a bordered group.

As an example, consider this rich text control panel, where the bottom row has two buttons to affect the text indentation level, as well as a segmented picker to change text alignment:

![Toolbar with indentation buttons]({{page.assets}}toolbar-buttons.jpg)

The buttons use a `.bordered` style, which makes them not as tall as the picker. I think it would be nicer if the buttons and picker aligned better, and to group the buttons together.

To fix this, you can wrap the buttons in a `ControlGroup` to make them look just like the segmented text alignment picker:

```swift
ControlGroup {
    Button("1") {}
    Button("2") {}
}
```

Since the control group is greedy, it will take up as much horizontal space as it can. If you don't want this greedy behavior, you can just add a `frame(width:)` to the group.

We can now group the indentation buttons together, to align with the segmented picker:

![Toolbar with a segmented indentation button group]({{page.assets}}toolbar-buttongroup-segmented.jpg)

If we want to take this even further, we can also group the style buttons on the second row:

![Toolbar with a segmented style toggle group]({{page.assets}}toolbar-stylegroup-segmented.jpg)

I think that this grouping much clearer indicates which buttons that belong together.