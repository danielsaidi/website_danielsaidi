---
title:  Building a rich text editor for UIKit, AppKit and SwiftUI
date:   2022-07-01 06:00:00 +0000
tags:   swift swiftui

icon:   swiftui
assets: /assets/blog/2022/2022-07-01/

oribi:         https://www.oribi.se/en
oribi-writer:  https://oribi.se/en/apps/oribi-writer/
okeyboard:     https://oribi.se/en/apps/okeyboard/
richtextkit:   https://github.com/danielsaidi/RichTextKit
---

In this post, we'll look at how to build a rich text editor for UIKit, AppKit and SwiftUI. We'll extend a bunch of native types to extend the foundation support for rich text, create new types to bridge the various platforms and make sure that we have a nice, working foundation that we can expand in future posts.


## Background

Rich text editing on Apple's platforms is pretty straightforward. Just create a `UITextView` in UIKit (iOS and tvOS) and an `NSTextView` in AppKit (macOS) and use attributed strings, and you're good to go. The text view will automatically support different fonts, styles, alignments etc. and can insert images and other kind of rich content with very little extra work.

Well, you could think that it'd be that easy, but unfortunately it's not. Many basic tasks are actually pretty complicated to achieve with these views and attributed strings, and requires the same solutions to be discovered and invented by each new developer that takes on this task. Adding multi-platform support to the mix makes things even worse, since the various platforms handles strings, attributes, attachments etc. differently, which means that we also have to find a way to design around the platform differences.

Another complication is SwiftUI, where we have to find a way to embed and bridge the platform-specific views in a way that works on all platforms. We also need some way of letting SwiftUI affect the platform-specific views, and for the platform-specific views and their delegates to affect SwiftUI correctly.

All in all, this is a pretty complicated task, which is why I'm happy to announce that my client [Oribi]({{page.oribi}}) has given me permission to open-source a rich text engine that I created for them as part of building a new version of their text editor [Oribi Writer]({{page.oribi-writer}}).


## About Oribi and Oribi Writer

[Oribi]({{page.oribi}}) is a Swedish company that develops powerful spelling aids and other types of language support for the digital and physical world. [Oribi Writer]({{page.oribi-writer}}) is their rich text editor, which features many of Oribi's amazing features, like spellcheck, word prediction, TTS, a lexicon and much more.

![OribiWriter screenshot]({{page.assets}}oribi-writer.jpg){:width="650px"}

Oribi Writer was a 10+ years old Objective-C app, when Oribi asked me to build a new version that used many of the tools that I helped them port to Swift for their [oKeyboard]({{page.okeyboard}}) app. I built the new Oribi Writer from scratch as a SwiftUI multi-platform app, and ported the old Objective-C code to Swift, feature by feature. We first released an iOS version for iPhone and iPad, and will soon release a macOS version.

The new version of the app uses the new SwiftUI document app capabilities and lets you view and edit rich text, with the additional Oribi features mentioned earlier. The first version supports toggling bold, italic and underline, adjusting font size and alignment, inserting images, highlighting text, scroll to follow the text that is being read etc.

The rest of the post will look at the technical challenges from a general point of view, although much of the problems that I solves was highly influenced by the features that were needed by Oribi Writer. Before we proceed, one final big thanks to Oribi for letting me open-source this engine. I'm sure it will help many developers out there.


## Designing for multi-platform

When building a multi-platform rich text engine that also should support SwiftUI, I was careful to design the foundation as platform-agnostic as possible. Designing for the unknown is often a vain task, but in this case, I just designed it in a way that I thought would made sense for both UIKit and AppKit, based on my at the time limited knowledge about AppKit.

Long story short, I made a few AppKit assumptions that didn't hold true, such as assuming that both a UITextView and an NSTextView scrolls in the same way (spoiler alert - they don't). Turns out that UIKit and AppKit are actually quite different on a foundational level, but having a platform-agnostic engine made the required adjustments easy, without having to change the application layer.


## The scope of this post

As we'll now start implementing this multi-platform rich text engine, we'll start with nothing but the native, foundation support that we get from Apple. We'll then work our way towards a library that has a basic rich text engine that works with UIKit, AppKit and SwiftUI and has the capability to present and edit rich text, apply text styles, change font size and alignment and sync any changes between SwiftUI and the underlying platforms. We'll leave images for a future post, since it's a pretty big topic.

The result will be released as an open-source library called [RichTextKit]({{page.richtextkit}}), which you'll be able to use in your own projects. When you read this, the library already exists, although it may not yet have all the features that it will have later. I will keep expanding the library over time, and will try to cover interesting topics in future blog posts.


## Creating a rich text view

Although `UITextView` and `NSTextView` contains a bunch of functionality, we'll have to extend them with more functionality later on and override some functionality. 

Therefore, let's start by creating a new `RichTextView` view for UIKit and AppKit:

```swift
#if os(macOS)
import AppKit

/**
 This view inherits `NSTextView` and applies additional rich
 text capabilities to it.
 */
public class RichTextView: NSTextView {
}
#endif
```

```swift
#if os(iOS) || os(tvOS)
import UIKit

/**
 This view inherits `UITextView` and applies additional rich
 text capabilities to it.
 */
public class RichTextView: UITextView {
}
#endif
```

As you can see, we can only implement this view for `macOS`, `iOS` and `tvOS`, since `UITextView` is not available on `watchOS`. However, many extensions that we'll add later *will* be, which means that the library will make it easier to work with rich text on watchOS as well.


## Creating a rich text editor for SwiftUI

To use these new text views in SwiftUI, let's create a `RichTextEditor` that wraps either of these views depending on which platform we're on.

To wrap UIKit and AppKit in SwiftUI, we use `UIViewRepresentable` and `NSViewRepresentable`. To simplify our code, we can create a typealias that removes the platform-specific details:

```swift
#if canImport(UIKit)
import UIKit

typealias ViewRepresentable = UIViewRepresentable
#endif

#if os(macOS)
import AppKit

typealias ViewRepresentable = NSViewRepresentable
#endif
```

We can now create our SwiftUI rich text editor without having to care about which platform we're on:

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
    #endif

    #if os(macOS)
    public let scrollView = RichTextView.scrollableTextView()

    public var textView: RichTextView {
        scrollView.documentView as? RichTextView ?? RichTextView()
    }
    #endif


    #if os(iOS) || os(tvOS)
    public func makeUIView(context: Context) -> some UIView {
        textView.attributedText = attributedString
        return textView
    }

    public func updateUIView(_ view: UIViewType, context: Context) {}
    #endif

    #if os(macOS)
    public func makeNSView(context: Context) -> some NSView {
        textView.textStorage?.setAttributedString(attributedString)
        return scrollView
    }

    public func updateNSView(_ view: NSViewType, context: Context) {}
    #endif
}
#endif
```

The code above shows one of my first learning as I started developing a macOS version of Oribi Writer. As you can see, iOS and tvOS uses the `RichTextView` that we created earlier. I first did the same for macOS, just to notice that the text view didn't scroll as the text content grew longer.

Turns out that in AppKit, you have to create a scroll view from the text view type that you intend to use, then cast its `documentView` to get a text view. You then need to return the scroll view to get a scrolling text view. We will need the text view itself later, so lets create a `textView` property for macOS as well.

To create an instance of the text editor, we have to provide it with an `NSAttributedString` binding, which will let us use a shared, attributed string  in our editor.

We can now create a `RichTextEditor` in SwiftUI, regardless of the platform we're on. The text view will display any rich text that we provide it with, and can edit it as well. However, if we'd display the rich text in another part of the app, you'd notice that typing in the text field will not update it elsewhere.

![A screenshow of how changes are not synced from the text view back to the state binding]({{page.assets}}changes-not-synced.jpg){:width="650px"}

In the image above, I have typed in the text field, but the `Text` view still shows the original text. This is because we never write back any changes in the text editor to the text binding. Let's fix that.


## Syncing changes

To listen for changes in the text views and sync them back to the text binding, we need to implement the text view delegation for both platforms. Since `TextEditor` is a struct, it can't be used as the delegate, but we can solve this by setting up a `coordinator` in the text editor and use it as the delegate.

Let's create a `RichTextCoordinator`, which will be used to coordinate changes between SwiftUI and the underlying text views.

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

We want to provide the coordinator with a text binding and a `RichTextView` and let it observe changes in both, then sync any changes with the other.

However, this code won't compile, since `attributedString` is not a property in neither `UITextView` or `NSTextView`. The `UITextView` property is called `attributedText`, while the `NSTextView` has an `attributedString()` and requires you to use its optional `textStorage` to change the text.

This is where I prefer to add additional properties as extensions, to make both views get the same api:s when using them. Let's first add an `attributedString` to `RichTextView` for UIKit:

```swift
public extension RichTextView {
    
    var attributedString: NSAttributedString {
        get { super.attributedText ?? NSAttributedString(string: "") }
        set { attributedText = newValue }
    }
}
```

We can then implement the same property for `RichTextView` for AppKit:

```swift
public extension RichTextView {
    
    var attributedString: NSAttributedString {
        get { attributedString() }
        set { textStorage?.setAttributedString(newValue) }
    }
}
```

To make sure that we actually do have the same public api:s for both platforms, I also prefer to create a protocol that enforces this. Let's call it `RichTextViewRepresentable`:

```swift
public protocol RichTextViewRepresentable {

    var attributedString: NSAttributedString { get }
}
```

We can now let both `RichTextView` implementations implement this protocol, to get compile support that the protocol is correctly implemented.

```swift
#if os(macOS)
import AppKit

extension RichTextView: RichTextViewRepresentable {}
#endif
```

```swift
#if os(iOS) || os(tvOS)
import UIKit

extension RichTextView: RichTextViewRepresentable {}
#endif
```

We can now use this protocol to bridge any differences between the two platforms, which will make our code cleaner. We can also remove setting `attributedString` in `makeUIView` and `makeNSView`, since our coordinator now takes care of that when it's initialized.

Our `RichTextCoordinator` now compiles, so we are now ready to start observing changes in the text view. We can do this by setting up the coordinator as the text view delegate in the initializer:

```swift
open class RichTextEditorCoordinator: NSObject {

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

#if os(iOS) || os(tvOS)
import UIKit

extension RichTextCoordinator: UITextViewDelegate {}

#elseif os(macOS)
import AppKit

extension RichTextCoordinator: NSTextViewDelegate {}
#endif
```

We have to implement this delegate differently for UIKit and AppKit, but we'll use the shared protocol to bridge any differences. Let's setup a way to sync the text binding with the text view's current state:

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

We can now setup the (so far limited) delegate handling, where we'll update the text binding whenever we type in the text view or move the cursor. For UIKit, it will look like this:

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
}
```

and for AppKit, it will loke like this:

```swift
open class RichTextCoordinator: NSObject {

    ...

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

However, if we run the test app, we still don't get any updates to our initial text if we type or move the cursor. This is because we haven't setup our coordinator yet.

Let's update the text editor to setup a `RichTextCoordinator` in `makeCoordinator`:

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

If we now run the test app again, you will see that the `Text` view now updates correctly when we type in the text field. This is because our coordinator listens for changes in the text field and updates the text binding when this happens.

However, if we now change the text binding's wrapped value by tapping the button in our test app, our text editor still doesn't update. This is actually something we have to leave for later, since updating the text editor is quite tricky, if we want things like text position to behave correctly when the text changes.

Instead, let's look at how to change the style of the text, by letting us toggle text styles like bold, italic and underline.


## Changing text style

To be able to change text style, we first have to find a way to get and set whether or not the current selection in the rich text editor should be bold, italic, underline etc.



## Conclusion

Although UIKit and AppKit has a bunch of build-in support for rich text, a lot is still missing. Also, some things work very different in UIKit and AppKit, and SwiftUI has no support at all as this is written.

If you're interested in the source code, you can find it in my [RichTextKit]({{page.richtextkit}}) library. Don't hesitate to comment or reach out with any thoughts you may have. I'd love to hear your thoughts on this.
















Besides observing the text, there will actually be a bunch of things that we need to observe as we add more features to our text editor. For instance, we may want to display the current font, text size, styles etc. when we move the input cursor, and want to be able to change these things from SwiftUI as well.

However, the text binding is different, since the source text can come from anywhere, e.g. a document in a document-based app. However, we don't want state properties for all other information that we may need to add later. As such, let's create an observed object that can help us with this. 

Let's create a `RichTextContext` that will be used for future state.

```swift
import SwiftUI

public class RichTextContext: ObservableObject {

    public init(
        text: NSAttributedString) {
        self.text = text
    }

    public init(
        text: String) {
        self.text = NSAttributedString(string: text)
    }

    @Published
    public var text: NSAttributedString
}
```

This context will be used to keep state for the rich text editor. We'll later add more functionality to it, but for now let's just use it to store the rich text.