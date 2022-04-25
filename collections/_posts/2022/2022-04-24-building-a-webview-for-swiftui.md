---
title:  Building a WebView for iOS and macOS in SwiftUI
date:   2022-04-24 08:00:00 +0100
tags:   article swiftui

assets: /assets/blog/2022/2022-04-24/
image:  /assets/blog/2022/2022-04-24/image.jpg

webviewkit: https://github.com/danielsaidi/WebViewKit
---

SwiftUI currently has no WebView, which means that we have to create it for ourselves. Let's see how we can easily build a multi-platform web view for iOS, iPadOS and macOS.

![Web view preview]({{page.assets}}title.png){:class="plain"}

If you've worked with SwiftUI before, you probably already know that the way to currently do this is to create a `UIViewRepresentable` for UIKit and an `NSViewRepresentable` for AppKit.

Question is, which native view do we want our representable to wrap? We have a few options, of which we'll try out two: `WKWebView` and `SFSafariViewController`.


## WKWebView

`WebKit` provides us with an `WKWebView` view, which supports both iOS and macOS. For our web view to support both iOS and macOS, we need to handle both UIKit and AppKit.

First, let's define a typealias for the view representable type, to make the rest of the code cleaner:

```swift
#if os(iOS)
typealias WebViewRepresentable = UIViewRepresentable
#elseif os(macOS)
typealias WebViewRepresentable = NSViewRepresentable
#endif
```

If the view is added to a target or package that supports more platforms than iOS and macOS, we need to wrap it in an os check:

```swift
#if os(iOS) || os(macOS)
....
#endif
```

Ok, we're ready to start. First import the required frameworks. We need both `SwiftUI` and `WebKit`:

```swift
import SwiftUI
import WebKit
```

We can then define our view. Let's call it `WebView` and have it implement our `ViewRepresentable`:

```swift
public struct WebView: WebViewRepresentable {
    ...
}
```

Let's add two initializers - one that just takes a non-optional `URL` and one that takes an optional `URL` and a `WKWebView` configuration block:

```swift
public init(url: URL) {
    self.url = url
    self.configuration = { _ in }
}

public init(
    url: URL? = nil,
    configuration: @escaping (WKWebView) -> Void = { _ in }) {
    self.url = url
    self.configuration = configuration
}

private let url: URL?
private let configuration: (WKWebView) -> Void
```

We can now create a web view that just loads a url or one that configures itself with a configuration (to setup delegates etc.), then loads the url if we provide it with one.

This approach gives us full flexibility - either a super-simple url-based approach or a little more complex, fully configurable one.

For iOS, we have to implement `makeUIView` and `updateUIView`:

```swift
#if os(iOS)
public func makeUIView(context: Context) -> WKWebView {
    makeView()
}

public func updateUIView(_ uiView: WKWebView, context: Context) {}
#endif
```

For macOS, we have to implement `makeNSView` and `updateNSView`:

```swift
#if os(macOS)
public func makeNSView(context: Context) -> WKWebView {
    makeView()
}

public func updateNSView(_ view: WKWebView, context: Context) {}
#endif
```

Since both platforms will setup `WKWebView` in the same way, we can have a single function for this:

```swift
private extension WebView {
    
    func makeView() -> WKWebView {
        let view = WKWebView()
        configuration(view)
        tryLoad(url, into: view)
        return view
    }

    func tryLoad(_ url: URL?, into view: WKWebView) {
        guard let url = url else { return }
        view.load(URLRequest(url: url))
    }
}
```

And that's it! We now have a `WebView` that can be used on both iOS and macOS.

Let's take a look at the slightly different `SFSafariViewController`.


## SFSafariViewController

`SFSafariViewController` is defined in `SafariServices`. It can display a navigation bar topmost and a toolbar bottommost, with navigation, reload etc.

Unlike `WKWebView`, `SFSafariViewController` only supports iOS, which means that this view will also be iOS only.

If the view is added to a target or package that supports more platforms than iOS, we need to wrap it in an os check:

```swift
#if os(iOS)
....
#endif
```

We're ready to start. First import the required frameworks. We need `SwiftUI` and `SafariServices`:

```swift
import SwiftUI
import SafariServices
```

We can then define our view. Let's call it `SafariWebView`. Since we only support iOS, it just have to implement `UIViewControllerRepresentable`:

```swift
public struct SafariWebView: UIViewControllerRepresentable {
    ...
}
```

Notice that we implement `UIViewControllerRepresentable` instead of `UIViewRepresentable`, since the embedded type is actually a view controller and not a view.

Since, `SFSafariViewController` can be initialized with a url and a configuration, let's adjust the approach from above to let you inject both a `configuration` and a `viewConfiguration`. The configuration will be used to crete the view and the view configuration to configure the created view.

```swift
public init(
    url: URL,
    configuration: SFSafariViewController.Configuration = .init(),
    viewConfiguration: @escaping (SFSafariViewController) -> Void = { _ in }) {
    self.url = url
    self.configuration = configuration
    self.viewConfiguration = viewConfiguration
}

private let url: URL
private let configuration: SFSafariViewController.Configuration
private let viewConfiguration: (SFSafariViewController) -> Void
```

We can then implement `makeUIViewController` and `updateUIViewController`:

```swift
public func makeUIViewController(context: Context) -> SFSafariViewController {
    let controller = SFSafariViewController(url: url, configuration: configuration)
    viewConfiguration(controller)
    return controller
}

public func updateUIViewController(_ safariViewController: SFSafariViewController, context: Context) {}
```

That's it! We now have a `SafariWebView` that can be used on iOS and iPadOS.


## Conclusion

You've seen two ways to create a web view for SwiftUI. I wouldn't be surprised if Apple adds such a view at this year's WWDC, but until they do, I hope that this helps.

I have added the source code and a demo app to a tiny library called [WebViewKit]({{page.webviewkit}}). Feel free to check it out and let me know what you think.