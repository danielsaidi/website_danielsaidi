---
title:  How to customize the macOS menu bar in SwiftUI
date:   2023-11-22 06:00:00 +0000
tags:   swiftui macos

image:  /assets/blog/2023/231122/header.jpg
assets: /assets/blog/2023/231122/

tweet:  https://x.com/danielsaidi/status/1727289933038260289?s=20
toot:   https://mastodon.social/@danielsaidi/111453994720983239
---

In this post, we'll take a look at how to customize the macOS menu bar for a SwiftUI app, using SwiftUI tools like `CommandMenu` and `CommandGroup`.

![Blog header image]({{page.image}})

{% include kankoda/data/open-source.html name="SwiftUIKit" %}

Although SwiftUI will kickstart your ability to start working on new platforms within the Apple ecosystem, there are still many platform specifics you may run into as you build your first few apps.

One thing that was new to me as I started developing apps for macOS, was to add and remove menu bar items for your app. SwiftUI makes a good job of keeping this simple, with the concept of commands.


## How to customize the macOS menu bar

Let's start with creating a new app in Xcode. If you go with a Multiplatform App or Document App, it will automatically use SwiftUI:

![Xcode's new project window]({{page.assets}}/new-app.jpg)

If we then run this app on our Mac, the standard menu will look like this:

![The standard macOS main menu]({{page.assets}}/standard-menu.png)

We can now add custom top-level menu items or modify the items within the standard ones, by applying the SwiftUI `.commands` modifier to the `WindowGroup`:

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

The `.commands` modifier will build on all platforms, so you don't have to use `#if os(macOS)` to check which platform you're on.


## How to add app-specific menu items to the menu bar

To add app-specific menu items to the menu bar, you just have to add one or multiple `CommandMenu` items to the `.commands` builder:

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

The content of these command menus are "just" regular SwiftUI view builders, which means that you can add anything you want to the command menu:

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

However, despite our best creative efforts, most views and view modifiers will be ignored by the menu:

![A custom macOS main menu with a smiley]({{page.assets}}/custom-menu-smiley.png)

You can add keyboard shortcuts to any button to make it easy to trigger it from anywhere in the app:

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

The macOS menu bar displays keyboard shortcuts as trailing instructions next to the button label:

![Keyboard shortcuts]({{page.assets}}/keyboard-shortcut.png)

This is basically all you need to do to get started with macOS menu bar customization. Let's now take a look at how to customize the standard top-level menus.


## How to customize the standard menus

You can add and remove (some) items from any of the standard menus by adding `CommandGroup` items to the `.commands` builder.

Consider the standard `Edit` menu:

![The standard `Edit` menu:]({{page.assets}}/edit-menu.png)

Let's customize the standard `Edit` menu by adding a couple of command groups to our app:

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

As you can see, it's very easy to modify it with standard SwiftUI views like `Button`, `Divider` and `Menu`:

![A customized `Edit` menu:]({{page.assets}}/edit-menu-custom.png)

You can even replace entire sections by using `replacing` instead of `before` or after:

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

Still, do note that the strongly typed items you can use in the command group initializer only specify a couple of types, so you will not get full creative freedom when customizing your menus.


## How to handle multiple windows

When working with multi-window apps, you may run into having to identify which of the currently open windows a certain menu command should apply to.

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

How should a menu command be able to access this state? The short answer is that it can't, we need to find another way to communicate between the menu and the window.

Having a global, observable state will not work either, if we can have multiple windows open and only want the currently active window to be affected:

![Multiple open windows]({{page.assets}}/windows.png)

To fix this, we need to look into focus values, which will let us bind values to a certain focus key when we focus on various screens and views within our app.


## How to implement custom focus values

To implement custom focus values, we must create a custom `FocusedValueKey` and bind it to both the menu and the view. Let's start with creating a new observable class that will hold our state:

```swift
public class World: ObservableObject {
    
    @Published
    var reply: String?
}
```

Second, let's create a `FocusedValueKey` for this kind of state:

```swift
public struct WorldFocusedValueKey: FocusedValueKey {
    
    public typealias Value = World
}
```

We can then extend `FocusedValues` with a `world` property, that will let us access this focused value:

```swift
public extension FocusedValues {
    
    typealias World = WorldFocusedValueKey
    
    var world: World.Value? {
        get { self[World.self] }
        set { self[World.self] = newValue }
    }
}
```

We can now add a `@StateObject` for the `World` class to our content view, use that state instead of the `@State` we had before, and finally apply a `.focusedValue` modifier to the entire view:

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

We can finally (well..not really) add a `@FocusedValue` property to our app, and use that value to trigger a reply action from our command menu:

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

The focused value is optional and is `nil` when no view with a matching `.focusedValue` has focus. This means that we can disable the button when it won't do anything.

However, there is one last, important thing to consider. A window only becomes focused when a view within it is focused. In our case, where we just have texts and dividers, *the window will never be focused*.

This means that our custom command menu button will never be enabled, even if a window is selected:

![Disabled menu command]({{page.assets}}/no-focus.png)

If we now add a text field to the view and start editing it, the command menu button becomes enabled:

![Menu command enabled by text field]({{page.assets}}/focus-with-textfield.png)

If we have no focusable views, we can still fix this, by applying `.focusable()` to the entire view:

![Menu command enabled by code]({{page.assets}}/focus-with-code.png)

But adding this causes a focus effect to appear. To disable this, just add `.focusEffectDisabled()` to your view, and the focus will not be visual:

![Menu command enabled by code, without focus effect]({{page.assets}}/focus-without-effect.png)

We can now tap the button or use the keyboard shortcut to trigger the action for the selected window:

![A reply being shown in a single window]({{page.assets}}/reply.png)

If we now close all windows, the focus value becomes `nil` and the menu button becomes disabled. And with that, we can now consider ourselves to be menu bar experts.