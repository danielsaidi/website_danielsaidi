---
title:  Linking DocC to a type with the same name as the package
date:   2022-05-02 07:00:00 +0100
tags:   quick-tip swift spm docc

icon:   swift
assets: /assets/blog/2022/2022-05-02/

lib:    https://github.com/danielsaidi/BottomSheet
---

In this post, let's take a quick look at how to link DocC to a type that has the same name as the Swift package that it belongs to.

In DocC, packages have precedence over types, which means that if you want to link to a type with the same name as the package, DocC will link to the package instead of the type.

To explicitly link to the type, you can add the package name before the type name, separated with a forward slash.

That means that for my [BottomSheet]({{page.lib}}), which has a view called `BottomSheet`, this way to link to it from the main page's topics section doesn't work:

```markdown
## Topics

### Views

- ``BottomSheet``
- ``BottomSheetHandle``

### Styles

- ``BottomSheetStyle``
```

This will give you a warning that that says the following:

```
Linking to 'BottomSheet' from a Topics group in 'doc://BottomSheet/documentation/BottomSheet' isn't allowed.
```

If you have a look at the generated information, `BottomSheet` will show the package description below the type name, and tapping it does nothing:

To fix this, just prefix the failing line `BottomSheet/`:

```markdown
## Topics

### Views

- ``BottomSheet/BottomSheet``
- ``BottomSheetHandle``

### Styles

- ``BottomSheetStyle``
```

This will make DocC link correctly to the type instead of the package.