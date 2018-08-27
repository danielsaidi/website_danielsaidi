---
title:  UITextView Placeholder Support
date:   2017-11-01 00:01:00 +0100
tags:	ios swift
---


In an app of mine, I want to add placeholder support to `UITextView`. Since this
is not built-in for `UITextView`, I created three extensions to help me out:

```swift
public extension UITextView {
    
    public func setupPlaceholder(
        text: String, 
        color: UIColor = .lightGray) {
        guard self.text.isEmpty else { return }
        self.text = text
        self.textColor = color
    }
    
    public func setupPlaceholderAfterEditing(
        text: String, 
        color: UIColor = .lightGray) {
        setupPlaceholder(text: text, color: color)
    }
    
    public func setupPlaceholderBeforeEditing(
        textColor: UIColor = .black, 
        placeholderColor: UIColor = .lightGray) {
        guard self.textColor == placeholderColor else { return }
        self.text = ""
        self.textColor = textColor
    }
}
```

As you can see, the placeholder text and color is fully customizable. However, I
use light grey placeholder text and black text by default.

Whenever I create a text view instance, I run `setupPlaceholder(...)` to setup a
default placeholder text. This operation will abort if the text view already has
text content.

Whenever editing begins, I call `setupPlaceholderBeforeEditing(...)` to remove a
placeholder text and prepare the text view for editing. To determine if the view
currently displays the placeholder, we compare the text and placeholder colors.

Whenever editing finally ends, I call `setupPlaceholderAfterEditing` to re-apply
the placeholder if the user hasn't entered any text.

You can grab the code from my [iExtra library](https://github.com/danielsaidi/iExtra).