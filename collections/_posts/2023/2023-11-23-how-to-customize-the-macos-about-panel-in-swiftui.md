---
title:  How to customize the macOS about panel in SwiftUI
date:   2023-11-23 10:00:00 +0000
tags:   swiftui macos

image:  /assets/blog/headers/231123.jpg
assets: /assets/blog/2023/1123/

article: /blog/2023/11/22/how-to-customize-the-macos-menu-bar-in-swiftui
---

In this post, we'll take a look at how to customize the macOS about panel for a SwiftUI app, to let us show custom content.

![Blog header image]({{page.image}})

If we create a brand new SwiftUI app in Xcode and run it, the main menu has an "About..." menu item that opens an "about panel" that has information about the app:

![The default about panel]({{page.assets}}/default.png)

This panel will by default show the app icon, project name, build number and version number. If we add an app icon to our app, it will automatically appear, without any code needed. Pretty nice, right?

Since the project is called "MyApp", this is also the default display name for the app. Let's change this to "My App" and see what happens:

![The default about panel shows project name, not display name]({{page.assets}}/name.png)

That's strange. The top-level menu item and the about panel still says "MyApp", while the menu button says "About My App". So only the menu button honors the display name.

While there may be ways to fix this with the build configuration, let's take it as a reason to look at how we can customize the about panel with SwiftUI.


## How to customize the macOS about panel

To customize the macOS about panel, we will use the same techniques as we looked at in [yesterday's post]({{page.article}}) - menu commands.

In short, we need to replace the current menu button with a custom one, that calls an action that opens a custom about panel:

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
            CommandGroup(replacing: .appInfo) {
               Button("About My App") {
                    NSApplication.shared
                       .orderFrontStandardAboutPanel(
                        options: [.applicationName: "My App"]
                    )
                }
            }
        }
    }
}
```

In the code above, we open an about panel that overrides the app name. The result looks like this:

![A custom about panel]({{page.assets}}/custom.png)

We can override app icon, name and version (1.2.3), as well as credits and version (the build number):

![Customization options]({{page.assets}}/intellisense.png)

The version row disappears if you set both `applicationVersion` and `version` to empty strings and will adjust if you set either value to an empty string.

The `credits` value is presented below the version. Notice that this is an **attributed string** and that the app will crash if you set it to a plain string. Instead, you can set it like this:

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
            CommandGroup(replacing: .appInfo) {
               Button("About My App") {
                    NSApplication.shared
                       .orderFrontStandardAboutPanel(
                        options: [
                            .applicationName: "My App",
                            .credits: NSAttributedString(
                                string: "This amazing app was created in SwiftUI.\nCopyright ©2023. No rights reserved.",
                                attributes: [
                                    .foregroundColor: NSColor.secondaryLabelColor,
                                    .font: NSFont.systemFont(ofSize: NSFont.smallSystemFontSize)
                                ]
                            )
                        ]
                    )
                }
            }
        }
    }
}
```

I don't like that indentation one bit. Let's specify our options in another way to make the code cleaner:

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
            CommandGroup(replacing: .appInfo) {
               Button("About My App") {
                    NSApplication.shared
                       .orderFrontStandardAboutPanel(
                        options: .basic(
                            applicationName: "My App",
                            credits: """
This amazing app was created in SwiftUI.
Copyright ©2023. No rights reserved.
"""
                        )
                    )
                }
            }
        }
    }
}

public extension Dictionary 
    where Key == NSApplication.AboutPanelOptionKey, Value == Any {
    
    static func basic(
        applicationName: String,
        credits: String
    ) -> Self {
        [
            .applicationName: applicationName,
            .credits: NSAttributedString(
                string: credits,
                attributes: [
                    .foregroundColor: NSColor.secondaryLabelColor,
                    .font: NSFont.systemFont(ofSize: NSFont.smallSystemFontSize)
                ]
            )
        ]
    }
}
```

If we run this app again, we now have custom credits in place as well:

![Custom credits]({{page.assets}}/credits.png)

Since `credits` is an attributed string, I'm sure you can do many crazy things with it. The first one who makes Doom run in this window gets a prize (they made it run in the touch bar, so it should be doable).