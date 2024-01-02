---
title:  SwiftUI menu gets incorrect separator line in list
date:   2023-08-25 06:00:00 +0000
tags:   swiftui

icon:   swiftui
assets: /assets/blog/2023/230825/

tweet:  https://twitter.com/danielsaidi/status/1694985225611137502?s=20
toot:   https://mastodon.social/@danielsaidi/110949234651595725#.

jimmy:  https://github.com/thekrazyjames
jimmy-tweet:  https://twitter.com/thekrazyjames/status/1694880297081327651?s=20
---

If you'ved added a `Menu` with an icon `Label` to a `List`, you may have noticed that the separator line behaves unlike other items, and starts below the icon instead of the title. Let's find a way to fix this.

{% include kankoda/data/open-source.html name="SwiftUIKit" %}

I noticed this strange behavior in an app of mine, as I moved a couple of menu options into a "Contact Us" menu, to clean things up a bit:

![A menu in a list, getting an incorrect separator line]({{page.assets}}menu.jpg){:width="350px"}

As you can see in the image above, the menu's separator line starts below the icon instead of the title. This is unlike how all other items behave by default.

If we add a `DisclosureGroup` with the same label as the menu, you can see that the separator line works for the disclosure group, but not for the menu:

![A comparison between a menu and a disclosure group, where the disclosure group gets a correct separator line]({{page.assets}}disclosuregroup.jpg){:width="350px"}

I tried adjusting the separator insets, using alignment guides etc., but nothing worked. Or, alignment guides technically worked, but only when specifying a certain width, which I don't want to do.


## The solution - using a custom menu style

After some discussions on [Twitter]({{site.twitter_url}}) and [Mastodon]({{site.mastodon_url}}), [@JimmyDev]({{page.jimmy}}) suggested something that actually works - [using a custom menu style]({{page.jimmy-tweet}}).

If we use Jimmy's code from the link above, and move the icon argument from the menu initializer to the style, the separator line now starts below the title, just like the other ones:

![A menu in a list, with a correct separator line]({{page.assets}}menu-fixed.jpg){:width="350px"}

Jimmy, what a genius! We can adjust his quick POC to support generic views as well, to make it versatile:

```swift
struct ListMenuStyle<Icon: View>: MenuStyle {
    
    init(_ icon: Icon) {
        self.icon = icon
    }
    
    private let icon: Icon
    
    func makeBody(configuration: Configuration) -> some View {
        Label {
            Menu(configuration)
        } icon: {
            icon
        }
    }
}

extension MenuStyle where Self == ListMenuStyle<Image> {
    
    static func list(systemImageName: String) -> some MenuStyle {
        ListMenuStyle(
            Image(systemName: systemImageName)
        )
    }
    
    static func list(icon: Image) -> some MenuStyle {
        ListMenuStyle(icon)
    }
}
```

Let's try it out by applying the menu style to a text-only menu, using a red circle as icon:

```swift
struct ContentView: View {
    
    var body: some View {
        List {
            button
            Menu("Test") {
                button
                button
            }
            .menuStyle(ListMenuStyle(icon))
            button
        }.buttonStyle(.plain)
    }
    
    var button: some View {
        Button {} label: { label }
    }
    
    var icon: some View {
        Circle().fill(.red)
    }
    
    var label: some View {
        Label {
            Text("Test")
        } icon: {
            icon
        }
    }
}
```

This works great! If we run this app, we can now use the menu style with custom icon views:

![A menu with a correct separator line and custom icon view]({{page.assets}}dots.jpg){:width="350px"}

I have yet to find out how to make a convenience `.list` builder for the generic view, but it should just involve finding the correct generic constraint to apply to the extension.

Important: When using this menu style, just keep in mind that you must only use a text title for the menu. If you apply a label with an icon, the entire label will be used and start at the separator line:

![An image showing how incorrect things become when using a label on the menu]({{page.assets}}label.jpg)

However, if we just stick with a text title and the menu style, things seems to work great. Thanks a bunch to [@JimmyDev]({{page.jimmy}}) for finding a workaround to this strange behavior that I think should be considered a bug.


## Conclusion

After playing with this some more, I think I will use a `DisclosureGroup` instead of a `Menu` in my apps, since a disclosure group uses an arrow to indicate that there are more actions, which I find works better.

Since I think this strange separator behavior is actually a bug that may be fixed in iOS 17, I will not add this menu style to [SwiftUIKit]({{project.url}}) until I use it in my own apps. Until then, you can just copy the code above.

If you find this topic/bug interesting and find any more information about it, please share your findings.