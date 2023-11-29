---
title:  How to customize the macOS About Panel in SwiftUI
date:   2023-11-28 10:00:00 +0000
tags:   swiftui macos

image:  /assets/blog/headers/231123.jpg
assets: /assets/blog/2023/1123/

article: /blog/2023/11/22/how-to-customize-the-macos-menu-bar-in-swiftui
tweet:  https://x.com/danielsaidi/status/1729767139966402628?s=20
toot:   https://mastodon.social/@danielsaidi/111492701147385921
---

In this post, we'll take a look at how to customize the macOS about panel for a SwiftUI app, to let us show custom content.

![Blog header image]({{page.image}})

If we create a brand new SwiftUI app in Xcode and run it, the main menu has an "About..." menu item that opens an "about panel" that has information about the app:

![The default about panel]({{page.assets}}/default.png)

This panel will by default show the app icon, project name, build number and version number. If we add an app icon to our app, it will automatically appear, without any code needed. Pretty nice, right?

Since the project is called "MyApp", this is also the default display name for the app. Let's change this to "My App" in Project Settings to see what happens:

![The default about panel shows project name, not display name]({{page.assets}}/name.png)

Hmmmm, that's strange. The top-level menu item and the about panel still says "MyApp", but the menu button now says "About My App". Seems like only the menu button honors the display name.

While there may be ways to fix this with the standard build configuration, let's take it as a reason to look at how we can customize the About Panel to show custom content.


## How to customize the macOS about panel

To customize the About Panel, we will use the same techniques as we looked at in [the previous post]({{page.article}}) on how to customize the main menu commands of an app.

In short, we need to replace the current about button with a custom one that calls an `NSApplication` funtion called `orderFrontStandardAboutPanel`, that opens an About Panel with custom options:

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

The code above replaces the `.appInfo` menu item with a custom menu item that opens an About Panel that overrides the app name. The result looks like this:

![A custom about panel]({{page.assets}}/custom.png)

We can use the options dictionary to override properties like `applicationInfo`, `applicationName` and `applicationVersion` (version string), as well as `credits` and `version` (build number):

![Customization options]({{page.assets}}/intellisense.png)

The version row disappears if you set both `applicationVersion` and `version` to empty strings and adjusts itself if you set either value to an empty string.

The `credits` value is presented below the version. Notice that this is an *attributed string*. The app will actually *crash* if you set it to a plain string. 

Instead, you can set it like this:

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

Yikes, I don't like that indentation arrow! Let's specify options in another way to make the code cleaner:

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
                            credits: "..."
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

If we run the app again, we now have custom credits with a custom style in place as well:

![Custom credits]({{page.assets}}/credits.png)

To simplify this mote, we can create a custom `AboutPanelCommand` that can be reused in many apps:

```swift
public struct AboutPanelCommand: Commands {
    
    public init(
        title: String,
        applicationName: String = Bundle.main.displayName,
        credits: String? = nil
    ) {
        let options: [NSApplication.AboutPanelOptionKey: Any]
        if let credits {
            options = [
                .applicationName: applicationName,
                .credits: NSAttributedString(
                    string: credits,
                    attributes: [
                        .foregroundColor: NSColor.secondaryLabelColor,
                        .font: NSFont.systemFont(ofSize: NSFont.smallSystemFontSize)
                    ]
                )
            ]
        } else {
            options = [.applicationName: applicationName]
        }
        self.init(title: title, options: options)
    }
    
    public init(
        title: String,
        options: [NSApplication.AboutPanelOptionKey: Any]
    ) {
        self.title = title
        self.options = options
    }
    
    private let title: String
    private let options: [NSApplication.AboutPanelOptionKey: Any]
    
    public var body: some Commands {
        CommandGroup(replacing: .appInfo) {
            Button(title) {
                NSApplication.shared
                    .orderFrontStandardAboutPanel(options: options)
            }
        }
    }
}
```

This lets us reduce the amount of code in our app to the following:

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
            AboutPanelCommand(
                title: "About My App",
                credits: "This amazing app was created in SwiftUI...."
            )
        }
    }
}
```

In the code above, we use the simplified initializer to pass in credits as plain text. If we want to specify a custom atttributed string, we have that option as well. 


## Conclusion

I really like SwiftUI's APIs for working with menu commands, but the About Panel stuff is a bit hidden. I really hope that they bring a more native approach for this in the next major bump.

With that, I'd love to see the limitations of the attributed string capabilities of the About Panel. The first to make Doom run in the credits text field (hey, someone made it run in the touch bar) wins a prize.