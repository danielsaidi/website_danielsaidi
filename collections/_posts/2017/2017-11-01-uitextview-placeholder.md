---
title: Add placeholder support to UITextView
date:  2017-11-01 00:01:00 +0100
tags:  swift uikit
icon:  swift
---

This post will show you how to apply the `UITextField` placeholder behavior to a
`UITextView`, which natively lacks this support.

I basically solved this by adding two extension functions to `UITextView`:

```swift
public extension UITextView {
    
    public func setupPlaceholder(text: String, textColor: UIColor) {
        guard self.text.isEmpty else { return }
        self.text = text
        self.textColor = color
    }
    
    public func setupPlaceholderBeforeEditing(
        textColor: UIColor, 
        placeholderColor: UIColor) {
        guard self.textColor == placeholderColor else { return }
        self.text = ""
        self.textColor = textColor
    }
}
```

As you can see, the placeholder text and color is customizable. However, I add
app-specific extensions that call the functions with app-specific colors, so I
don't have to pass around colors everywhere.

Whenever I create a text view, I run `setupPlaceholder(...)` to setup a standard
placeholder text. This operation will abort if the view already has text content. 

When editing begins, I call `setupPlaceholderBeforeEditing(...)` to ensure that
the placeholder text is correctly removed and prepare the view for editing. To
determine if the view currently displays the placeholder, we compare the text
and placeholder colors. However, this requires that we use different text colors
for text and placeholder text.

Whenever editing ends, I call `setupPlaceholder(...)` once again to re-apply the
placeholder, if the user hasn't entered any text.