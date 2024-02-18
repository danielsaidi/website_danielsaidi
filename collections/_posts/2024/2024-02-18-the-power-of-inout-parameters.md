---
title:  The power of inout parameters
date:   2024-02-18 06:00:00 +0000
tags:   swift

image:  /assets/blog/2024/240218/title.jpg
image-show: 0

tweet:  https://x.com/danielsaidi/status/1759312051640414643?s=20
toot:   https://mastodon.social/@danielsaidi/111954340675664871
---

{% include kankoda/data/open-source.html name="RichTextKit" %}
In Swift, `inout` parameters can reduce code duplication and the amount of code needed to perform certain tasks. Let's take a look.


## Background

Swift function parameters are constant, which means that you can't modify their value from within the function. You can change the properties of a reference type or make a local copy of a value type within the function, but can't change the parameter itself.

Marking a parameter as `inout` lets you pass in a value *reference*, which lets you change its value from within a function. This is not common, but can be used in certain situations.


## Example 

As example, let's take my open-source [{{project.name}}]({{project.url}}), which has a SwiftUI `RichTextEditor` that wraps a UIKit/AppKit `RichTextView`, uses a `RichTextContext` to provide observable state, and a `RichTextCoordinator` to sync changes between the view and the context.

SwiftUI can use the context to drive changes. The coordinator observes the context and syncs all changes to the text view, and also sets itself up as text view delegate and syncs text view changes to the context.


## Code without inout

The current coordinator code for syncing text view changes to the context looks like this:

```swift
func syncContextWithTextViewAfterDelay() {
    let styles = textView.richTextStyles

    let string = textView.attributedString
    if context.attributedString != string {
        context.attributedString = string
    }

    let range = textView.selectedRange
    if context.selectedRange != range {
        context.selectedRange = range
    }

    let canRedo = textView.undoManager?.canRedo ?? false
    if context.canRedoLatestChange != canRedo {
        context.canRedoLatestChange = canRedo
    }

    ... and so on
}
```

There are many problems with the code above. Besides being tedious to read and write, it performs a similar equality check for each property. It also refers to the local variable and a certain context property multiple times, which can lead to bugs. 

For instance, say that we copy and paste a code block to handle another property. We then have to change both properties in two places to avoid checking or setting the wrong value.

We can improve this by introducing an `inout`-based function.


## Code with inout

By using an `inout`-based function, we can move the equality check and property setter to a single place, that is able to write to the parameter we pass in. The code is very basic:

```swift
func sync<T: Equatable>(_ prop: inout T, with value: T) {
    if prop == value { return }
    prop = value
}
```

With this function in place, we can drastically reduce the amount of code from above:

```swift
func syncContextWithTextViewAfterDelay() {
    let font = textView.richTextFont ?? .standardRichTextFont
    sync(&context.attributedString, with: textView.attributedString)
    sync(&context.selectedRange, with: textView.selectedRange)
    sync(&context.canCopy, with: textView.hasSelectedRange)
    sync(&context.canRedoLatestChange, with: textView.undoManager?.canRedo ?? false)
    sync(&context.canUndoLatestChange, with: textView.undoManager?.canUndo ?? false)
    sync(&context.fontName, with: font.fontName)
    sync(&context.fontSize, with: font.pointSize)
    ... and so on
}
```

Every operation is now a single line, which makes the code it cleaner and less error-prone, since we're only using the same context property and text view state once per operation.

As you can see, we must use `&` when calling the `inout` function, since we're are actually providing a *refrence* to a value, not the value itself.