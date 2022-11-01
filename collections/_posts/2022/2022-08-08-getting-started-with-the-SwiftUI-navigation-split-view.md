---
title:  Getting started with the SwiftUI NavigationSplitView
date:   2022-08-08 06:00:00 +0000
tags:   swiftui navigation

icon:   swiftui
assets: /assets/blog/2022/2022-08-08/
---

SwiftUI 4 adds a new `NavigationSplitView` component that simplifies creating rich sidebar-based experiences on iPad and macOS, while automatically scaling down to a `NavigationStackView` (also new) on iPhone. It's a powerful component that however can be a bit tricky to get started with, so let's take a look at how to use it and some ways to style it.

Since `NavigationSplitView` requires some setup that is easy to overlook, this post will do some intentional trial and error, where we will first setup things in a way that will fail, then discuss why it fails before fixing it.


## Creating a navigation split view

To serve as a base for the various examples in this post, consider the following basic example, where we have a navigation split view that links to various "demo screens" that are defined by this enum:

```swift
enum DemoScreen: String, Codable {
    case first, second, third

    var title: String {
        rawValue.capitalized
    }
}
```

Let's now create a `NavigationSplitView` with a sidebar that links to the various screens, as well as a main detail area that displays the currently selected screen:

```swift
struct ContentView: View {

    @State
    private var selection: DemoScreen? = .first

    var body: some View {
        NavigationSplitView {
            sidebarContent
        } detail: {
            detailContent
        }
    }
}
```

`sidebarContent` is just a `List` with navigation links to the various screens:

```swift
extension ContentView {

    var sidebarContent: some View {
        List {
            link(to: .first)
            link(to: .second)
            link(to: .third)
        }
    }

    func link(to page: DemoScreen) -> some View {
        NavigationLink(value: page) {
            Text(page.title)
        }
    }
}
```

`detailContent` switches over the `selection`, if any, and displays some screen-specific content:

```swift
extension ContentView {

    @ViewBuilder
    var detailContent: some View {
        if let selection = selection {
            detailContent(for: selection)
                .buttonStyle(.bordered)
        } else {
            Text("No selection")
        }
    }

    @ViewBuilder
    func detailContent(for screen: DemoScreen) -> some View {
        switch screen {
        case .first: Button("First button") {}
        case .second: Button("Second button") {}
        case .third: Button("Second button") {}
        }
    }
}
```

Note that we use a `.bordered` button style to accentuate the button colors for later examples.


## Navigation split view style

If you now run the app on an iPad, it should looks like this when the sidebar is collapsed in landscape:

![A screenshot of an iPad that shows an app with a sidebar toggle button]({{page.assets}}1-ipad.png){:width="650px"}

However, if you now tap the top leading sidebar toggle button, the sidebar may expand as a slid-over:

![A screenshot of an iPad that shows a sidebar without a toggle button]({{page.assets}}2-ipad-no-sidebar-button.png){:width="650px"}

You may also notice that the sidebar button is missing. These effects are however not consistent. For instance, I sometimes get a non-slideover by default. The slideover button will also most often show if the sidebar is expanded when the app launches:

![A screenshot of an iPad that shows a sidebar with a toggle button]({{page.assets}}3-ipad-with-sidebar-button.png){:width="650px"}

I guess these are just beta bugs, so let's not focus on them too much. If a slideover is not the style you want, you can change the `navigationSplitViewStyle` to `.balanced`:

```swift
struct ContentView: View {

    ...

    var body: some View {
        NavigationSplitView {
            sidebarContent
        } detail: {
            detailContent
        }.navigationSplitViewStyle(.balanced)
    }
}
```

This will make the sidebar push and resize the main content to the right, instead of sliding in over it:

![A screenshot of an iPad with a balanced sidebar]({{page.assets}}4-ipad-with-balanced-sidebar.png){:width="650px"}

I really like this style, and it also doesn't suffer from the disappearing button that I mentioned earlier. The style really depends on your use case, though, so just pick the one you think fits your app best.


## Navigation split view selection

Although the sidebar now works, we actually have a pretty serious bug, which you may have noticed in the screenshots above. Although we currently have the `.firstPage` screen selected, it doesn't highlight in the sidebar. Also, the `NavigationLink` items don't render as buttons, but as plain text.

The reason why this happens is one of the subtle `NavigationSplitView` requirements that are easy to miss and hat can take a long time to track down. Luckily, it's easy to fix.

You see, while we do keep track of the current sidebar `selection` as a `@State` property and display it by switching over the cases in `detailContent`, we never bind the selection to the view hierarchy. This means that the navigation split view doesn't know about the currently selected screen.

To fix this, let's add a `selection` parameter to the `List` that is rendered by the `sidebarContent`:

```swift
var sidebarContent: some View {
    List(selection: $selection) {
        link(to: .first)
        link(to: .second)
        link(to: .third)
    }
}
```

If we run the app again, the sidebar will now render the links as buttons, with a selection being applied to the currently selected screen:

![A screenshot of an iPad with working sidebar links]({{page.assets}}5-ipad-with-selection.png){:width="650px"}

Apple probably have their reasons for designing the API this way, but I think it would have been nice if you could specify the `selection` directly on the `NavigationSplitView`  and have it automatically applying it to the `List`. The current api makes it easy to overlook that you have to specify it for the list.


## Styling the navigation split view on iPad

The navigation split view takes its colors from the `.accent` color by default, but you can override this by specifying a custom `.accentColor`:

```swift
struct ContentView: View {

    ...

    var body: some View {
        NavigationSplitView {
            sidebarContent
        } detail: {
            detailContent
        }
        .accentColor(.green)
    }
}
```

As you can see below, applying an accent color will affect all content, including the sidebar button, the sidebar list and the detail content buttons:

![A screenshot of an iPad with a green accent color applied]({{page.assets}}6-ipad-green-accent-color.png){:width="650px"}

You can stop the accent color from being used in the detail screen buttons by applying a global tint color:

```swift
struct ContentView: View {

    ...

    var body: some View {
        NavigationSplitView {
            sidebarContent
        } detail: {
            detailContent
        }
        .tint(.blue)
        .accentColor(.green)
        .navigationSplitViewStyle(.balanced)
    }
}
```

However, a tint color affects buttons in more ways than the accent color, by also tinting the background:

![A screenshot of an iPad with a green accent color and a blue tint color applied]({{page.assets}}7-ipad-green-sidebar-accent-blue-detail-tint.png){:width="650px"}

A better way is to reset the accent color on the detail content, instead of specifying a global tint color:

```swift
struct ContentView: View {
    
    ...

    var body: some View {
        NavigationSplitView {
            sidebarContent
        } detail: {
            detailContent
                .accentColor(.accentColor)
        }
        .accentColor(.green)
        .navigationSplitViewStyle(.balanced)
    }
}
```

This will apply a green accent color to the sidebar, but reset the accent color for the detail content:

![A screenshot of an iPad with a green accent color and a blue tint color applied]({{page.assets}}8-ipad-green-sidebar-accent-standard-detail-accent.png){:width="650px"}

If you want to use different accent colors for the sidebar toggle button and the sidebar content, you can just apply an separate accent color to the sidebar content:

```swift
struct ContentView: View {
    
    ...

    var body: some View {
        NavigationSplitView {
            sidebarContent
                .accentColor(.purple)
        } detail: {
            detailContent
                .accentColor(.accentColor)
        }
        .accentColor(.green)
        .navigationSplitViewStyle(.balanced)
    }
}
```

This can be useful if your app requires the button and content to be colored differently, which I need in an app that I'm currently working on:

![A screenshot of an iPad with a green sidebar button but purple sidebar color]({{page.assets}}9-ipad-green-and-purple-sidebar-accent-standard-detail-accent.png){:width="650px"}


## Styling the navigation split view on iPhone

If you run the app on an iPhone, you'll see how the `NavigationSplitView` is automatically converted to a `NavigationStackView`, with the initial selection being pushed by default:

![A screenshot of an iPhone with a navigation stack view]({{page.assets}}10-iphone.png){:width="450px"}

As you can see, our applied accent colors are still being applied here, with the green color being applied to the back button and the standard color being applied to the detail content button.

However, if you now tap "Back" to present the sidebar, you can see that the list is rendered as a plain `List` instead of using the `.sidebar` style:

![A screenshot of an iPhone that displays the sidebar content as a list]({{page.assets}}11-iphone-sidebar.png){:width="450px"}

Furthermore, the purple accent color isn't applied, since the plain list style ignores it. The current selection is also only indicated briefly before being reset.

If you want to color the list items on iPhone, you thus have to use the `listRowBackground` modifier:

```swift
extension ContentView {

    ... 

    var sidebarContent: some View {
        List(selection: $selection) {
            link(to: .first)
            link(to: .second)
            link(to: .third)
        }
        .listStyle(.sidebar)
        .listRowBackground(Color.purple)
    }

    ...
}
```

However, while you can apply custom background colors to separate list items, you can't highlight the currently selected page, since presenting the menu will reset the current selection.


## Conclusion

I really like the new `NavigationSplitView` and think the `.balanced` style gives me a really nice design in an app that I'm currently porting to iOS 16. There are some strange api choices and some design limitations, but overall I think that this is a great addition to SwiftUI.
