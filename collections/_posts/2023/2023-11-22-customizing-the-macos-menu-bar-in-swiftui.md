---
title:  Customizing the macOS menu bar in SwiftUI
date:   2023-11-22 06:00:00 +0000
tags:   swiftui macos

redirect_from: /blog/2023/11/22/how-to-customize-the-macos-menu-bar-in-swiftui

assets: /assets/blog/23/1122/
image:  /assets/blog/23/1122.jpg
image-show: 0

tweet:  https://x.com/danielsaidi/status/1727289933038260289?s=20
toot:   https://mastodon.social/@danielsaidi/111453994720983239
---

In this post, we'll take a look at how to customize the macOS menu bar for a SwiftUI app, using SwiftUI tools like `CommandMenu` and `CommandGroup`.

{% include kankoda/data/open-source name="SwiftUIKit" %}

Although SwiftUI helps you start working on new platforms, you will run into many platform-specific concepts and challenges as you build your first few apps on the new platform.

One thing that was new to me as I started building apps for macOS, was how to customize the menu bar items for your app. 

SwiftUI makes a good job of keeping this simple, with the concept of commands. Let's take a look at how we can add, remove and replace items in the main menu.


## How to customize the macOS menu bar

Let's start with creating a new app in Xcode. If you pick a Multiplatform App or Document App, it will automatically use SwiftUI and target many platforms:

![Xcode's new project window]({{page.assets}}/new-app.jpg)

If we run this app on macOS without customizations, the standard menu will look like this:

![The standard macOS main menu]({{page.assets}}/standard-menu.png)

We can easily add more top-level menu items or modify items within the standard menus, by applying a SwiftUI `.commands` modifier to the `WindowGroup`:

```swift
@main
struct MyAppApp: App {
    var body: some Scene {
        WindowGroup {
            ContentView()
        }
        .commands {
            // ... custom content here
        }
    }
}
```

This modifier builds on both iOS and macOS, so you don't have to use `#if os(macOS)` to opt out code for certain platforms.


## How to add app-specific menu items to the menu bar

To add new menu items to the main menu, you just have to add `CommandMenu` items to the `.commands` menu builder:

```swift
@main
struct MyAppApp: App {
    var body: some Scene {
        WindowGroup {
            ContentView()
        }
        .commands {
            CommandMenu("Custom") {
                Button("Item 1") {}
                Divider()
                Button("Item 2") {}
            }
            CommandMenu("Another one") {
                Button("Item 3") {}
                Divider()
                Button("Item 4") {}
            }
        }
    }
}
```

These custom menu items will be added in order, after the standard `Views` menu item:

![A custom macOS main menu]({{page.assets}}/custom-menu.png)

The content of these menus are regular SwiftUI view builders, which means that you can add anything you want to the command menu:

```swift
@main
struct MyAppApp: App {
    var body: some Scene {
        WindowGroup {
            ContentView()
        }
        .commands {
            CommandMenu("Custom") {
                Color.red
                TextField("Test", text: .constant(""))
                Image(systemName: "face.smiling")
                    .font(.largeTitle)
            }
        }
    }
}
```

However, despite our creative efforts, most views and modifiers will be ignored by the item:

![A custom macOS main menu with a smiley]({{page.assets}}/custom-menu-smiley.png)

You can add keyboard shortcuts to an item to make it easy to trigger it from within the app:

```swift
@main
@main
struct MyAppApp: App {
    var body: some Scene {
        WindowGroup {
            ContentView()
        }
        .commands {
            CommandMenu("Custom") {
                Button("Item 1") {}
                    .keyboardShortcut("x", modifiers: [.command, .shift, .option])
            }
        }
    }
}
```

The macOS menu bar displays these shortcuts as trailing instructions next to the button:

![Keyboard shortcuts]({{page.assets}}/keyboard-shortcut.png)

This is all you have to do to add new top-level menu items to the main menu. Let's take a look at how we can customize the standard menu items.


## How to customize the standard menu items

You can add and remove items from the standard menu items by adding `CommandGroup`s to the `.commands` builder.

Consider the standard `Edit` menu:

![The standard `Edit` menu:]({{page.assets}}/edit-menu.png)

We can customize this menu by adding a couple of `CommandGroup` items to the `commands`:

```swift
@main
struct MyAppApp: App {
    var body: some Scene {
        WindowGroup {
            ContentView()
        }
        .commands {
            CommandMenu("Custom") {
                Button("Item 1") {}
                    .keyboardShortcut("x", modifiers: [.command, .shift, .option])
            }
            CommandGroup(after: .undoRedo) {
                Button("Redo everything") {}
                    .keyboardShortcut("z", modifiers: [.command, .shift, .option])
            }
            CommandGroup(after: .undoRedo) {
                Divider()
                Menu("Import/Export") {
                    Button("Item 1") {}
                    Button("Item 1") {}
                    Button("Item 1") {}
                }
            }
        }
    }
}
```

In the code above, we use `after` to add custom menu items after certain standard items. We can use standard SwiftUI views like `Button`, `Divider` and `Menu`:

![A customized `Edit` menu:]({{page.assets}}/edit-menu-custom.png)

You can even replace entire sections by using `replacing` instead of `before` or `after`:

```swift
@main
struct MyAppApp: App {
    var body: some Scene {
        WindowGroup {
            ContentView()
        }
        .commands {
            CommandGroup(replacing: .undoRedo) {
                Divider()
                Menu("Import/Export") {
                    Button("Item 1") {}
                    Button("Item 1") {}
                    Button("Item 1") {}
                }
            }
        }
    }
}
```

This will completely remove undo/redo from the edit menu:

![A further customized `Edit` menu:]({{page.assets}}/edit-menu-custom-2.png)

Note that the strongly typed items you can use with the command group only specify a couple of types, so you will not get full creative freedom.


## How to handle multiple windows

In multi-window apps, you must be able to identify which of the currently open windows a certain menu command should apply to.

For instance, say that we want our app to be able to set the world's reply within this view:

```swift
struct ContentView: View {
    
    @State
    private var worldReply: String?
    
    var body: some View {
        VStack {
            Image(systemName: "globe")
                .imageScale(.large)
                .foregroundStyle(.tint)
            Text("Hello, world!")
            if let worldReply {
                Divider()
                Text(worldReply)
            }
        }
        .padding()
    }
}
```

How can a menu command access this state? The short answer is "it can't". We must find another way to communicate between the menu and the window.

Having a global singleton state will not work either, since multiple open windows will then react to changes in the global state:

![Multiple open windows]({{page.assets}}/windows.png)

To fix this, we need to use `focus values` to update observable state when focus moves between various windows and views within our app.


## How to implement custom focus values

To support focus, we must create a `FocusedValueKey` and bind it to the menu and the view. 

Let's start with creating a new observable class that will hold our state:

```swift
public class World: ObservableObject {
    
    @Published
    var reply: String?
}
```

Let's then create a `FocusedValueKey` for this state:

```swift
public struct WorldFocusedValueKey: FocusedValueKey {
    
    public typealias Value = World
}
```

We can now extend `FocusedValues` with a property that lets us access this focused value:

```swift
public extension FocusedValues {
    
    typealias World = WorldFocusedValueKey
    
    var world: World.Value? {
        get { self[World.self] }
        set { self[World.self] = newValue }
    }
}
```

We can now add a `World` class `@StateObject` to our view, and use it instead of the `@State` that we had before, then apply a `.focusedValue` modifier to the entire view:

```swift
struct ContentView: View {
    
    @StateObject
    private var world = World()

    var body: some View {
        VStack {
            Image(systemName: "globe")
                .imageScale(.large)
                .foregroundStyle(.tint)
            Text("Hello, world!")
            if let reply = world.reply {
                Divider()
                Text(reply)
            }
        }
        .padding()
        .focusedValue(\.world, world)
    }
}
```

We can then (well, not really) add a `@FocusedValue` property to the app and use it to trigger a reply action from our command menu:

```swift
@main
struct MyAppApp: App {
    
    @FocusedValue(\.world)
    var world: World?
    
    var body: some Scene {
        WindowGroup {
            ContentView()
        }
        .commands {
            CommandMenu("World") {
                Button("Reply") {
                    world?.reply = "Hey!"
                }
                .disabled(world == nil)
                .keyboardShortcut("r", modifiers: [.command, .shift])
            }
        }
    }
}
```

The focused value is optional and `nil` when no view with a matching `.focusedValue` has focus. This means that we can disable the button when it won't do anything.

However, there is one last thing to consider. A window only becomes focused when a view within it is focused. Since we just have texts and dividers, the window will not be focused.

This means that our menu button will never be enabled, even when a window is selected:

![Disabled menu command]({{page.assets}}/no-focus.png)

If we add a text field to the view and starts editing it, the menu button becomes enabled:

![Menu command enabled by text field]({{page.assets}}/focus-with-textfield.png)

Without a focusable view, we can still fix this, by applying `.focusable()` to the entire view:

![Menu command enabled by code]({{page.assets}}/focus-with-code.png)

Since the focus effect is not needed in this case, since the window becomes focused, we can add a `.focusEffectDisabled()` modifier to the view to hide the blue square:

![Menu command enabled by code, without focus effect]({{page.assets}}/focus-without-effect.png)

We can now tap the menu button or use the keyboard shortcut to trigger the action for the selected window:

![A reply being shown in a single window]({{page.assets}}/reply.png)

If we close all windows, the focus value becomes `nil` and the menu button disabled. And with that, we can now consider ourselves to be menu bar experts.