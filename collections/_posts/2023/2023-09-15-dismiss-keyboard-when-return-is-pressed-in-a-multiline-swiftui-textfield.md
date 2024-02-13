---
title:  Dismiss keyboard when return is pressed in a multiline SwiftUI TextField
date:   2023-09-15 06:00:00 +0000
tags:   swiftui

icon:   swiftui

tweet:  https://x.com/danielsaidi/status/1702632945612100069?s=20
toot:   https://mastodon.social/@danielsaidi/111068728937234328
---

While a single line `TextField` will automatically dismiss the keyboard when you press return, the same is not true for multiline text fields. Lets take a look at how to fix this.

{% include kankoda/data/open-source.html name="SwiftUIKit" %}

In SwiftUI, an `onSubmit` view modifier can be applied to a text field. The submit action will be performed when the return key is pressed, and will also dismiss the keyboard:

```swift
TextField("Enter text", text: $text)
    .onSubmit {
        print("Text field was submitted")
    }
```

The `onSubmit` view modifier will however not have any effect on multiline text fields, since these views will insert new lines instead of submitting the text field:

```swift
TextField("Enter text", text: $text, axis: .vertical)
    .onSubmit {
        print("This will never be called")
    }
```

If another multiline-supporting submit modifier that I don't know about exists, please share. Until then, I made it work with a `FocusState` and an `onChange` modifier:

```swift
struct MyView: View {

    @State
    var text = ""

    @FocusState
    var isFocused: Bool

    var body: some View {
        TextField("Enter text", text: $text, axis: .vertical)
            .focused($isFocused)
            .onChange(of: text) { newValue in
                guard isNotesFocused else { return }
                guard newValue.contains("\n") else { return }
                isFocused = false
                text = newValue.replacing("\n", with: "")
            }
    }
}
```

This code applies a `focused` modifier to the text field and an `onChange` modifier that listens for changes to `text`. If a new line is typed, we clean up the text and remove focus.

The return key still says `return`, which may be confusing since it implies that you can tap return to add new lines. You can add a `submitLabel(.done)` modifier to make it say "Done".

We can move this code to a `ViewModifier` to make it easy to reuse this functionality. We can also add an additional `onSubmit` action that will be called whenever return is pressed:

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

We can also add a view extension to make this view modifier even easier to apply:

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

The only change from `.onSubmit` is that you must pass in the text field's `text` binding.

We can add another extension that just applies the submit behavior without an action, to get some nice naming for the cases when we just want the keyboard dismissal:

```swift
public extension View {
    
    func multilineSubmitEnabled(
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


## Conclusion

The view modifier in this blog post may already be in SwiftUI anywhere, but if so, I haven't found it yet. If you know how to achieve this with plain SwiftUI, please share.

I have added these view modifiers to my [{{project.name}}]({{project.url}}) library. Feel free to give it a try and let me know what you think.