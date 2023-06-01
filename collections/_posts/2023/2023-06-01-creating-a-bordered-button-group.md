---
title:  Creating a bordered button group
date:   2023-06-01 06:00:00 +0000
tags:   swiftui

icon:   swiftui
assets: /assets/blog/2023/2023-06-01/

swiftuikit: https://github.com/danielsaidi/SwiftUIKit
---

While SwiftUI buttons can be given a `.bordered` button style to make them apply a nice, round border, there is currently (to my knowledge) no way to group several buttons together in a bordered group. Let's look at a way to achieve this.

As an example, consider this rich text toolbar, where the bottom row has two buttons to affect the text indentation level, as well as a segmented picker to change text alignment:

![Toolbar with indentation buttons]({{page.assets}}toolbar-buttons.jpg)

Both indentation buttons use a `.bordered` button style, which makes the height a bit smaller than the picker. I think it would be nice if the buttons and the picker aligned well together, and also think it would be nice to group the two buttons together, since they affect the same text property.

Unfortunately, there is no (to my knowledge) way to apply a border style to a group of buttons, besides doing it yourself. I however don't want to create a custom group style that mimics the segmented picker, since even a small difference will be noticable and I'd have to make sure that the style plays well with all future iOS version.

To fix this, I realized that I could use a segmented picker as a foundation, and overlay it with my group of buttons, then apply a button style to each button to make it look like a segmented picker button:

```swift
public struct BorderedButtonGroup<Content: View>: View {

    public init(
        segmented: Bool = false,
        @ViewBuilder content: @escaping () -> Content
    ) {
        self.segmented = segmented
        self.content = content
    }

    private let segmented: Bool
    private let content: () -> Content

    public var body: some View {
        picker.overlay(contentStack)
    }
}

private extension BorderedButtonGroup {

    var picker: some View {
        Picker("", selection: .constant(-1)) {
            if segmented {
                content().hidden()
            } else {
                Text("")
            }
        }
        .pickerStyle(.segmented)
        .allowsHitTesting(false)
    }

    var contentStack: some View {
        HStack(spacing: 0) {
            Group {
                content()
            }
            .frame(maxWidth: .infinity, maxHeight: .infinity)
        }
    }
}
```

With this, we can now create a button group in a very easy way:

```swift
BorderedButtonGroup {
    Button("1") {}
    Button("2") {}
}
```

Since the segmented picker is greedy, it will take up as much horizontal space as it can. Since the group's button style applies a max width, the buttons within the group will share the available width. If you don't want the group to be greedy, you can just add a `frame(width:)` to it.

We can now group the indentation buttons together in a group that aligns with the segmented picker:

![Toolbar with an indentation button group]({{page.assets}}toolbar-buttongroup.jpg)

If we want the group to be segmented, we can just add a `segmented: true` to the initializer:

![Toolbar with a segmented indentation button group]({{page.assets}}toolbar-buttongroup-segmented.jpg)

If we want to take this even further, we can group the style buttons on the second row as well:

![Toolbar with a segmented style toggle group]({{page.assets}}toolbar-stylegroup-segmented.jpg)

Although the rightmost stepper controls are a bit taller, I think that these groups much clearer indicate which buttons that belong together.


## Conclusion

This post shows a way to create a button group, using an underlying segmented picker. It works well, but it would be amazing if Apple would add this as a native feature to SwiftUI. Fingers crossed for WWDC '23! 

I have added `BorderedButtonGroup` to my [SwiftUIKit]({{page.swiftuikit}}) open-source library. Feel free to try it out and let me know what you think.