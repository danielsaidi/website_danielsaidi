---
title:  "Sheeeeeeeeet 1.2"
date:   2019-01-16 21:00:00 +0100
tags:	hobby-projects swift

sheeeeeeeeet: https://github.com/danielsaidi/Sheeeeeeeeet
example: https://github.com/danielsaidi/Sheeeeeeeeet/blob/master/Readmes/Advanced-Example.md
---

[Sheeeeeeeeet]({{page.sheeeeeeeeet}}) 1.2 introduces a completely rewritten appearance engine that makes it easier than ever to style and subclass your action sheets and their items. In this post, I'll discuss some of the major changes.

To see these changes in action, please download the source code or have a look at [this example]({{page.example}}), which demonstrates how to create and customize action sheets, and also covers how you can create smarter, more self contained sheets.


## The old approach

Sheeeeeeeeet 1.1 and earlier had a custom-built appearance model, where each action sheet had an `appearance` property that contained a bunch of appearance properties, including a separate property for each item type. Sheeeeeeeeet then created a sheet-specific copy of the global appearance when an action sheet was created, then propagated that appearance down to each item every time the sheet was refreshed.

This approach worked fairly well, but caused a bunch of problems. For instance, user created item types could not have custom appearances injected into the model, since the `ActionSheetAppearance` class did not have any custom fields. I could have solved this with a key-value store for each item appearance, but the entire model never felt quite right.

Another problem was that the approach was too complex. The copy mechanisms were pretty complicated and hard to customize for each item type, and applying the appearance was clunky and error-prone.


## Deprecating the old approach

In Sheeeeeeeeet 1.2, I therefore decided to fix this and rebuilt the appearance engine from scratch. I therefore begun deprecating the old model and begun building a new engine that was based on the iOS appearance proxy model, which is a solid iOS concept.

The old model will be deprecated until 1.4, which means that it will be around for roughly 2-3 months before I completely remove it. During this time, Sheeeeeeeeet will be backwards compatible and apply the old appearance to the new approach. However, developers will see deprecation warnings if they touch the deprecated parts.


## The new approach

In Sheeeeeeeeet 1.2, most appearance customizations are done through appearance proxy properties, instead of custom properties like before. Every built-in item type now has a corresponding cell type that can be used for this. Furthermore, the views that make up the action sheet now have specific classes as well, which means that the background view, for instance, is no longer a `UIView`, but rather an `ActionSheetBackgroundView`. This makes it very easy to style even these parts.

With this new model in place, Sheeeeeeeeet now lets you customize the appearances of action sheets and their views and items with a lot less effort. You can change fonts, colors and images as well as item heights and even more stuff. Subclassing works out of the box, thanks to the iOS appearance proxy model, so hopefully this will make it a lot easier to create your own custom item types from now on.

In short, Sheeeeeeeeet now lets you apply appearance customizations in four ways. The `ActionSheet` class has apperance properties for the edge insets, section spacing etc. while view classes lets you style the background view, the table views etc. Item heights are customized for each item type, while item appearances are customized for each item type's corresponding cell class.


## Important

Before we proceed, let me just emphasize that it's important that you setup the global action sheet appearances in a structured way, e.g. as your app starts. If you change the global appearance many times, for instance every time your app presents an action sheet, your action sheets looks may differ, which is inconsistent.


## Action sheet apperance

The `ActionSheet` class lets you customize insets and spacings, and has three instance properties that you can modify:

* `minimumContentInsets: UIEdgeInsets` (the minimum screen edge margins)
* `preferredPopoverWidth: CGFloat` (the popover width, when presented on iPads)
* `sectionMargins: CGFloat` (the distance between the header, items and buttons)

Since these properties apply to each action sheet instance, you can't change the
default values for all action sheets in your app. If you want all sheets to have
a different default value, just subclass `ActionSheet` and set a custom value.


## View class appearances

The `ActionSheet` class has many subviews that inherit these Sheeeeeeeeet-specific classes:

* `ActionSheetBackgroundView`
* `ActionSheetTableView`
    * `ActionSheetButtonTableView`
    * `ActionSheetItemTableView` 
* `ActionSheetHeaderView`

To modify the appearance of these views, just use their appearance proxies, as such:

```swift
ActionSheetBackgroundView.appearance().backgroundColor = .purple
ActionSheetTableView.appearance().cornerRadius = 15
ActionSheetButtonTableView.appearance().cornerRadius = 20 // Otherwise 15
```

To modify the appearance of these views when they are contained in a specific action sheet, just use the iOS appearance proxy api as such:

```swift
ActionSheetHeaderView.appearance(whenContainedInInstancesOf: [MyCustomActionSheet.self]).backgroundColor = .blue
```


## Action sheet item appearances

To modify the appearance of specific items, just modify the appearance proxy of their corresponding cell, for instance:

```swift
ActionSheetItemCell.appearance().titleColor = .red
```

The appearance properties are inherited down the inheritance chain, much like in css, so changing `ActionSheetItemCell.appearance().titleColor` affects the title color of all action sheet items and their subclasses.

The various items have the following available appearance properties:

* `ActionSheetItem`
    * `titleColor`
    * `titleFont`
    * `subtitleColor`
    * `subtitleFont`
* `ActionSheetLinkItemCell`
    * `linkIcon`
* `ActionSheetSelectItemCell`
    * `selectedIcon`
    * `selectedIconColor`
    * `selectedSubtitleColor`
    * `selectedTitleColor`
    * `selectedTintColor`
    * `unselectedIcon`
    * `unselectedIconColor`
* `ActionSheetMultiSelectToggleItem`
    * `deselectAllImage`
    * `deselectAllSubtitleColor`
    * `deselectAllTitleColor`
    * `selectAllImage`
    * `selectAllSubtitleColor`
    * `selectAllTitleColor`

If you inherit an item type, you get access to all appearance properties and the default styling.


## Action sheet item heights

Finally, let's see how to customize item heights with the new appearance approach.

The default action sheet item height is `50` points, but you can customize this for every item type like this:

```swift
ActionSheetItem.height = 60
ActionSheetSectionMargin.height = 30
ActionSheetSectionTitle.height = 30
```

The reason why this property is not handled like the appearance proxy properties above, is that the item must know about its height before it has created a cell.


## Conclusion

The new appearance engine has been really fun to write, and will hopefully make it easier to work with and to customize action sheets. If you are a Sheeeeeeeeet user, I would love to hear your thoughts!
