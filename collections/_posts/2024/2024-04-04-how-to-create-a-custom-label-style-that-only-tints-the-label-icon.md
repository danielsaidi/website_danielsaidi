---
title:  How to create a custom label style that only tints the icon
date:   2024-04-04 04:00:00 +0000
tags:   swift swiftui

assets: /assets/blog/2024/240404/
image:  /assets/blog/2024/240404/title.jpg

tweet:  https://x.com/danielsaidi/status/1775887952947917178
toot:   https://mastodon.social/@danielsaidi/112213337335862484
---

In this post, we'll take a look at how easy it is to create a custom SwiftUI `LabelStyle` that only tints the icon of a `Label`, while leaving the text element unchanged.


## Background

In my apps, I like to have basic, reusable views, to get a consistent UI all across my apps.

For instance, this `AddLabel` wraps a regular `Label` and adds a plus icon next to the text:

```swift
public struct AddLabel: View {
    
    public init(
        _ title: LocalizedStringResource
    ) {
        self.title = title
    }
    
    private let title: LocalizedStringResource
    
    public var body: some View {
        Label(
            title: { Text(title) },
            icon: {
                Image.add
            }
        )
    }
}
```

This label is then used by an `AddButton`, which just wraps the label and applies an action:

```swift
public struct AddButton: View {
    
    public init(
        _ title: LocalizedStringResource,
        action: @escaping () -> Void
    ) {
        self.title = title
        self.action = action
    }
    
    private let title: LocalizedStringResource
    private let action: () -> Void
    
    public var body: some View {
        Button(action: action) {
            AddLabel(title)
        }
    }
}
```
This button can be used in lists, menus, etc. which makes it easy to reuse the same type of view across the app.

However, I always want the plus to be in a filled, green circle when it's in a `List`, which is why I have yet another view:

```swift
public struct ListAddButton: View {
    
    public init(
        _ title: LocalizedStringResource,
        action: @escaping () -> Void
    ) {
        self.title = title
        self.action = action
    }
    
    private let title: LocalizedStringResource
    private let action: () -> Void
    
    public var body: some View {
        AddButton(title, action: action)
            .buttonStyle(.plain)
            .symbolVariant(.circle.fill)
            .foregroundStyle(.green)
    }
}
```

However, if I apply a foreground color like this, both the icon and the text become green:

![A screenshot of an all-green label]({{page.assets}}foreground.jpg)

We could solve this by adding an `iconColor` to the `AddLabel`, but that would require us to also add it to the `AddButton`. For deep nestings, this is painful and doesn't scale.

A better way is to create a custom `LabelStyle`. Let's take a look at how easy this is to do.


## How to create a custom label style

We can easily just tint the icon of a `Label` by creating a custom `LabelStyle`, like this one:

```swift
public struct IconTintLabelStyle: LabelStyle {
    
    public init(_ color: Color) {
        self.color = color
    }
    
    private let color: Color
    
    public func makeBody(
        configuration: Configuration
    ) -> some View {
        Label(
            title: { configuration.title },
            icon: { configuration.icon.foregroundStyle(color) }
        )
    }
}
```

If we replace `.foregroundStyle(.green)` with `.labelStyle(IconTintLabelStyle(.green))` in the example above, you can now see that only the icon is tinted:

![A screenshot of using the new style]({{page.assets}}style.jpg)

Writing `IconTintLabelStyle(...)` is however a mouthful, so let's add an `iconTint` alias for this style to make it easier to use:

```swift
public extension LabelStyle where Self == IconTintLabelStyle {
    
    static func iconTint(
        _ color: Color
    ) -> Self {
        .init(color)
    }
}
```

We can now replace `IconTintLabelStyle(.green)` with `.iconTint(.green)` in the example above, to make the code cleaner:

![A screenshot of using the new style alias]({{page.assets}}stylealias.jpg)

As you can see, I also add a custom `.list` button style that applies a `.plain` style without making only the text tappable. Just let me know if you'd like to see this described too.