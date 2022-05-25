---
title:  Building a rich text editor for UIKit, AppKit and SwiftUI
date:   2019-07-01 06:00:00 +0000
tags:   swift swiftui

icon:   swiftui
assets: /assets/blog/2022/2022-07-01/

oribi:         https://www.oribi.se/en
oribi-writer:  https://oribi.se/en/apps/oribi-writer/
okeyboard:     https://oribi.se/en/apps/okeyboard/
richtextkit:   https://github.com/danielsaidi/RichTextKit
---

In this post, we'll look at how to build a rich text editor for UIKit, AppKit and SwiftUI. We'll extend native types to extend the foundation support for rich text, create new types to bridge the various platforms and make sure that we have a basic, working foundation that we can expand in future posts.


## Background

Rich text editing on Apple's platforms is pretty straightforward. Just create a `UITextView` in UIKit (iOS and tvOS) and an `NSTextView` in AppKit (macOS) and use `NSAttributedString`, and you're good to go. The text view will automatically support different fonts, styles, alignments etc. as well as images and other kind of rich content with very little extra work.

Well, you could think that it'd be that easy, but unfortunately it's not. Many basic tasks are actually pretty complicated. Adding multi-platform support to the mix makes things even worse, since UIKit and AppKit handle strings, attributes, attachments etc. differently.

Another complication is SwiftUI, where we have to find a way to embed and bridge the platform-specific views in a way that works on all platforms. We also need some way of letting SwiftUI affect the platform-specific views, and for the platform-specific views and their delegates to affect SwiftUI correctly.

All in all, this is a pretty complicated task, which is why I'm happy to announce that my client [Oribi]({{page.oribi}}) has given me permission to open-source a rich text engine that I created for them as part of building a new version of their text editor [Oribi Writer]({{page.oribi-writer}}). The result will be an open-source library called [RichTextKit]({{page.richtextkit}}), which you’ll be able to use in your own projects.


## About Oribi and Oribi Writer

[Oribi]({{page.oribi}}) is a Swedish company that develops powerful spelling aids and other types of language support for the digital and physical world. [Oribi Writer]({{page.oribi-writer}}) is their rich text editor, which features many of Oribi's amazing features, like spellcheck, word prediction, TTS, a lexicon and much more.

![OribiWriter screenshot]({{page.assets}}oribi-writer.jpg){:width="650px"}

Oribi Writer was a 10+ years old Objective-C app, when Oribi asked me to build a new version, after I helped them build their [oKeyboard]({{page.okeyboard}}) keyboard app. I built the new app from scratch as a SwiftUI multi-platform, document app. It lets you view and edit rich text, with the additional Oribi features mentioned earlier. It supports bold, italic and underline, fonts, font sizes and alignments, images, highlighting etc.

The technical challenges with implementing rich text support in Oribi Writer proved significant, especially since it had to support multiple platforms as well as SwiftUI. Considering how much time I spend on the rich text support, how little information that is available and how old most of the code you find is,  it's very generous of them to let me open-source this. Many thanks to [Oribi]({{page.oribi}})!


## Designing for multi-platform

When building a multi-platform rich text engine that also should support SwiftUI, I was careful to design the foundation as platform-agnostic as possible. Designing for the unknown is often a vain task, but in this case, I just designed it in a way that I thought would made sense for both UIKit and AppKit.

Long story short, I made a few assumptions that didn't hold true when I started developing the macOS version. For instance, I assumed that `UITextView` and `NSTextView` scrolls in the same way (spoiler alert - they don't). However, having a platform-agnostic foundation made the adjustments manageable.

One example is how we can set up the text views. We will use `UITextView` in UIKit and `NSTextView` in AppKit, but since we'll have to override some parts and add more functionality later, we can create a new `RichTextView` view for both platforms, to get a single text view type:

```swift
#if os(iOS) || os(tvOS)
import UIKit

public class RichTextView: UITextView {}
#endif
```

```swift
#if os(macOS)
import AppKit

public class RichTextView: NSTextView {}
#endif
```

As you can see, we can only implement this view for `macOS`, `iOS` and `tvOS`, since `UITextView` is not available on `watchOS`. 

Furthermore, the `UITextView` and `NSTextView` api:s differ quite a bit. For instance, to get and set the attributed string, `UITextView` has an `attributedText` property, while `NSTextView` has an `attributedString()` function to get the string and provides an optional `textStorage` to change it.

To bridge the platform differences, we can add extend the views with additional properties, to make them get the same api:s. To ensure that we actually do have the same api:s for both platforms, I prefer to add protocols that enforce this. For the rich text view, let's add a `RichTextViewRepresentable` protocol:

```swift
public protocol RichTextViewRepresentable {

    var attributedString: NSAttributedString { get }
}
```

We can now make both views implement this protocol by adding an `attributedString` to them:

```swift
#if os(iOS) || os(tvOS)
extension RichTextView: RichTextViewRepresentable {}

public extension RichTextView {
    
    var attributedString: NSAttributedString {
        get { attributedText ?? NSAttributedString(string: "") }
        set { attributedText = newValue }
    }
}
#endif
```

```swift
#if os(macOS)
extension RichTextView: RichTextViewRepresentable {}

public extension RichTextView {
    
    var attributedString: NSAttributedString {
        get { attributedString() }
        set { textStorage?.setAttributedString(newValue) }
    }
}
#endif
```

Using protocols to communicate any bridging between the two platforms is a nice way to make our code cleaner in a controlled way. The most important thing is however that both platforms get the same api:s and to remove the need for `#if` checks in our library.

To design SwiftUI for multi-platform use, we can create a `ViewRepresentable` typealias that makes platform-specific views in SwiftUI regardless of platform.

```swift
#if os(iOS) || os(tvOS)
import UIKit

typealias ViewRepresentable = UIViewRepresentable
#endif

#if os(macOS)
import AppKit

typealias ViewRepresentable = NSViewRepresentable
#endif
```

This will make the SwiftUI-specific rich text editor that we'll create next cleaner, since it can implement `ViewRepresentable` without having to care about the platform differences.


## Creating a rich text editor for SwiftUI

To use these new text views in SwiftUI, we can create a `RichTextEditor` view that wraps either of the two views depending on which platform we're on:

```swift
#if os(iOS) || os(macOS) || os(tvOS)
import SwiftUI

public struct RichTextEditor: ViewRepresentable {

    public init(text: Binding<NSAttributedString>) {
        self._attributedString = text
    }


    @Binding
    public var attributedString: NSAttributedString


    #if os(iOS) || os(tvOS)
    public let textView = RichTextView()

    public func makeUIView(context: Context) -> some UIView {
        textView.attributedString = attributedString
        return textView
    }

    public func updateUIView(_ view: UIViewType, context: Context) {}
    #endif

    #if os(macOS)
    public let scrollView = RichTextView.scrollableTextView()

    public var textView: RichTextView {
        scrollView.documentView as? RichTextView ?? RichTextView()
    }

    public func makeNSView(context: Context) -> some NSView {
        textView.attributedString = attributedString
        return scrollView
    }

    public func updateNSView(_ view: NSViewType, context: Context) {}
    #endif
}
#endif
```

To create a text editor view, we have to provide it with an `NSAttributedString` binding. This can be any custom string or content that comes from e.g. a document in a document-based app. The editor will display any rich text that we provide it with, and can be used to edit it as well.

This code shows one of my first AppKit learnings as I started developing the library for macOS. As you can see, iOS and tvOS just uses the `RichTextView` that we created earlier. I first did the same in the macOS-specific code, just to notice that the text editor then didn't scroll.

Turns out that in AppKit, you have to create a scroll view from the text view type that you intend to use, cast its `documentView` to get a text view, then use the `scrollView` to get the scrollable behavior we get by default in iOS. We'll need the text view later, so let's also add a `textView` property.

If we now create a SwiftUI test app with a text binding, as well as a `TextEditor` and a `Text` view that shows the text, you'll notice that typing in the text field will not affect the text view.

![A screenshow of how changes are not synced from the text view back to the state binding]({{page.assets}}changes-not-synced.jpg){:width="650px"}

In the image above, I have typed in the text field, but the `Text` view still shows the original text. This is because we never write back any changes in the text editor to the text binding. Let's fix that.


## Syncing changes

To listen for changes in the text views and sync them back to the text binding, we need to implement text view delegation. However, since `TextEditor` is a struct, it can't be used as the text view delegate.

We can solve this by adding a `coordinator` to the text editor and use it as the delegate. Let's create a `RichTextCoordinator` and use it to coordinate changes between SwiftUI and the underlying views.

```swift
#if os(iOS) || os(macOS) || os(tvOS)
import SwiftUI

open class RichTextCoordinator: NSObject {

    public init(
        text: Binding<NSAttributedString>,
        textView: RichTextView) {
        textView.attributedString = text.wrappedValue
        self.text = text
        self.textView = textView
        super.init()
        self.textView.delegate = self
    }

    public var text: Binding<NSAttributedString>

    public private(set) var textView: RichTextView
}
#endif
```

The coordinator is given a text binding and a `RichTextView` and will sync changes between both. We can also remove setting the `attributedString` in the editor, since the coordinator does it.

To be able to set the coordinator as the text view delegate, we first have to make it implement both the `UITextViewDelegate` and the `NSTextViewDelegate` protocol.

```swift
#if os(iOS) || os(tvOS)
import UIKit

extension RichTextCoordinator: UITextViewDelegate {}

#elseif os(macOS)
import AppKit

extension RichTextCoordinator: NSTextViewDelegate {}
#endif
```

We can then set the coordinator as the text view delegate in the coordinator's initializer:

```swift
open class RichTextCoordinator: NSObject {

    public init(
        text: Binding<NSAttributedString>,
        textView: RichTextView) {
        textView.attributedString = text.wrappedValue
        self.text = text
        self.textView = textView
        super.init()
        textView.delegate = self // <-- New line!
    }

    ...
}
```

For the coordinator to actually do anything, we need to implement a few delegate functions. Before we do, let's first add a way to sync the text binding with the text view:

```swift
private extension RichTextEditorCoordinator {

    func syncWithTextView() {
        syncTextWithTextView()
    }

    func syncTextWithTextView() {
        if text.wrappedValue == textView.attributedString { return }
        text.wrappedValue = textView.attributedString
    }
}
```

We can now setup the (so far limited) delegate handling, where we'll update the text binding when we type in the text view or changes its selection. Note that UIKit and AppKit has similar, but different api:s:

```swift
open class RichTextCoordinator: NSObject {

    ...

    #if canImport(UIKit)

    open func textViewDidChange(_ textView: UITextView) {
        syncWithTextView()
    }

    open func textViewDidChangeSelection(_ textView: UITextView) {
        syncWithTextView()
    }
    #endif

    #if canImport(AppKit)

    open func textDidChange(_ notification: Notification) {
        syncWithTextView()
    }

    open func textViewDidChangeSelection(_ notification: Notification) {
        syncWithTextView()
    }
    #endif
}
```

With this code in place, the coordinator will now sync changes back to the text binding. Still, if we run the test app, the original text still doesn't update. This is because we still haven't put the coordinator to use.

We do this by simply update the text editor to setup a `RichTextCoordinator` in `makeCoordinator`:

```swift
public struct RichTextEditor: ViewRepresentable {

    ... 

    public func makeCoordinator() -> RichTextCoordinator {
        RichTextCoordinator(
            text: text,
            textView: textView)
    }
}
```

If we now run the test app, you will see that the `Text` view now updates when we type in the text field. This is because our coordinator listens for changes in the text field and updates the text binding for us.

However, tapping the button to change the text still doesn't update the text editor. This is something we have to leave for later, since updating the text editor is quite tricky, if we want things like text position to behave correctly whenever the text changes.

Instead, let's look at how to change text style and let us toggle bold, italic and underline for the current position or selection in the text editor.


## Working with text styles

To support text styling, we have to find a way to get and set if the text is bold, italic, underlined etc. Turns out that this is not as straightforward as you may think. Bold and italic are symbolic font traits, while the underline information is a text attribute, which means that we have to handle different styles in different ways. UIKit and AppKit then adds even more differences to the mix. Think different.

To get attribute information for the attributed string, we can use `attributes(at:effectiveRange:)`. However, ranges and strings are a dangerous combo, since invalid ranges will cause the code to crash.

Therefore, let's add a `safeRange` function to `NSAtrributedString`, to shield us from invalid ranges:

```swift
public extension NSAttributedString {
    
    func safeRange(for range: NSRange) -> NSRange {
        NSRange(
            location: max(0, min(length-1, range.location)),
            length: min(range.length, max(0, length - range.location)))
    }
}
```

We can now add `NSAtrributedString` extensions that get a single or all attributes at a certain range:

```swift
public extension NSAttributedString {

    func textAttribute<Value>(_ key: Key, at range: NSRange) -> Value? {
        textAttributes(at: range)[key] as? Value
    }

    func textAttributes(at range: NSRange) -> [Key: Any] {
        if length == 0 { return [:] }
        let range = safeRange(for: range)
        return attributes(at: range.location, effectiveRange: nil)
    }
}
```

To actually change attributes for a certain range, we can create another extension. Note that we need to extend `NSMutableAttributedString` instead of `NSAttributedString`:

```swift
public extension NSMutableAttributedString {

    func setTextAttribute(_ key: Key, to newValue: Any, at range: NSRange) {
        let range = safeRange(for: range)
        guard length > 0, range.location >= 0 else { return }
        beginEditing()
        enumerateAttribute(key, in: range, options: .init()) { value, range, _ in
            removeAttribute(key, range: range)
            addAttribute(key, value: newValue, range: range)
            fixAttributes(in: range)
        }
        endEditing()
    }
}
```

Phew, that's a mouthful. Still, these extensions will now help us get and set text attributes like `.font`, `.underlineStyle` etc. and will make it easier to create a clean api. 

However, traits like `bold` and `italic` can not be get and set directly with attributes. Instead, we have to use the font to get and set these kind of traits. Going through all of this would however make the post very long and tedious, so we'll brush over some details and fast-forward to where the native types have been extended with more functionality. If you're interested in the code, please have a look at [RichTextKit]({{page.richtextkit}}).


## Changing text styles

If we were to use UIKit and AppKit, our UIKit/AppKit `TextView` and its attributed string would already be able to get and set attributes, which means that we could for instance toggle underline on and off.

In SwiftUI, however, we have to find a way to observe the current state of the text view, like we did when we synced text view changes back to the text binding. However, we then had a text binding to sync with. For information like traits, attributes etc. we need a way to manage that state in an observable way.

We also need to find a way to trigger changes in the text view from SwiftUI, like toggling bold by tapping a button. However, since views are structs, there are no references to the text view for a button to use.

We can solve this by introducing a new, observable class that we can use to keep track of the current state of the text view, and that SwiftUI can use to affect the text view. Let's call it `RichTextContext`.

```swift
public class RichTextContext: ObservableObject {

    public init() {}
}
```

Let's also adjust the `RichTextEditor` and `RichTextCoordinator` to require a rich text context:

```swift
public struct RichTextEditor: ViewRepresentable {

    public init(
        text: Binding<NSAttributedString>,
        context: RichTextContext) {
        self.text = text
        self._richTextContext = ObservedObject(wrappedValue: context)
    }

    private var text: Binding<NSAttributedString>

    @ObservedObject
    private var richTextContext: RichTextContext

    ...
}
```

```swift
open class RichTextCoordinator: NSObject {

    public init(
        text: Binding<NSAttributedString>,
        textView: RichTextView,
        context: RichTextContext) {
        textView.attributedString = text.wrappedValue
        self.text = text
        self.textView = textView
        self.context = context
        super.init()
        self.textView.delegate = self
    }

    public var context: RichTextContext

    ...
}
```

We can now add observable information to the context, such as if the text is underlined or not:

```swift
public class RichTextContext: ObservableObject {

    ...

    @Published
    public var isUnderlined = false

    ...
}
```

We can then use the same text view delegation as before to sync this information with the context when the text view's text or position changes:

```swift
private extension RichTextCoordinator {

    func syncWithTextView() {
        syncContextWithTextView()
        syncTextWithTextView()
    }

    func syncContextWithTextView() {
        let string = textView.attributedString
        let attributes = string.textAttributes(at: textView.selectedRange)
        let isUnderlined = (attributes[.underlineStyle] as? Int) == 1
        context.isUnderlined = isUnderlined
    }

    ...
}
```

As you can see, the api:s are still rough even though we added a `textAttributes(at:)` function to the text view. This is why [RichTextKit]({{page.richtextkit}}) will add even more extensions to make these operations easier.

To toggle the underline style on and off with a SwiftUI button, the button must trigger something that in its turn affect the text view. Since it can't use the editor itself, we'll have to find another way.

We actually just added a way to solve this - the context. If we could observe context changes, we could just change the `isUnderlined` property and let something with access to the underlying text view act on that change. Turns out we have that thing as well - the coordinator.

The coordinator will use `Combine` to observe the context. Let's first add a way to store our observables:

```swift
open class RichTextEditorCoordinator: NSObject, RichTextPresenter {
    
    ...
    
    public var cancellables = Set<AnyCancellable>()

    ...
}
```

Let's then add functions that make the coordinator subscribe to context changes:

```swift
open class RichTextEditorCoordinator: NSObject, RichTextPresenter {
    
    public init(
        text: Binding<NSAttributedString>,
        textView: RichTextView,
        textContext: RichTextContext) {
        ...
        subscribeToContextChanges()
    }

    ...


}

private extension RichTextEditorCoordinator {
    
    func subscribeToContextChanges() {
        subscribeToIsUnderlined()
    }

    func subscribeToIsUnderlined() {
        richTextContext.$isUnderlined
            .sink(
                receiveCompletion: { _ in },
                receiveValue: { [weak self] in self?.setIsUnderlined(to: $0) })
            .store(in: &cancellables)
    }

    func setIsUnderlined(to newValue: Bool) {
        let string = textView.attributedString
        let attributes = string.textAttributes(at: textView.selectedRange)
        let isUnderlined = (attributes[.underlineStyle] as? Int) == 1
        if newValue == isUnderlined { return }
        let value = NSNumber(value: newValue)
        // How to set the text attribute??
    }
}
```

As you can see, we have to duplicate the `isUnderlined` logic from earlier. This is a clear indication (and the content for another post) that we should find a way to simplify this logic later on.

However, let's leave it like this for now, and instead discuss how we can set this new attribute. We can't use the text view's `attributedString`, since it's not mutable, so we have to find another way.

Turns out that `UIKit` has a non-optional `textStorage`, while `NSKit` has an optional property with the same name and type. Let's use this to add a `mutableAttributedString` to the text view types:

```swift
public protocol RichTextViewRepresentable {

    ...
    
    var mutableAttributedString: NSMutableAttributedString? { get }
}

// Then add this to both the UIKit and AppKit view
public extension RichTextView {

    var mutableAttributedString: NSMutableAttributedString? {
        textStorage
    }
}
```

We can now add a last line to our `setIsUnderlined` function:

```swift
private extension RichTextEditorCoordinator {
    
    ...

    func setIsUnderlined(to newValue: Bool) {
        ...
        textView
            .mutableAttributedString
            .setCurrentTextAttribute(.underlineStyle, to: value)
    }
}
```

And with that, we're done! The coordinator will write to the context whenever the text view's text or position changes, and will also observe the context and affect the text view.

We can now add a button to our test app and have it highlight when the context indicates that the text is underlined, and use it to set the underlined style as well:

![Screenshot uf underlining text]({{page.assets}}underline.jpg){:width="650px"}

There are still tons to do to make attribute and trait management easier, and we haven't even started looking at more advanced features like image attachments, but we now have a solid, multi-platform foundation with SwiftUI for future work.


## Conclusion

Although UIKit and AppKit has a bunch of built-in support for rich text, a lot is still missing. Also, some things work very differently in UIKit and AppKit, which makes multi-platform support a hassle. Finally, making things with in SwiftUI requires some tricky coordination.

I hope that this post has made some things clearer and that you found the various examples interesting. You can find the source code in the [RichTextKit]({{page.richtextkit}}) library, which I will evolve over time, by adding more features, extensions, bug fixes etc. I will cover some of this work in future blog posts, so keep an eye at the `rich-text` tag if the topic interests you.

Don't hesitate to comment or reach out with any thoughts you may have. I'd love to hear your thoughts.

Thanks for reading!