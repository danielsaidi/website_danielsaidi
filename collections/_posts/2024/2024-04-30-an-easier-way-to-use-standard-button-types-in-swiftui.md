---
title:  An easier way to use standard buttons types in SwiftUI
date:   2024-04-30 04:00:00 +0000
tags:   swift swiftui

assets: /assets/blog/24/0430/
image:  /assets/blog/24/0430.jpg
image-show: 0

redirect_from: /blog/2024/04/30/an-easier-way-to-create-standard-button-types-in-swiftui

tweet:  https://x.com/danielsaidi/status/1785295019705835827
toot:   https://mastodon.social/@danielsaidi/112360327019668316
---

In this post, I'll show how to use a simple enum to make it easier to reuse standard button types (add, delete, edit, done, etc.), with support for localization.

{% include kankoda/data/open-source.html name="SwiftUIKit" %}


## Background

I always try to streamline my workflow. This is why I often move things into Swift packages, which lets me reduce the cognitive load and focus on project-specific things.

Another reason for moving things into packages is to reduce the tedious repetition of doing the same thing in project after project.

For instance, I'm tired of having to re-create the standard button types in every project and having to add the localized values to every string catalog in every app:

```swift
Button("Button.OK") { ... }
``` 

I therefore decided to set up a cleaner way of working with standard button types, where I have all assets and localizations handled in one central place.


## Solution

I started with setting up an enum that I can use to define all standard button types:

```swift
public extension Button {
    
    enum StandardType: String, CaseIterable, Identifiable {
        case add, addFavorite,
             cancel, call, copy,
             delete, deselect, done, 
             edit, email,
             ok, 
             paste,
             removeFavorite, 
             select, share
    }
}
```

I then added an extension to provide the required properties for this type:

```swift
public extension Button.StandardType {
    
    var id: String{ rawValue }
    
    var image: Image {
        .symbol(imageName)
    }
    
    var imageName: String {
        switch self {
        case .add: "plus"
        case .addFavorite: "star.circle"
        case .cancel: "xmark"
        case .call: "phone"
        case .copy: "doc.on.doc"
        case .delete: "trash"
        case .deselect: "checkmark.circle.fill"
        case .done: "checkmark"
        case .edit: "pencil"
        case .email: "envelope"
        case .ok: "checkmark"
        case .paste: "clipboard"
        case .removeFavorite: "star.circle.fill"
        case .select: "checkmark.circle"
        case .share: "square.and.arrow.up"
        }
    }
    
    var role: ButtonRole? {
        switch self {
        case .cancel: .cancel
        case .delete: .destructive
        default: nil
        }
    }
    
    var title: LocalizedStringKey {
        switch self {
        case .add: "Button.Add"
        case .addFavorite: "Button.AddFavorite"
        case .addToFavorites: "Button.AddToFavorites"
        case .call: "Button.Call"
        case .cancel: "Button.Cancel"
        case .copy: "Button.Copy"
        case .deselect: "Button.Deselect"
        case .edit: "Button.Edit"
        case .email: "Button.Email"
        case .delete: "Button.Delete"
        case .done: "Button.Done"
        case .ok: "Button.OK"
        case .paste: "Button.Paste"
        case .removeFavorite: "Button.RemoveFavorite"
        case .removeFromFavorites: "Button.RemoveFromFavorites"
        case .select: "Button.Select"
        case .share: "Button.Share"
        }
    }
}
```

I will probably refactor this to resolve to a type that defines all these properties, instead of having all these redundant switches.

Since `title` is a `LocalizedStringKey`, and I use Xcode 15's String Catalog feature, every new button title I add will automatically be added to the localized string file.

I then added a `Button` convenience initializer that takes a `Button.StandardType` value, that overrides both the title and the icon:

```swift
public extension Button {
    
    init(
        _ type: StandardType,
        _ title: LocalizedStringKey? = nil,
        _ icon: Image? = nil,
        bundle: Bundle? = nil,
        action: @escaping () -> Void
    ) where Label == SwiftUI.Label<Text, Image?> {
        self.init(role: type.role, action: action) {
            Label(
                title: { Text(title ?? type.title, bundle: title == nil ? .module : bundle) },
                icon: { icon ?? type.image }
            )
        }
    }
}
```

The initializer will use the `.module` bundle if you don't provide a custom title, to ensure that the button uses the localized button titles that are provided by the package.

You can now create standard buttons very easily, by just providing a type and an action:

```swift
Button(.add) { print("Tapped") }
Button(.delete) { print("Tapped") }
Button(.edit) { print("Tapped") }
```

If you want to provide a custom title or icon, you can do so just as easily:

```swift
Button(.add, "Button.AddNewItem") { print("Tapped") }
Button(.delete, Image(systemName: "trash.circle")) { print("Tapped") }
```

Since the title is a `LocalizedStringKey`, custom title keys will automatically be added to the string catalog that you've defined in the bundle.


## Future work

There are some things to consider with this approach, since initializer approach means that we can't apply use view modifiers within it.

Or rather, we *can* apply modifiers to the label, text or image, but that requires changes the generic constraint, in a way that I have yet to figure out and would be nice to have fixed.

First, SwiftUI will not tint `destructive` button icons in a `List` in Xcode 15.4. This works in 15.2, so I wonder if it's a simulator or SwiftUI bug. It also works in toolbars and the navbar. 

For now, fix it by applying a `foregroundStyle` to the button, but this shouldn't be needed.

Second, since each type defines an icon, both an icon and a title are used by default. Use `.labelStyle(.titleOnly)` and `.labelStyle(.iconOnly)` to adjust the label if needed.

I would have loved to have a label display config in the `Button` initializer, but since I don't know how to describe the generic constraint, I currently use the label style approach.


## Conclusion

If you want to try this approach, I've added it to my [{{project.name}}]({{project.url}}) open-source project. I'd love to hear what you think of it.