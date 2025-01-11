---
title: Bind view geometry data to bindable properties
date:  2020-03-26 00:00:00 +0100
tags:  swiftui
icon:  swiftui

source: /Sources/SwiftUIKit/Extensions
---

SwiftUI is a great UI framework. However, it's still young and may lack things you need. In this post, we'll look at a way to read geometry information from any view.

{% include kankoda/data/open-source name="SwiftUIKit" %}


## GeometryReader

`GeometryReader` can wrap any view and provide geometric data via its `GeometryProxy`:

```swift
GeometryReader { proxy in
    Text("\(proxy.size.height)")
}
```

But beware, `GeometryReader` is greedy and expands to take up as much space as it can! The code above does not return the text height, but the height of the available space.

Until you understand `GeometryReader`, it can mess up your view hierarchy. Instead, let's create an extension that lets us use it in a safer way.


## View Extension

Let's create a `View` extension that binds any geometric value to a `CGFloat`-based property.

When we're done, we should be able to do this:

```swift
@State private var bodyHeight: CGFloat = 0
@State private var bodyWidth: CGFloat = 0
@State private var textHeight: CGFloat = 0
@State private var textWidth: CGFloat = 0

var body: some View {

    ZStack {
        Color.clear
            .bindGeometry(to: $bodyHeight) { $0.size.height }
            .bindGeometry(to: $bodyWidth) { $0.size.width }
        Text("Hello!")
            .bindGeometry(to: $textHeight) { $0.size.height }
            .bindGeometry(to: $textWidth) { $0.size.width }
    }
}
```

`Color` is greedy and takes up as much space as it can, which will also affect the `ZStack`. We can therefore use `bindGeometry` on either the color or the stack, with the same result. 

`Text`, on the other hand, is not greedy. The binding will therefore get the size of the text.


## Implementation

The implementation of this extension it pretty straightforward:

```swift
extension View {
    
    func bindGeometry(
        to binding: Binding<CGFloat>,
        reader: @escaping (GeometryProxy) -> CGFloat
    ) -> some View {
        self.background(GeometryBinding(reader: reader))
            .onPreferenceChange(GeometryPreference.self) {
                binding.wrappedValue = $0
        }
    }
}

private struct GeometryBinding: View {
    
    let reader: (GeometryProxy) -> CGFloat
    
    var body: some View {
        GeometryReader { geo in
            Color.clear.preference(
                key: GeometryPreference.self,
                value: self.reader(geo)
            )
        }
    }
}

private struct GeometryPreference: PreferenceKey {
    
    typealias Value = CGFloat

    static var defaultValue: CGFloat = 0

    static func reduce(value: inout CGFloat, nextValue: () -> CGFloat) {
        value = max(value, nextValue())
    }
}
```

The `bindGeometry` takes a `binding` and a `reader` function that takes a `GeometryProxy` and returns a `CGFloat`. It then creates a `GeometryBinding` with the `reader`, adds it to the calling view, then binds an `onPreferenceChanged` to the provided `binding`.

`GeometryBinding` is a view builder that creates a `GeometryReader` and binds a `preference` modifier to the provided `reader`.

With this in place, we can now bind any `CGFloat` property of the `GeometryProxy` to any bindable property, e.g. `@State` or the properties of an `@ObservedObject`.


## Cleaning things up

While the extension is convenient, its block-based syntax makes the view hierarchy ugly:

```swift
var body: some View {

    ZStack {
        Color.clear
            .bindGeometry(to: $bodyHeight) { $0.size.height }
            .bindGeometry(to: $bodyWidth) { $0.size.width }
        Text("Hello!")
            .bindGeometry(to: $textHeight) { $0.size.height }
            .bindGeometry(to: $textWidth) { $0.size.width }
    }
}
```

I prefer to use cleaner, more specific extensions, rather than using it as is. For instance, we can use it to create an extension that reads the safe area inset of any `Edge`:

```swift
extension View {
    
    func bindSafeAreaInset(
        of edge: Edge,
        to binding: Binding<CGFloat>) -> some View {
        self.bindGeometry(to: binding) {
            self.inset(for: $0, edge: edge)
        }
    }
}

private extension View {
    
    func inset(for geo: GeometryProxy, edge: Edge) -> CGFloat {
        let insets = geo.safeAreaInsets
        switch edge {
        case .top: return insets.top
        case .bottom: return insets.bottom
        case .leading: return insets.leading
        case .trailing: return insets.trailing
        }
    }
}
```

This makes it possible to convert this:

```swift
.bindGeometry(to: $topInset) { $0.insets.top }
```

to the much cleaner and easier to read:

```swift 
.bindSafeAreaInset(of: .top, to: $topInset)
```

This is however just a matter of taste. I like the cleaner syntax, but the original extension is really all you need.


## Code

I have added these extensions to my [SwiftUIKit]({{project.url}}) library. You can find the source code [here]({{project.url}}{{page.source}}).