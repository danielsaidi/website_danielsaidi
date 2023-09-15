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

In SwiftUI, the `onSubmit` view modifier can be applied to single line text fields, to perform actions when the return key is pressed:

```swift
TextField("Enter text", text: $text)
    .onSubmit {
        print("Text field was submitted")
    }
```

This modifier will however not have any effect on multiline text fields:

```swift
TextField("Enter text", text: $text, axis: .vertical)
    .onSubmit {
        print("This will never be called")
    }
```

The reason for this is that multiline text fields will insert new lines into the text when the return key is pressed, instead of "submitting" the text field and dismissing the keyboard.

Perhaps a modifier exists that I don't know about, so please share if you know one. Until then, I made multiline dismissal work with a `FocusState` and an `onChange` modifier:

```swift
struct MyView: View {

    @State
    var text = ""

    @FocusState
    var isFocused: Bool


    var body: some View {
        TextField("Enter text", text: $text, axis: .vertical)
            .focused($isFocused)
            .onChange(of: item.notes) { newValue in
                guard isNotesFocused else { return }
                guard newValue.contains("\n", caseSensitive: false) else { return }
                isFocused = false
                item.notes = newValue.replacing("\n", with: "")
            }
    }
}
```

The code above applies a `focused` modifier to the text field and an `onChange` modifier that listens for changes to `text`. As soon as a new line is typed, we clean up the text and removes focus from the view.

The return key still says `return`, which may be a confusing, since it may imply that you can press return to insert new lines. You can add a `submitLabel(.done)` modifier to make it say "Done" instead.

We can move this code to a view modifier to make it easy to reuse this functionality. We can also add an `onSubmit` function to call whenever return is pressed:

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

We can also add a view extension to make this even easier to apply:

```swift
public extension View {
    
    func onMultilineSubmit(
        in text: Binding<String>,
        submitLabel: SubmitLabel = .done,
        action: @escaping () -> Void = {}
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

The only main difference between `onSubmit` and `onMultilineSubmit`, is that `onMultilineSubmit` requires the text binding used by the text field.

We can add another extension that just applies the resign behavior without an `onSubmit` action:

```swift
func multilineSubmit(
    for text: Binding<String>,
    submitLabel: SubmitLabel = .done
) -> some View {
    self.modifier(
        MultilineSubmitViewModifier(
            text: text,
            submitLabel: submitLabel,
            onSubmit: {}
        )
    )
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

This may already be in SwiftUI anywhere, but I haven't managed to find it. If you know how to achieve this with plain SwiftUI, please share it here.

I have added the view modifier and extensions to my [SwiftUIKit]({{project.url}}) library. Feel free to try it out and let me know what you think.