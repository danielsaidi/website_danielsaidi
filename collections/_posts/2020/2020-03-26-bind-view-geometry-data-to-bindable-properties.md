---
title: Bind view geometry data to bindable properties
date:  2020-03-26 00:00:00 +0100
tags:  swiftui swift
icon:  swiftui

lib:   https://github.com/danielsaidi/SwiftUIKit
---

SwiftUI is great for building declarative user interfaces. However, it's still young and lacks many common things. In this post, we'll look at a way to read geometry information from any view in a view hierarchy.


## GeometryReader

If you need to fetch geometric information about of your view hierarchy, `GeometryReader` can be used to wrap any view and provide geometric information via a `GeometryProxy`. 

You can use it like this:

```swift
GeometryReader { geoProxy in
    Text("\(geoProxy.size.height)")
}
```

But beware! `GeometryReader` is greedy and will expand to take up as much space as it can. If you use the code above in your app, the height will not be that of the text, but of the available space.

`GeometryReader` is a great tool, but it comes with many quirks. Until you understand it, it can mess up your view hierarchy. Instead, let's use it to create powerful extensions.


## Extensions

Let's create a `View` extension that binds any geometric value to a `CGFloat`-based property. When we're done, we should be able to do this:

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

Since `Color` is greedy, it will take up as much space as it can, and therefore cause the `ZStack` to expand as well. We can therefore bind the `body` properties to any of these views with the same result. 

`Text`, on the other hand, is not greedy. The text bindings will therefore get the size of the text itself.


## Implementation

The implementation of this extension it pretty straightforward:

```swift
public extension View {
    
    /**
     Bind any `CGFloat` value within a `GeometryProxy` value
     to an external binding.
     */
    func bindGeometry(
        to binding: Binding<CGFloat>,
        reader: @escaping (GeometryProxy) -> CGFloat) -> some View {
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

`bindGeometry` takes a `binding` and a `reader` function that takes a `GeometryProxy` and returns a `CGFloat`. It then creates a `GeometryBinding` using the `reader`, adds it to the calling view, then binds an `onPreferenceChanged` to the provided `binding`.

`GeometryBinding` is just a view creator that creates a `GeometryReader` and binds a `preference` modifier to the provided `reader`.

With this in place, we can now bind any `CGFloat` property of the `GeometryProxy` to any bindable property, e.g. `@State` or the properties of an `@ObservedObject`.


## Cleaning things up

While the extension is convenient, its block-based syntax makes the view hierarchy pretty ugly:

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

I therefore prefer to use it to create cleaner, more specific extensions, rather than using it as is. For instance, we can use it to create an extension that reads the safe area inset of any `Edge`:

```swift
public extension View {
    
    /**
     Bind the safe area insets of a certain edge and bind it
     to the provided binding parameter.
     
     This modifier is very useful when you want a view to be
     able to ignore safe areas, but its embedded views honor
     the previously ignored safe areas. Just use the binding
     to set the edge padding of the view you want to inset.
     */
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

This is just a matter of taste. I like the cleaner syntax, but the original extension is really all you need.


## Code

I have added these extensions to my [SwiftUIKit]({{page.lib}}) library, under `Sources/SwiftUIKit/Extensions`.