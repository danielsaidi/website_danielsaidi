---
title: Adding placeholder support to UITextView
date:  2017-11-01 00:01:00 +0100
tags:  swift uikit
icon:  swift

redirect_from: /blog/2017/11/01/uitextview-placeholder
---

This post shows how to apply the `UITextField` placeholder behavior to a `UITextView`, which natively lacks this support.

I basically solved this by adding two extension functions to `UITextView`:

```swift
extension UITextView {
    
    func setupPlaceholder(text: String, textColor: UIColor) {
        guard self.text.isEmpty else { return }
        self.text = text
        self.textColor = color
    }
    
    func setupPlaceholderBeforeEditing(
        textColor: UIColor, 
        placeholderColor: UIColor) {
        guard self.textColor == placeholderColor else { return }
        self.text = ""
        self.textColor = textColor
    }
}
```

The placeholder text and color is customizable. I add app-specific extensions that call the functions with app-specific colors, so I don't have to pass around colors.

When I create a text view, I run `setupPlaceholder(...)` to set up a standard placeholder. This operation will abort if the view already has text content. 

When editing begins, I call `setupPlaceholderBeforeEditing(...)` to ensure the placeholder text is correctly removed.

To determine if a view currently displays a placeholder, we compare text and placeholder colors. However, this requires that we use different text colors for text and placeholder text.

When editing ends, I call `setupPlaceholder(...)` once again to re-apply the placeholder, if the user hasn't entered any text.