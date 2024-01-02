---
title:  Quick Search with SwiftUI Searchable
date:   2023-12-20 06:00:00 +0000
tags:   swiftui macos ios

image:  /assets/blog/2023/231220/header.jpg

toot:   https://mastodon.social/@danielsaidi/111612228156955705
tweet:  https://x.com/danielsaidi/status/1737416857823461669?s=20
---

In this post, we’ll take a look at how to search with `.searchable` by just typing on the keyboard without first having to tap/click on the text field to give it focus.

{% include kankoda/data/open-source.html name="QuickSearch" %}

![Blog header image]({{page.image}})

The `.searchable` view modifier is a convenient way to add a search text field to a screen. However, unlike regular text fields, it doesn't let you (to my knowledge) use `FocusState` to control its focus.

This means that your users will have to manually tap/click on the search field in order to start searching. 

Sure, users may can use the tab key to toggle focus state between available focus elements, but many macOS apps have screens where you can just start typing to search or filter a collection of items.

Implementing this quick search feature in SwiftUI is pretty tricky, but I think I have made it work with two quite different approaches on macOS and iOS, that share a common foundation. Let's take a look.


## onKeyPress to the rescue

I got quick search to work by using the `.onKeyPress` modifier, which means that this approach will only work in iOS 17 and macOS 14.

Unfortunately, we can't just add `.onKeyPress` to a view and be done with it. macOS will only detect key presses on focusable views, and `.focusable()` will not have an effect on any kind of view on iOS.

This forced me to come up with a solution where macOS will apply `.focusable()` to the modified view, and iOS instead injects a hidden text field that handles the focus.

Let's take a look at this as we create a view modifier to handle this functionality.


## Creating a quick search view modifier

Let's create a `QuickSearchViewModifier` view modifier that handles quick search for any view. Since it will integrate with a `.searchable` text field, it needs the same text binding:

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

The iOS text field approach is an ugly hack, but we need it gone from the view hierarchy. Any other ideas for a better approach are more than welcome.

The `extend` function is meant to let both approaches share a common foundation. This is what it does:

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

The function applies a `focused` view modifier with an internal focus state, then disables the focus effect since this is not meant to be an accessibility feature. Make sure to re-enable the effect where it's needed!

The function then applies an `.onKeyPress` view modifier to handle key presses (more on that soon). It also focuses on the view when it appears, and whenever the text binding becomes empty.

The empty state handling is required since focus would otherwise be lost whenever the user taps/clicks the clear button in the search text field.


## Handling key presses

We will handle key presses by appending characters to the text field, delete characters when backspace is pressed and clear the search field when escape is pressed.

To make this work, we need to be able to identify some keys, namely `backspace`, `space` and `tab`:

```swift
extension String {
    
    static let backspace = String("\u{7f}")
    static let space = String(" ")
    static let tab = String("\t")
}
```

We can now implement `handleKeyPress`, which requires some special handling on macOS and iOS:

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

I was surprised to see that backspace behaves differently on macOS and iOS. On iOS, you have to check if `key` is `delete`, while macOS requires that you check if `char` is `\u{7f}` (backspace).

If we look at the code above, you can see that we handle backspace in two different ways, clear the text binding when pressing escape, ignores tab and appends typed characters to the text binding.

Even though `KeyPress` will trigger repeatedly if you press and hold the key, I had problems getting the repeated behavior to work. To make it work, I had to wrap the operations in an async call:

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

That's it! We can now apply `.quickSearch` next to `.searchable` to make it possible to search by just start typing on the keyboard. It works on both macOS, iOS, and iPadOS.


## Disclaimer

I'm fully and completely aware that this is a highly experimental approach to make a common macOS feature work in SwiftUI. Use it with caution, and only when it makes sense.


## Conclusion

I really hope that this arrives as a native `.searchable` feature in future SwiftUI updates. Until it does, you can use my [{{project.name}}]({{project.url}}) library to avoid having to add all this code to your project.