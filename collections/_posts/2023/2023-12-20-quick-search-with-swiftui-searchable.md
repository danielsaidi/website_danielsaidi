---
title:  Implementing Quick Search with SwiftUI Searchable
date:   2023-12-20 06:00:00 +0000
tags:   swiftui macos ios

image:  /assets/blog/2023/231220/title.jpg

toot:   https://mastodon.social/@danielsaidi/111612228156955705
tweet:  https://x.com/danielsaidi/status/1737416857823461669?s=20
---

In this post, we’ll take a look at how to search with the `.searchable` API, by just typing on the keyboard without first having to tap/click on the text field.

{% include kankoda/data/open-source.html name="QuickSearch" %}

The `.searchable` view modifier is a convenient way to add a search text field to a screen. However, unlike regular text fields, it doesn't let you use `FocusState` to control its focus.

This means that users will have to tap/click on the search field in order to start searching, while many native macOS apps let you just type to search or filter items.

Implementing quick search in SwiftUI is tricky, but I think I made it work with two different approaches on macOS and iOS. Let's take a look.


## onKeyPress to the rescue

I got quick search to work with the `.onKeyPress` modifier, which means that this approach will only work in iOS 17 and macOS 14.

Unfortunately, we can't just add `.onKeyPress` to a view and be done. macOS only detects key presses on focusable views, while `.focusable()` has no effect on some views in iOS.

This forced me to come up with a solution where macOS will apply `.focusable()`  and iOS instead injects a hidden text field that handles the focus.


## Creating a quick search view modifier

Let's first create a `QuickSearchViewModifier` view modifier that applies quick search to any view. It will integrate with a `.searchable` text field, so it needs the same text binding:

```swift
struct QuickSearchViewModifier: ViewModifier {
    
    init(text: Binding<String>) {
        self._text = text
    }
    
    @Binding
    private var text: String
    
    @FocusState
    private var isFocused
    
    func body(content: Content) -> some View {
        #if os(iOS)
        content
            .background(
                extend {
                    TextField("", text: $text)
                        .opacity(0.01)
                        .offset(x: -10_000, y: -10_000)
                }
            )
        #else
        extend {
            content
        }
        #endif
    }
}
```

The iOS text field hack is ugly, but we need it gone from the view hierarchy. Any ideas for a better approach are more than welcome.

This `extend` function will let both approaches share a common foundation:

```swift
private extension QuickSearchViewModifier {

    func extend<Content: View>(
        content: @escaping () -> Content
    ) -> some View {
        content()
            .focused($isFocused)
            .focusEffectDisabled()
            .onKeyPress(action: handleKeyPress)
            .onChange(of: text) {
                guard $1.isEmpty else { return }
                isFocused = true
            }
            .onAppear { isFocused = true }
    }
}
```

It applies a `focused` modifier with an internal state, then disables the focus effect since it's not meant to be an accessibility feature. Re-enable the effect where it's needed!

The function then applies an `.onKeyPress` modifier to handle key presses (more on it soon) and focuses on the view when it appears and when the text binding becomes empty.

The empty state handling is required since focus would otherwise be lost whenever a user taps or clicks the clear button in the search text field.


## Handling key presses

We will handle key presses by appending characters to the text field, delete if backspace is pressed and clear the search field if escape is pressed.

To make this work, we need to be able to identify the `backspace`, `space` and `tab` keys:

```swift
extension String {
    
    static let backspace = String("\u{7f}")
    static let space = String(" ")
    static let tab = String("\t")
}
```

We can now implement `handleKeyPress`. It requires special handling on macOS and iOS:

```swift
private extension QuickSearchViewModifier {

    func handleKeyPress(
        _ press: KeyPress
    ) -> KeyPress.Result {
        guard press.modifiers.isEmpty else { return .ignored }
        let chars = press.characters
        switch press.key {
        case .delete: return handleKeyPressWithBackspace()
        case .escape: return handleKeyPressWithReset()
        default: break
        }
        switch chars {
        case .backspace: return handleKeyPressWithBackspace()
        case .space: return handleKeyPressByAppending(.space)
        case .tab: return .ignored
        default: return handleKeyPressByAppending(chars)
        }
    }
    
    func handleKeyPressByAppending(
        _ char: String
    ) -> KeyPress.Result {
        performAsyncToMakeRepeatPressWork {
            text.append(char)
        }
    }
    
    func handleKeyPressWithBackspace() -> KeyPress.Result {
        if text.isEmpty { return .ignored }
        return performAsyncToMakeRepeatPressWork {
            text.removeLast()
        }
    }
    
    func handleKeyPressWithReset() -> KeyPress.Result {
        if text.isEmpty { return .ignored }
        text = ""
        return .handled
    }
}
```

I was surprised to see that backspace behaves differently on macOS & iOS. iOS requires checking if `key` is `delete`, while macOS requires checking if `char` is `\u{7f}` (backspace).

In the code above, we handle backspace in two different ways, clear the text binding when pressing escape, ignore tab and append typed characters to the text binding.

Even though `KeyPress` will trigger repeatedly if you press and hold the key, I had problems getting the repeat to work. For this to work, I had to wrap the operations in an async call:

```swift
func performAsyncToMakeRepeatPressWork(
    action: @escaping () -> Void
) -> KeyPress.Result {
    DispatchQueue.main.async(execute: action)
    return .handled
}
```

That's all the code needed to make quick search work. But before we wrap up, let's add a view extension to make it easier to use this modifier:

```swift
public extension View {
    
    func quickSearch(
        text: Binding<String>
    ) -> some View {
        self.modifier(
            QuickSearchViewModifier(text: text)
        )
    }
}
```

We can now apply `.quickSearch` next to `.searchable` to make it possible to search by just start typing on the keyboard. It works on both macOS, iOS and iPadOS.


## Disclaimer

Be aware that this is a highly experimental approach to make quick typing work in SwiftUI. Use it with caution, and only when it makes sense.

I hope that the native `.searchable` will support quick typing in the future. Until it does, you can use my [{{project.name}}]({{project.url}}) library to avoid having to add all this code to your project.