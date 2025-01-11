---
title:  Dismissing a multiline textfield with the return key in SwiftUI
date:   2023-09-15 06:00:00 +0000
tags:   swiftui

redirect_from: /blog/2023/09/15/how-to-dismiss-a-multiline-swiftui-textfield-by-pressing-the-return-key

assets: /assets/blog/23/0915/
image:  /assets/blog/23/0915.jpg
image-show: 0

tweet:  https://x.com/danielsaidi/status/1702632945612100069?s=20
toot:   https://mastodon.social/@danielsaidi/111068728937234328
---

While a single-line `TextField` will dismiss the keyboard when you press return, the same is not true for a multiline text field. Lets take a look at how to fix this.

{% include kankoda/data/open-source name="SwiftUIKit" %}

In SwiftUI, an `onSubmit` modifier can be applied to a text field, to trigger an action when the return key is used to submit the text field and dismiss the keyboard:

```swift
TextField("Enter text", text: $text)
    .onSubmit {
        print("Text field was submitted")
    }
```

This will however not work with a multiline text field, since it will insert a new line instead of submitting the text field:

```swift
TextField("Enter text", text: $text, axis: .vertical)
    .onSubmit {
        print("This will never be called")
    }
```


## How to solve this with native view modifiers

We can use `FocusState` and `onChange` to submit a multiline text field when pressing return:

```swift
struct MyView: View {

    @State
    var text = ""

    @FocusState
    var isFocused: Bool

    var body: some View {
        TextField("Enter text", text: $text, axis: .vertical)
            .submitLabel(.done)
            .focused($isFocused)
            .onChange(of: text) { newValue in
                guard isFocused else { return }
                guard newValue.contains("\n") else { return }
                isFocused = false
                text = newValue.replacing("\n", with: "")
            }
    }
}
```

In the code above, we apply a `focused` modifier that binds the text field focused state to a `@State` property, and an `onChange` modifier that listens for changes to the `text` property.

Pressing return will cause a new line (`\n`) to be typed into the text field. This will trigger the `onChange`, which sets `isFocused` to false, then cleans up the text and removes focus.

Since it would be confusing for the return key to say `return`, when it instead submits, we also apply a `.submitLabel(.done)` modifier to make it say "Done".


## How to create a custom view modifier

We can move this code to a `ViewModifier` to make it easy to reuse it. We can also add an additional `onSubmit` action that will be called whenever return is pressed:

```swift
struct MultilineSubmitViewModifier: ViewModifier {
    
    init(
        text: Binding<String>,
        submitLabel: SubmitLabel,
        onSubmit: @escaping () -> Void
    ) {
        self._text = text
        self.submitLabel = submitLabel
        self.onSubmit = onSubmit
    }
    
    @Binding
    private var text: String
    private let submitLabel: SubmitLabel
    private let onSubmit: () -> Void
    
    @FocusState
    private var isFocused: Bool
    
    func body(content: Content) -> some View {
        content
            .focused($isFocused)
            .submitLabel(submitLabel)
            .onChange(of: text) { newValue in
                guard isFocused else { return }
                guard newValue.contains("\n") else { return }
                isFocused = false
                text = newValue.replacingOccurrences(of: "\n", with: "")
                onSubmit()
            }
    }
}
```

We can also create a custom view extension to make the modifier even easier to apply:

```swift
public extension View {
    
    func onMultilineSubmit(
        in text: Binding<String>,
        submitLabel: SubmitLabel = .done,
        action: @escaping () -> Void
    ) -> some View {
        self.modifier(
            MultilineSubmitViewModifier(
                text: text,
                submitLabel: submitLabel,
                onSubmit: action
            )
        )
    }
}
```

The only change from `.onSubmit` is that you must pass in the text field's `text` binding. We explicitly add `Multiline` to the function name to clearly communicate the intent.

We can add second view extension that just applies the submit behavior without an action, for the cases when we just want to enable multiline submit:

```swift
public extension View {
    
    func multilineSubmit(
        for text: Binding<String>,
        submitLabel: SubmitLabel = .done
    ) -> some View {
        self.modifier(
            MultilineSubmitViewModifier(
                text: text,
                submitLabel: submitLabel,
                action: {}
            )
        )
    }
}
```

We can now easily apply these modifiers to any multiline text fields, just like this:

```swift
struct MyView: View {

    var body: some View {
        TextField("Enter text", text: $text, axis: .vertical)
            .onMultilineSubmit(in: $text) {
                print("Text field was submitted")
            }
    }
}
```

This view modifier can also be applied to a SwiftUI `TextEditor`, to submit it the same way.


## Conclusion

A view modifier like this one may already exist in SwiftUI, but I haven't found one. If you do know how to achieve this with plain SwiftUI, please share.

I've added these view modifiers to [{{project.name}}]({{project.url}}). Give it a try and let me know what you think.