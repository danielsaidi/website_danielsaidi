---
title:  Adding a sticky header to a SwiftUI ScrollView
date:   2023-02-09 10:00:00 +0000
tags:   swiftui open-source scrollview

icon:   swiftui
assets: /assets/blog/2023/2023-02-09/
assets-stretch: /assets/blog/2023/2023-02-06/

tweet:  https://twitter.com/danielsaidi/status/1622632586450173967?s=20&t=T5PE5CWDIX23RE0PNBadUw
toot:   https://mastodon.social/@danielsaidi/109818724244063609

post-offset:    https://danielsaidi.com/blog/2023/02/06/adding-scroll-offset-tracking-to-a-swiftui-scroll-view
post-stretch:   https://danielsaidi.com/blog/2023/02/06/adding-a-stretchable-header-to-a-swiftui-scroll-view

github: https://github.com/danielsaidi/ScrollKit
source: https://github.com/danielsaidi/ScrollKit/blob/main/Sources/ScrollKit/ScrollViewWithStickyHeader.swift
---

As we've previously looked at how to implement [offset tracking]({{page.post-offset}}) and [stretchable headers]({{page.post-stretch}}) for SwiftUI scroll views, let's now combine them to implement a scroll view header that stretches out when you pull down and sticks to the navigation bar as you scroll.

This is a commonly used and loved kind of component, which strangely enough isn't available as a native UIKit or SwiftUI component. If you're unsure of what kind of view I mean, consider this nice album screen from the Spotify iOS app:

![A Spotify screenshot]({{page.assets-stretch}}spotify-demo.jpg)

As you can see, the header stretches out when you pull it down, instead of leaving a gap at the top, then scrolls away with the rest of the content and sticks to the navigation bar with a nice fade animation. It uses nice fade and parallax effects to make this a truly playful experience, so let's implement it as well.

To recreate this view, we first need to be detect the scroll offset, then make the header stretch out when it's pulled down, then finally make it stick to the top as it scrolls past the navigation bar. Let's take a look at how to solve these things seprately, then combine them to implement the final result.


## How to implement scroll offset tracking

We need to detect the scroll offset to be able to stick the header to the top and adjust certain parts of it as it scrolls. For instance, Spotify scales and fades out the cover and fades in the screen title.

As we saw in [this blog post]({{page.post-offset}}), we can detect scroll offset by using a `preference key` and a `coordinate namespace`. By adding a tiny layer on top of a native `ScrollView`, the post's `ScrollViewWithOffset` will continuously provide us with an updated scroll offset as its content scrolls.


## How to implement a stretchable scroll view header

We need to find a way to make the scroll view header stretch out when we pull it down. It would also be nice to make the cover scale up when we do, as the Spotify app does.

As we saw in [this blog post]({{page.post-stretch}}), it's actually pretty easy to implement this kind of scroll view header. It uses a `GeometryReader` to read the frame and applies a `frame` and `offset` to the header's content to make it stretch out downwards. We can use the post's `ScrollViewHeader` with the `ScrollViewWithOffset` to get both offset tracking and a strechable header.


## How to make the scroll view header sticky

We can build upon the `ScrollViewWithOffset` and `ScrollViewHeader` and add a little more code to make the header sticky. Let's start with creating a new `ScrollViewWithStickyHeader` view that wraps a `ScrollViewWithOffset` and stores the scroll offset for future use:

```swift
public struct ScrollViewWithStickyHeader<Content: View>: View {

    public init(
        _ axes: Axis.Set = .vertical,
        showsIndicators: Bool = true,
        onScroll: ScrollAction? = nil,
        @ViewBuilder content: @escaping () -> Content
    ) {
        self.axes = axes
        self.showsIndicators = showsIndicators
        self.onScroll = onScroll
        self.content = content
    }

    private let axes: Axis.Set
    private let showsIndicators: Bool
    private let onScroll: ScrollAction?
    private let content: () -> Content

    public typealias ScrollAction = (_ offset: CGPoint) -> Void

    @State
    private var scrollOffset: CGPoint = .zero

    @State
    private var navigationBarHeight: CGPoint = .zero

    public var body: some View {
        scrollView
    }
}

private extension ScrollViewWithStickyHeader {
    
    var scrollView: some View {
        ScrollViewWithOffset(onScroll: handleScrollOffset) {
            content()
        }
    }

    func handleScrollOffset(_ offset: CGPoint) {
        self.scrollOffset = offset
        self.onScroll?(offset, headerVisibleRatio)
    }
}
```

In the code above, we currently just duplicate most of the `ScrollViewWithOffset` properties and pass them into the wrapped scroll view. We will however make changes to this as we go along.

We will for instance need the navigation bar height to detect when the header is scrolled past it. We can get this by wrapping the scroll view in a `GeometryReader` and read its top `safeAreaInsets`:

```swift
private extension ScrollViewWithStickyHeader {

    var scrollView: some View {
        GeometryReader { proxy in
            ScrollViewWithOffset(onScroll: handleScrollOffset) {
                content()
            }
            .onAppear {
                DispatchQueue.main.async {
                    navigationBarHeight = proxy.safeAreaInsets.top
                }
            }
        }
    }   
}
```

We now have the `scrollOffset` and `navigationBarHeight`. Let's now add a custom header builder and header height to the view.

To support a custom header view, we must add a generic `Header` condition. We can then add `init` parameters and view properies to inject a `header` view builder and a `headerHeight`:

```swift
public struct ScrollViewWithStickyHeader<Header: View, Content: View>: View {

    public init(
        _ axes: Axis.Set = .vertical,
        @ViewBuilder header: @escaping () -> Header,
        headerHeight: CGFloat,
        headerMinHeight: CGFloat? = nil,
        showsIndicators: Bool = true,
        onScroll: ScrollAction? = nil,
        @ViewBuilder content: @escaping () -> Content
    ) {
        self.axes = axes
        self.showsIndicators = showsIndicators
        self.header = header
        self.headerHeight = headerHeight
        self.headerMinHeight = headerMinHeight
        self.onScroll = onScroll
        self.content = content
    }

    private let axes: Axis.Set
    private let showsIndicators: Bool
    private let header: () -> Header
    private let headerHeight: CGFloat
    private let headerMinHeight: CGFloat?
    private let onScroll: ScrollAction?
    private let content: () -> Content
    
    ...
}
```

We can then adjust the `scrollView` so that it adds the `header` topmost in a `VStack` with `0` spacing:

```swift
var scrollHeader: some View {
    ScrollViewHeader(content: header)
        .frame(height: headerHeight)
}

var scrollView: some View {
    GeometryReader { proxy in
        ScrollViewWithOffset(onScroll: handleScrollOffset) {
            VStack(spacing: 0) {
                scrollHeader
                content()
            }
        }
        ...
    }
}
```

If we would stop coding now, we would already have a pretty nice scroll view with a stretchy header. This header would however scroll away with the content. If you consider the Spotify screen we saw above, it would currently look like this:

![A screenshot the header is scrolled past the navbar]({{page.assets}}no-header.png)

To make the header stick to the top, we must detect when it has scrolled past the navigation bar. We can calculate this using the `navigationBarHeight` and `headerHeight`:

```swift
private var headerVisibleRatio: CGFloat {
    max(0, (headerHeight + scrollOffset.y) / headerHeight)
}
```

Since the header view is added topmost in the scroll view and `scrollOffset.y` decreases as we scroll, we just have to add the offset to the `headerHeight` to get how many points that are still "visible". If we divide this with the `headerHeight` and limit it to zero, we get a visibility ratio that starts at `1` (all of the header is visible), then decrease down to `0` as the header is scrolled under the navigation bar. As a fun bonus, the value becomes greater than `1` when you pull down. This can be used for pull down effects.

The header is considered visible as long as it has not been fully covered by the navigation bar, even if the bar is transparent and you can see through it. When the header becomes fully hidden, we should detach it from the scroll view to avoid it from scrolling further.

We can however not remove the header without affecting the position of the rest of the content...but we actually don't need to. We can just add a second header view when `headerVisibleRatio` becomes `0`:

```swift
public struct ScrollViewWithStickyHeader<Header: View, Content: View>: View {

    ...

    public var body: some View {
        ZStack(alignment: .top) {
            scrollView
            navbarOverlay
        }
    }
}

private extension ScrollViewWithStickyHeader {

    @ViewBuilder
    var navbarOverlay: some View {
        if headerVisibleRatio <= 0 {
            Color.clear
                .frame(height: navigationBarHeight)
                .overlay(scrollHeader, alignment: .bottom)
                .ignoresSafeArea(edges: .top)
        }
    }
}
```

Here, we wrap the scroll view in a `ZStack` and overlays it with a view that bottom aligns a second scroll view header over a clear color once the `headerVisibleRatio` becomes `0`.

If we compare the old result to the left with the new result to the right, you will see something strange:

![A screenshot where the additional header cuts off visible content]({{page.assets}}title-cut-off.jpg)

The right scrolled title is cut off too soon, which means that the additional header actually added but becomes too tall. Also, the navigation bar should be transparent.

The reason why the additional header becomes too tall is that we are using the default navigation bar title display mode, which is `.large`. Let's change this to `.inline`:

```swift
public struct ScrollViewWithStickyHeader<Header: View, Content: View>: View {

    ...

    public var body: some View {
        ZStack(alignment: .top) {
            scrollView
            navbarOverlay
                .ignoresSafeArea(edges: .top)
                .frame(minHeight: headerMinHeight)
        }
        #if os(iOS)
        .navigationBarTitleDisplayMode(.inline)
        #endif
    }
}
```

This makes the additional header view height correct, which means that the scrollable title is now visible:

![A screenshot where the additional header cuts off visible content]({{page.assets}}title-not-cut-off.jpg)

To make the navigation bar transparent, we can use the new `toolbarBackground` view modifier, which is available in iOS 16 and later:

```swift
public struct ScrollViewWithStickyHeader<Header: View, Content: View>: View {

    ...

    public var body: some View {
        ZStack(alignment: .top) {
            scrollView
            navbarOverlay
                .ignoresSafeArea(edges: .top)
                .frame(minHeight: headerMinHeight)
        }
        #if os(iOS)
        .toolbarBackground(.hidden)     // <- Added
        .navigationBarTitleDisplayMode(.inline)
        #endif
    }
}
```

This makes the navigation bar transparent, which means that the additional header is now visible, which you can tell by the purple gradient:

![A screenshot of a sticky header]({{page.assets}}sticky-header.jpg)

You may also notice that the navigation bar title uses a different font. This is because the screen replaces the default title with a custom one that fades in as the header is scrolled away:

```swift
scrollView
    .toolbar {
        ToolbarItem(placement: .principal) {
            Text("We've Come for You All")
                .font(.headline.bold())
                .opacity(1-headerVisibleRatio)
        }
    }
```

And that's basically all you need to create a sticky header. This is how the screen now looks and behaves when it's pulled down and scrolled:

![Screenshot of how the screen looks when being pulled and scrolled]({{page.assets}}result.jpg)

If you look closely, you can see that the cover is scaled up when we pull down, since we get more vertical space for the header content. We've also applied a fun little tilt effect that causes the cover to rotate around the x axis as the visible ration grows above `1`. Finally, you can see that the cover has a parallax effect that cause it to be overlapped by the scrollable content.

Hooray - we now have a fully functional sticky header! However, to make it easier to adjust the screen content when we scroll, we should make this view provide us with more information in the scroll action. Let's modify the `ScrollAction` typealias to also include the header visible ratio:

```swift
public typealias ScrollAction = (
    _ offset: CGPoint, 
    _ headerVisibleRatio: CGFloat
) -> Void
```

then adjust the `handleScrollOffset` to include the ratio when calling `onScroll`:

```swift
func handleScrollOffset(_ offset: CGPoint) {
    self.scrollOffset = offset
    self.onScroll?(offset, headerVisibleRatio)
}
```

Having this additional information will make it easier to create certain effects that depend on the header visibility rather than the scroll offset.

Ok, that's about it! Let's take a look at the final scroll view code, then wrap up by looking at how to apply fun pull down and parallax effects to the scroll view.


## Final code

Although this post became rather long, the `ScrollViewWithStickyHeader` code is actually just this:

```swift
public struct ScrollViewWithStickyHeader<Header: View, Content: View>: View {

    public init(
        _ axes: Axis.Set = .vertical,
        @ViewBuilder header: @escaping () -> Header,
        headerHeight: CGFloat,
        headerMinHeight: CGFloat? = nil,
        showsIndicators: Bool = true,
        onScroll: ScrollAction? = nil,
        @ViewBuilder content: @escaping () -> Content
    ) {
        self.axes = axes
        self.showsIndicators = showsIndicators
        self.header = header
        self.headerHeight = headerHeight
        self.headerMinHeight = headerMinHeight
        self.onScroll = onScroll
        self.content = content
    }

    private let axes: Axis.Set
    private let showsIndicators: Bool
    private let header: () -> Header
    private let headerHeight: CGFloat
    private let headerMinHeight: CGFloat?
    private let onScroll: ScrollAction?
    private let content: () -> Content

    public typealias ScrollAction = (_ offset: CGPoint, _ headerVisibleRatio: CGFloat) -> Void

    @State
    private var navigationBarHeight: CGFloat = 0

    @State
    private var scrollOffset: CGPoint = .zero

    private var headerVisibleRatio: CGFloat {
        max(0, (headerHeight + scrollOffset.y) / headerHeight)
    }

    public var body: some View {
        ZStack(alignment: .top) {
            scrollView
            navbarOverlay
        }
        .prefersNavigationBarHidden()
        #if os(iOS)
        .navigationBarTitleDisplayMode(.inline)
        #endif
    }
}

private extension ScrollViewWithStickyHeader {

    var headerView: some View {
        header().frame(height: headerHeight)
    }

    @ViewBuilder
    var navbarOverlay: some View {
        if headerVisibleRatio <= 0 {
            Color.clear
                .frame(height: navigationBarHeight)
                .overlay(scrollHeader, alignment: .bottom)
                .ignoresSafeArea(edges: .top)
        }
    }

    var scrollView: some View {
        GeometryReader { proxy in
            ScrollViewWithOffset(onScroll: handleScrollOffset) {
                VStack(spacing: 0) {
                    scrollHeader
                    content()
                }
            }
            .onAppear {
                DispatchQueue.main.async {
                    navigationBarHeight = proxy.safeAreaInsets.top
                }
            }
        }
    }

    var scrollHeader: some View {
        ScrollViewHeader(content: header)
            .frame(height: headerHeight)
    }

    func handleScrollOffset(_ offset: CGPoint) {
        self.scrollOffset = offset
        self.onScroll?(offset, headerVisibleRatio)
    }
}

private extension View {

    @ViewBuilder
    func prefersNavigationBarHidden() -> some View {
        #if os(iOS) || os(macOS)
        if #available(iOS 16.0, macOS 13.0, *) {
            self.toolbarBackground(.hidden)
        } else {
            self
        }
        #else
        self
        #endif
    }
}
```

Note that the `toolbarBackground` modifier is only available in iOS 16 and later, so if you target iOS 15 and before, you'd have to use other ways to make it transparent. One way is to use appearance proxies.



## How to implement the pull down tilt effect

Most of the cover behavior that we see when pulling down is actually automatically applied by giving the cover an aspect ratio and padding. This is how the cover is created:

```swift
var cover: some View {
    AsyncImage(
        url: URL(string: "https://upload.wikimedia.org/wikipedia/en/8/8f/AnthraxWCFYA.jpg"),
        content: { image in
            image.image?.resizable()
                .aspectRatio(contentMode: .fit)
        }
    )
    .aspectRatio(1, contentMode: .fit)
    .cornerRadius(5)
    .shadow(radius: 10)
    .opacity(headerVisibleRatio)
    .padding(.top, 60)
    .padding(.horizontal, 20)
}
```

We make the cover square by giving it an aspect ratio of `1`. The `fit` content mode makes it stay within the available space, which means that it will automatically grow as we pull down and give it more space. It will then keep growing until it the horizontal padding kicks in.

To implement the tilt effect, we just have to use the `headerVisibleRatio`. Greater than `1` means we are pulling down, at which we can apply a `rotation3DEffect` around the x-axis. 

We determine the number of degrees like this:

```swift
var rotationDegrees: CGFloat {
    guard headerVisibleRatio > 1 else { return 0 }
    let value = 20.0 * (1 - headerVisibleRatio)
    return value.capped(to: -5...0)
}
```

And apply it like this:


```swift
var cover: some View {
    AsyncImage(
        url: URL(string: "https://upload.wikimedia.org/wikipedia/en/8/8f/AnthraxWCFYA.jpg"),
        content: { image in
            image.image?.resizable()
                .aspectRatio(contentMode: .fit)
        }
    )
    .aspectRatio(1, contentMode: .fit)
    .cornerRadius(5)
    .shadow(radius: 10)
    .rotation3DEffect(.degrees(rotationDegrees), axis: (x: 1, y: 0, z: 0))  // <-- Here
    .opacity(headerVisibleRatio)
    .padding(.top, 60)
    .padding(.horizontal, 20)
}
```

You now get a subtle, but very nice tilt effect when you pull down. To me, this makes all the difference in making the pull down effect playful and enjoyable:

![Animated gif showing the tilt effect when pulling down]({{page.assets}}pull-down.gif)

Next, let's look at how to implement the parallax effect that cause the cover to slide below the content.



## How to implement the parallax effect

The parallax effect is very similar to the tilt effect. We just have to check that the `headerVisibleRatio` is less than `1` which means that we are scrolling, then apply an x `offset`.

We determine the offset like this:

```swift
var verticalOffset: CGFloat {
    guard headerVisibleRatio < 1 else { return 0 }
    return 70.0 * (1 - headerVisibleRatio)
}
```

And apply it like this:


```swift
var cover: some View {
    AsyncImage(
        url: URL(string: "https://upload.wikimedia.org/wikipedia/en/8/8f/AnthraxWCFYA.jpg"),
        content: { image in
            image.image?.resizable()
                .aspectRatio(contentMode: .fit)
        }
    )
    .aspectRatio(1, contentMode: .fit)
    .cornerRadius(5)
    .shadow(radius: 10)
    .rotation3DEffect(.degrees(rotationDegrees), axis: (x: 1, y: 0, z: 0))
    .offset(y: verticalOffset)    // <-- Here
    .opacity(headerVisibleRatio)
    .padding(.top, 60)
    .padding(.horizontal, 20)
}
```

You now get a subtle parallax effect when you scroll (here with trackpad induced pauses):

![Animated gif showing the parallax effect when scrolling]({{page.assets}}parallax.gif)

Some additional details is the `opacity` modifier that causes the cover to fade out. You may also notice how the title also fades in the more we scroll.


## Conclusion

The `ScrollViewWithStickyHeader` in this post lets you create scroll views with sticky headers by just providing a custom header and header height. It will continuously provide you with the scroll offset and the visible header ratio as you scroll, which you can use to create amazing scroll effects. 

I have added this view to my newly released [ScrollKit]({{page.github}}) library. You can find the source code [here]({{page.source}}). If you decide to give it a try, I'd be very interested in hearing what you think.

Big thanks to [Daniel Arden]({{page.arden}}) for joining me in my efforts to extend the native SwiftUI `ScrollView` with these great features.