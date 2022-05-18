---
title:  Linking DocC to package named types
date:   2022-05-02 07:00:00 +0100
tags:   swift spm docc

assets: /assets/blog/2022/2022-04-27/
image:  /assets/blog/2022/2022-04-27/image.jpg
tweet:  https://twitter.com/danielsaidi/status/1521200257002094592?s=20&t=wF1kbk5Nxm27t6vxQ1OeLQ

lib:    https://github.com/danielsaidi/BottomSheet
---

In this post, let's take a quick look at how to link DocC to types that have the same name as the target they belong to.

![DocC icon]({{page.image}})

In DocC, targets have precedence over types, which means that if you link to a type that has the same name as the target, DocC will link to the target instead of the type.

That means that for my [BottomSheet]({{page.lib}}) library, which has a SwiftUI view that's also called `BottomSheet`, linking like this won't work:

```markdown
## Topics

### Views

- ``BottomSheet``
- ``BottomSheetHandle``

### Styles

- ``BottomSheetStyle``
```

This will give you the following warning:

```
Linking to 'BottomSheet' from a Topics group in 'doc://BottomSheet/documentation/BottomSheet' isn't allowed.
```

If you have a look at the generated documentation, `BottomSheet` will show the target's description instead of the type's, and tapping it does nothing.

To explicitly link to a type instead of a target, just add the target's name before the type name, separated with a forward slash:

```markdown
## Topics

### Views

- ``BottomSheet/BottomSheet``
- ``BottomSheetHandle``

### Styles

- ``BottomSheetStyle``
```

Addint this prefix will make DocC link correctly to the type instead of the target. However, since it's easy to forget and you may link quite a lot to a type with the same name as the target (since it's probably the main type), you may want to avoid naming your types after the target.