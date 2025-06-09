---
title:  Creating tiny utility apps with SwiftUI Previews
date:   2025-01-04 06:00:00 +0000
tags:   swiftui spm

assets: /assets/blog/25/0104/
image:  /assets/blog/25/0104/image.jpg

old:    https://betterprogramming.pub/swiftui-pain-links-in-text-b31319783c9e

toot:   https://mastodon.social/@danielsaidi/113770773039022380
tweet:  https://x.com/danielsaidi/status/1875563484081185225
---

In this post, we'll take a look at how to create small, lightweight apps that you don't have to install to your physical device, using SwiftUI Previews and Swift Package internal views.


## Background 

I have many [open-source projects](/opensource) projects, where most have a demo app that can let users explore the capabilities of each project.

Creating these apps are easier than ever thanks to SwiftUI, which removes much work compared to UIKit and AppKit, and lets us create multi-platform apps with little effort.

However, I sometimes have to create additional tools to manage these projects. One such example is the [KeyboardKit](https://keyboardkit.com) project, where I prefer a UI instead of unit test to manage certain tasks.

For such tools, creating a separate app for each tool would be a massive time sink, where I'd have to create a project, write the code, and then run the app on a device or simulator.

As an alternative, I've started playing around with package-internal SwiftUI Preview only apps, that can be run directly from within Xcode, with support for persistency, network requests, etc.

Let's take a look.


## Locale Explorer

I just created a SwiftUI Preview app called Locale Explorer, which handles locales for KeyboardKit. It lets me browse all available locales and all implemented ones, navigate to GitHub requests, etc.

Locale Explorer is less than 200 lines and tool less than 30 minutes to write. It's exclusive to macOS and has a split view app with a main menu, a menu detail view, and a locale detail view.

![Locale Explorer Screenshot]({{page.assets}}app.jpg)

The app supports search, will persist menu and locale selection across reloads, and has support for additional screens, like a screen where I can keep track of all relevant links:

<div class="grid col2">
    <img src="{{page.assets}}search.jpg" />
    <img src="{{page.assets}}links.jpg" />
</div>

Gathering all these resources in a single place lets me access and work with this app by just opening the SwiftUI file within the Swift Package, then render the SwiftUI Preview in the Xcode Canvas:

![An image of Xcode running the Locale Explorer app]({{page.assets}}xcode.jpg)

The app lets me switch screens from the menu, browse locales, filter locales by searching, navigate to additional links, all without having to install an app to my device or simulator.


## Multiple apps

As you can see in the screenshot above, KeyboardKit Pro currently has two apps in an `Apps` folder - `LocaleExplorerApp` and `LicenseExplorerApp`.

Storing all of these apps in an `Apps` folder is a convenient way to keep all tools in the same place.


## Code

While this post isn't really about how to create such apps - it's really just plain SwiftUI and Previews - let's just take a look at the LocaleExplorer source code.

Let's create an `LocaleExplorerApp.swift` file that contains an internal `LocaleExplorerApp` SwiftUI view.

Since LocaleExplorer is only meant to be used as a macOS 13+ app, let's add some code for this and adjust the SwiftUI preview size to a good default size:

```swift
#if os(macOS)
import SwiftUI

@available(macOS 13.0, *)
struct LocaleInspectorApp: View {
    
    var body: some View {
        ...
    }
}

#Preview {
    if #available(macOS 13.0, *) {
        LocaleInspectorApp()
            .frame(minWidth: 900, minHeight: 600)
    }
}
#endif
```

Since LocaleExplorer will let us select screens, and list, select and search for locales, let's add types and properties to handle these capabilities:

```swift
#if os(macOS)
import SwiftUI

@available(macOS 13.0, *)
struct LocaleInspectorApp: View {

    init() {
        let ids = Locale.availableIdentifiers
        allLocales  = ids.map { .init(identifier: $0) }.unique()
    }

    enum Screen: String, CaseIterable {
        case all, implemented, remaining, requested, links

        var title: String { rawValue.capitalized }
    }
    
    @AppStorage("com.keyboardkit.localeinspector.localeId")
    var localeId: String?
    
    @AppStorage("com.keyboardkit.localeinspector.screen")
    var screen = Screen.implemented
    
    @State var allLocales: [Locale]
    @State var locale: Locale?
    @State var query = ""
    
    var body: some View {
        ...
    }
}

#Preview {
    if #available(macOS 13.0, *) {
        LocaleInspectorApp()
            .frame(minWidth: 900, minHeight: 600)
    }
}
#endif
```

Since the `localeId` and `screen` properties are both persisted with `AppStorage`, the app will restore the last persisted value every time the preview is reloaded.

The app will use a `NavigationSplitView` with three columns: A left main menu, a center view for the selected screen and a right detail view for the selected locale:

```swift
...

var body: some View {
    NavigationSplitView {
        menu
    } content: {
        view(for: screen)
    } detail: {
        if let locale {
            details(for: locale)
        }
    }
}
```

The menu is implemented by defining a `menu` view property and a `menuItem` view builder function:

```swift
@available(macOS 13.0, *)
private extension LocaleExplorer {

    var menu: some View {
        List(selection: $screen) {
            Image.keyboardKit
                .resizable()
                .aspectRatio(contentMode: .fit)
            Section {
                menuItem(for: .all)
                menuItem(for: .implemented)
                menuItem(for: .requested)
                menuItem(for: .remaining)
            }
            Section {
                menuItem(for: .links)
            }
        }
        .frame(minWidth: 200)
    }

    func menuItem(for screen: Screen) -> some View {
        LabeledContent(screen.title) {
            let locales = locales(for: screen).count
            if locales > 0 {
                Text("\(locales)")
            }
        }
        .tag(screen)
    }
}
```

Since `menu` renders a `List(selection: $screen)`, and each menu item uses `.tag(screen)`, the `screen` property is automatically updated (and persisted) whenever we tap items in the menu:

<div class="grid col2">
    <img src="{{page.assets}}menu-1.jpg" />
    <img src="{{page.assets}}menu-2.jpg" />
</div>

The `view(for screen: Screen)` function can be implemented as a `ViewBuilder`, to either show the selected list of locales, or the "links" view:

```swift
@available(macOS 13.0, *)
private extension LocaleExplorer {

    @ViewBuilder
    func view(for screen: Screen) -> some View {
        switch screen {
        case .links: viewForLinks
        default: viewForSelectedLocales
        }
    }

    var viewForLinks: some View {
        List {
            Text("Links").font(.title)
            Link("iOS Locale Identifiers", destination: .init(string: "https://gist.github.com/jacobbubu/1836273")!)
        }
    }

    var viewForSelectedLocales: some View {
        List(selectedLocales, selection: $locale) { locale in
            VStack(alignment: .leading) {
                Text(locale.localizedName(in: .english) ?? "-")
                    .lineLimit(1)
            }
            .tag(locale)
        }
        .frame(minWidth: 285)
        .navigationTitle("Locale Explorer")
        .searchable(text: $query)
    }
}
```

As you can see above, the `viewForLinks` view is just a hard-coded list, while `viewForSelectedLocales` lists the `selectedLocales` in a `searchable` list.

The `selectedLocales` property is implemented by filtering out relevant locales from `allLocales`. I prefer defining separate properties for the various lists that we're interested in:

```swift
@available(macOS 13.0, *)
private extension LocaleExplorer {

    var implementedLocales: [Locale] {
        allLocales.filter {
            Locale.keyboardKitSupported.contains($0)
        }
    }
    
    var remainingLocales: [Locale] {
        allLocales.filter {
            !Locale.keyboardKitSupported.contains($0)
        }
    }
    
    var requestedLocales: [Locale] {
        allLocales.filter {
            $0.gitHubIssue != nil
        }
    }
    
    var selectedLocales: [Locale] {
        locales(for: screen)
            .matching(query: query)
            .sorted(in: .english)
    }
    
    func locales(for screen: Screen) -> [Locale] {
        switch screen {
        case .all: allLocales
        case .implemented: implementedLocales
        case .remaining: remainingLocales
        case .requested: requestedLocales
        default: []
        }
    }
}
```

To make `Locale` support the query matching, we can add an extension for the type and collection:

```swift
public extension Locale {

    func matches(
        query: String,
        in locale: Locale = .current
    ) -> Bool {
        let query = query.trimmingCharacters(in: .whitespaces)
        if query.isEmpty { return true }
        let name = localizedName ?? ""
        let nameInLocale = localizedName(in: locale) ?? ""
        return name.contains(query) || nameInLocale.contains(query)
    }
}

public extension Collection where Element == Locale {

    func matching(
        query: String,
        in locale: Locale = .current
    ) -> [Locale] {
        filter { $0.matches(query: query, in: locale) }
    }
}

private extension String {

    func contains(_ query: String) -> Bool {
        localizedCaseInsensitiveContains(query)
    }
}
```

The `sorted(in:)` is implemented with separate extensions, which are outside the scope of this post.

Finally, let's add code to automatically setup the view and to persist the locale when it changes:

```swift
...

var body: some View {
    NavigationSplitView {
        ...
    }
    .onChange(of: locale) { localeId = $0?.identifier }
    .onChange(of: screen) {
        if $0 == .links { locale = nil }
    }
    .onAppear { updateLocale(localeId) }
}
```


```swift
@available(macOS 13.0, *)
private extension LocaleExplorer {

    func updateLocale(_ id: String?) {
        guard let id else { return }
        locale = Locale(identifier: id)
    }
}
```

While the `screen` is automatically persisted, the `Locale` can't use `AppStorage` and therefore handles persistency and restorations by persisting the locale identifier.

And that's it! With less than 200 lines of code, we've createad an ultra-light macOS app that can be run directly from within Xcode, without having to manage or install a full-blown app on your device.


## Complete code

For reference, this is all the code that's required for the app, except some additional extensions that live elsewhere in the KeyboardKit Pro package:

```swift
//
//  LocaleInspectorApp.swift
//  KeyboardKitPro
//
//  Created by Daniel Saidi on 2025-01-02.
//  Copyright © 2025 Daniel Saidi. All rights reserved.
//

#if os(macOS)
import SwiftUI

@available(macOS 13.0, *)
struct LocaleInspectorApp: View {
    
    init() {
        let ids = Locale.availableIdentifiers
        allLocales  = ids.map { .init(identifier: $0) }.unique()
    }
    
    enum Screen: String, CaseIterable {
        case all, implemented, remaining, requested, links
        var title: String { rawValue.capitalized }
    }
    
    @AppStorage("com.keyboardkit.localeinspector.localeId")
    var localeId: String?
    
    @AppStorage("com.keyboardkit.localeinspector.screen")
    var screen = Screen.implemented
    
    @State var allLocales: [Locale]
    @State var locale: Locale?
    @State var query = ""
    
    var body: some View {
        NavigationSplitView {
            menu
        } content: {
            view(for: screen)
        } detail: {
            if let locale {
                details(for: locale)
            }
        }
        .onChange(of: locale) { localeId = $0?.identifier }
        .onChange(of: screen) {
            if $0 == .links { locale = nil }
        }
        .onAppear { updateLocale(localeId) }
    }
}

@available(macOS 13.0, *)
private extension LocaleInspectorApp {
    
    var menu: some View {
        List(selection: $screen) {
            Image.keyboardKit
                .resizable()
                .aspectRatio(contentMode: .fit)
            Section {
                menuItem(for: .all)
                menuItem(for: .implemented)
                menuItem(for: .requested)
                menuItem(for: .remaining)
            }
            Section {
                menuItem(for: .links)
            }
        }
        .frame(minWidth: 200)
    }
    
    func menuItem(for screen: Screen) -> some View {
        LabeledContent(screen.title) {
            let locales = locales(for: screen).count
            if locales > 0 {
                Text("\(locales)")
            }
        }
        .tag(screen)
    }
    
    @ViewBuilder
    func view(for screen: Screen) -> some View {
        switch screen {
        case .links: viewForLinks
        default: viewForSelectedLocales
        }
    }
    
    var viewForLinks: some View {
        List {
            Text("Links").font(.title)
            Link("iOS Locale Identifiers", destination: .init(string: "https://gist.github.com/jacobbubu/1836273")!)
        }
    }
    
    var viewForSelectedLocales: some View {
        List(selectedLocales, selection: $locale) { locale in
            VStack(alignment: .leading) {
                Text(locale.localizedName(in: .english) ?? "-")
                    .lineLimit(1)
            }
            .tag(locale)
        }
        .frame(minWidth: 285)
        .navigationTitle("Locale Explorer")
        .searchable(text: $query)
    }
    
    func details(for locale: Locale) -> some View {
        List {
            VStack(alignment: .leading) {
                Text(locale.localizedName(in: .english) ?? "-")
                    .font(.title)
            }
            
            Section("Information") {
                TextField("", text: .constant(locale.identifier))
                TextField("", text: .constant(locale.localizedName(in: locale) ?? "-"))
            }
            
            if let issue = locale.gitHubIssue, let url = URL(string: issue) {
                Section("Links") {
                    Link("GitHub Issue", destination: url)
                }
            }
        }
    }
}

extension Locale: @retroactive Identifiable {}

private extension Locale {
    
    static let gitHubIssues: [String: String] = [
        "vi": "https://github.com/KeyboardKit/KeyboardKit/issues/744"
    ]
    
    var gitHubIssue: String? {
        Self.gitHubIssues[String(identifier.prefix(2))]
    }
}

@available(macOS 13.0, *)
extension LocaleInspectorApp {
    
    var implementedLocales: [Locale] {
        allLocales.filter {
            Locale.keyboardKitSupported.contains($0)
        }
    }
    
    var remainingLocales: [Locale] {
        allLocales.filter {
            !Locale.keyboardKitSupported.contains($0)
        }
    }
    
    var requestedLocales: [Locale] {
        allLocales.filter {
            $0.gitHubIssue != nil
        }
    }
    
    var selectedLocales: [Locale] {
        locales(for: screen)
            .matching(query: query)
            .sorted(in: .english)
    }
    
    func locales(for screen: Screen) -> [Locale] {
        switch screen {
        case .all: allLocales
        case .implemented: implementedLocales
        case .remaining: remainingLocales
        case .requested: requestedLocales
        default: []
        }
    }
    
    func updateLocale(_ id: String?) {
        guard let id else { return }
        locale = Locale(identifier: id)
    }
}

#Preview {
    if #available(macOS 13.0, *) {
        LocaleInspectorApp()
            .frame(minWidth: 900, minHeight: 600)
    }
}
#endif
```



## Conclusion

This code took less than 30 minute to write, can easily be extended with more features, and have no complicated dependencies to anything but the Swift Package in which it's defined.

While this approach isn't applicable to all cases (I need an additional app to test each locale with a real keyboard), creating these tiny tools is a great alternative to managing multiple real apps.