---
title:  Automatically alerting async operation errors in SwiftUI
date:   2026-05-15 08:00:00 +0100
tags:   swiftui

image-show: 0
image: /assets/blog/26/0515/image.jpg

sdk:  https://github.com/danielsaidi/PresentationKit

toot: https://mastodon.social/@danielsaidi/116574186078049253
bsky: https://bsky.app/profile/danielsaidi.bsky.social/post/3mlteuq7bus2h
---


When building SwiftUI apps, it's common to perform async operations that can throw errors when something goes wrong. This post shows how you can alert them gracefully, without a lot of code.


## PresentationKit

The approach in this code uses [PresentationKit]({{page.sdk}}), which is an open-source library I created to handle alerts, modals, and sheets. Check out the project for more handy examples.


## The Building Blocks

I wanted to remove as much code as possible from the call site, and rely on handy, reusable utilities from PresentationKit. This will leave your code clean and easy to understand.

At the core lies a `PresentationContext` class, which is just an observable class with a value. This value can be used with the regular `alert`, `fullScreenCover`, and `sheet` view modifiers, but the library also has context-specific versions for even easier use.

PresentationKit has an `ErrorAlerter` protocol that can be implemented by any types that should be able to alert async errors. All conforming types get access to a `tryWithErrorAlert` function, that can perform any throwing async operation and automatically alert any errors that are thrown.

PresentationKit also has an `AlertableError` protocol for error types that can describe themselves as an `AlertMessage`, with a title, an optional message view, and optional buttons. There is a dedicated `alert` view modifier that automatically performs this mapping when alerting an error.

Together, these building blocks let you write throwing operations normally, and trust that errors will surface to the user without any extra wiring.


## Example

To make a SwiftUI view conform to `ErrorAlerter`, add a `PresentationContext<Error>` state property and attach the `.alert(for:)` modifier to the view:

```swift
struct MyView: View, @MainActor ErrorAlerter {

    @State var errorContext = PresentationContext<Error>()

    var body: some View {
        List {
            // ...
        }
        .alert(for: $errorContext)
    }
}
```

That's basically all you have to do. You can now call `tryWithErrorAlert` to perform async operations and automatically alert any errors that are thrown.

Implementing `AlertableError` is easy, and just involves specifying an `alertMessage` property:

```swift
enum MyError: String, AlertableError {
    case notFound
    case unauthorized

    var alertMessage: AlertMessage<AnyView, AnyView> {
        switch self {
        case .notFound: ...
        case .unauthorized: ...
        }
    }
}
```

With this in place, this is how easy it is to set up an `ErrorAlerter` view with a custom `AlertableError`:

```swift
enum DataError: String, AlertableError {
    case networkUnavailable
    case serverError

    var alertMessage: AlertMessage<AnyView, AnyView> {
        AlertMessage(
            title: "A \(rawValue.replacingOccurrences(of: "([A-Z])", with: " $1", options: .regularExpression)) error occurred",
            message: { Text("Please try again later.") },
            actions: { Button("OK") {} }
        )
    }
}

struct ContentView: View, @MainActor ErrorAlerter {

    @State var errorContext = PresentationContext<Error>()

    var body: some View {
        List {
            Button("Fetch from network") {
                tryWithErrorAlert {
                    try await fetchData()
                }
            }
            Button("Trigger custom error") {
                tryWithErrorAlert {
                    throw DataError.networkUnavailable
                }
            }
            Button("Trigger generic error") {
                tryWithErrorAlert {
                    throw URLError(.badServerResponse)
                }
            }
        }
        .alert(for: $errorContext)
    }

    func fetchData() async throws {
        // Your async data fetching logic
    }
}
```


## Considerations

The `.alert(for:)` view modifier automatically maps all errors to an `AlertMessage`. All `AlertableError` types use their well-defined alert messages, while all other errors will use the localizable description and a standard "OK" button.

If you want full control of all non-alertable errors, you can use the `alert(for:content:)` modifier and customize the errors message.