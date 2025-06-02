---
title:  Swift Enums vs Structs - Picking the Best Tool for the Job
date:   2025-05-26 07:00:00 +0000
tags:   swift

assets: /assets/blog/25/0526/
image:  /assets/blog/25/0526/image.jpg
header: /assets/blog/25/0526/header.jpg

redirect_from: /blog/2025/05/26/Swift-Enums-vs-Structs-Picking-the-Best-Tool-for-the-Job

bsky: https://bsky.app/profile/danielsaidi.bsky.social/post/3lq37rdslqc2y
toot: https://mastodon.social/@danielsaidi/114574231021749921
linkedin: https://www.linkedin.com/posts/danielsaidi_swift-enums-vs-structs-picking-the-best-activity-7332750997615804416-sZuF?utm_source=share&utm_medium=member_desktop&rcm=ACoAAABnwcEBLiSBDEydkcyl5DHukvfGrCEKbX0
---

When designing state in Swift, choosing between enums and structs can significantly impact your code's flexibility and structure. Let's explore when to use each approach with examples.

## Enums: Perfect for Finite State

Enums excel when you have a clearly defined, finite set of states that won't change frequently. They provide compile-time safety and exhaustive switching capabilities.

For instance, here's a content view that uses a `LoadingState` enum to manage its loading state:

```swift
struct SomeModel {

    var title: String
}

enum LoadingState {
    case idle
    case loading
    case success(SomeModel)
    case failure(Error)
}

struct ContentView: View {

    @State var loadingState: LoadingState = .idle
    
    var body: some View {
        VStack {
            Text("Details")
            switch loadingState {
            case .idle:
                Text("Ready to load")
            case .loading:
                ProgressView("Loading...")
            case .success(let model):
                Text("Success: \(model.title)")
            case .failure(let error):
                Text("Error: \(error.localizedDescription)")
                    .foregroundColor(.red)
            }
        }
        .task {
            loadData()
        }
    }
    
    private func loadData() {
        loadingState = .loading
        do {
            let model = try ... // Load data here
            loadingState = .success(model)
        } catch {
            loadingState = .failure(error)
        }
    }
}
```

We can extend the enum with more functionality, make it conform to `View`, make it generic to let it use any model type, etc., but that's outside the scope of this post.

The key advantage here is **exhaustive switching** - the compiler ensures you handle every possible case, which makes your code more robust.



## Structs: Extensible and Flexible

Structs shine when you need extensibility and want to allow additional cases to be added over time. This is particularly useful for styles and configurations.

For instance, here we define a basic message style with a few predefined values that can be injected into the environment and used to style a custom message view:

```swift
struct MessageStyle {
    let backgroundColor: Color
    let foregroundColor: Color
}

// MARK: - Predefined styles

extension MessageStyle {
    
    static let primary = MessageStyle(
        backgroundColor: .blue,
        foregroundColor: .white
    )
    
    static let secondary = MessageStyle(
        backgroundColor: .gray,
        foregroundColor: .primary
    )
    
    static let destructive = MessageStyle(
        backgroundColor: .red,
        foregroundColor: .white
    )
}

// MARK: - Extensions

extension MessageStyle {

    var cornerRadius: Double { 10 }
}

// MARK: - Environment Value

extension EnvironmentValues {

    @Entry var messageStyle = MessageStyle.primary
}

extension View {

    func messageStyle(_ style: MessageStyle) -> some View {
        self.environment(\.messageStyle, style)
    }
}

// MARK: - Message View

struct Message: View {
    let title: String

    @Environment(\.messageStyle) var style
    
    var body: some View {
        Text(title)
            .foregroundColor(style.foregroundColor)
            .background(
                style.backgroundColor, 
                in: .rect(cornerRadius: style.cornerRadius)
            )
    }
}
```

With all this in place, we can easily apply any of the predefined styles or create custom styles:

```swift
struct MessageStyleExample: View {
    var body: some View {
        VStack(spacing: 16) {
            Message(title: "Primary")
                .messageStyle(.primary)
            Message(title: "Secondary")
                .messageStyle(.secondary)
            Message(title: "Delete")
                .messageStyle(.destructive)
            
            // Developers can create custom styles
            Message(title: "Custom")
                .messageStyle(
                    MessageStyle(
                        backgroundColor: .purple,
                        foregroundColor: .white
                    )
                )
        }
    }
}
```

The key here is **extensibility** - you can define a standard set of styles and configurations, but still allow custom values. This is perfect for cases where you want to allow customizations.

With all the things that both enums and structs can do, there's one big difference between the two.



## The Struct Limitation: No Exhaustive Switching

While you can switch over equatable structs, you can't exhaust the switch like you can with enums.

For instance, consider this equatable `NetworkState` struct, which has a couple of predefined values:

```swift
struct NetworkState: Equatable {
    let connectionType: String
    let speed: Double?
    
    static let offline = NetworkState(connectionType: "Offline", speed: nil)
    static let wifi = NetworkState(connectionType: "WiFi", speed: 100.0)
    static let cellular = NetworkState(connectionType: "Cellular", speed: 25.0)
}

struct NetworkStatusView: View {
    @State private var networkState = NetworkState.offline
    
    var body: some View {
        switch networkState {
        case .offline: 
            Label(networkState.connectionType, systemImage: "wifi.slash")
                .foregroundColor(.red)
        case .wifi:
            Label(networkState.connectionType, systemImage: "wifi")
                .foregroundColor(.green)
        case .cellular:
            Label(networkState.connectionType, systemImage: "antenna.radiowaves.left.and.right")
                .foregroundColor(.orange)
        default:
            // Now what?
        }
    }
}
```

Unlike an enum, a struct can't be exhausted. So, in this case, we need some kind of fallback case to handle all unknown states.


## Extending enums and structs

We can extend both enums and structs with additional functionality, which can either be computed properties for all values (like the message style corner radius) or specific for each case/value.

This is easy with enums, since we know all potential values. For instance, `LoadingState` could have a `LoadingState+UI` extension file with UI extensions, like moving the view from the content view:

```swift
extension LoadingState: View {
    
    @ViewBuilder
    var body: some View {
        switch self {
        case .idle:
            Text("Ready to load")
        case .loading:
            ProgressView("Loading...")
        case .success(let model):
            Text("Success: \(model.title)")
        case .failure(let error):
            Text("Error: \(error.localizedDescription)")
                .foregroundColor(.red)
        }
    }
}
```

This could be broken up even more:

```swift
extension LoadingState: View {
    
    @ViewBuilder
    var body: some View {
        switch self {
        case .loading: ProgressView(title)
        default: Text(title).foregroundColor(foregroundColor)
        }
    }

    var title: String {
        switch self {
        case .idle: "Ready to load"
        case .loading: "Loading..."
        case .success(let model): model.title
        case .failure(let error): "Error: \(error.localizedDescription)"
        }
    }

    var foregroundColor: Color? {
        switch self {
        case .failure: .red
        default: nil
        }
    }
}
```

This would allow us to clean up the content view from before, to just look like this:

```swift
struct ContentView: View {

    @State var loadingState: LoadingState = .idle
    
    var body: some View {
        VStack {
            Text("Details")
            loadingState
        }
        .task {
            loadData()
        }
    }
    
    private func loadData() {
        ...
    }
}
```

In this case, I think it'd make more sense to create a `LoadingStateView` that takes a `LoadingState` and not make the state also "be" a view, but you get the idea.

Extending a struct is a bit different, since there is no way to know about all potential values. For the `NetworkState`, we would have to derive information in other ways, for instance:

```swift
extension NetworkState {

    // Here we could either do a comparison, or check the speed
    var isOffline: Bool {
        self == .offline    // or...
        speed == nil
    }
}
```

The difference here is semantic. Since we can define many state values with `nil` speed, this struct should define what that means. In this case, perhaps renaming the property is a better alternative?

```swift
extension NetworkState {

    // Here we could either do a comparison, or check the speed
    var isConnected: Bool {
        guard let speed else { return false }
        return speed > 0
    }
}
```

We could use the same way to add more properties, like `hasFastNetworkConnection` (we won't, but let me just do it to illustrate the concept):

```swift
extension NetworkState {

    // Here we could either do a comparison, or check the speed
    var hasFastNetworkConnection: Bool {
        guard let speed else { return false }
        return speed > 25
    }
}
```

We can now use this to implement UI-related extensions, like we did with the loading state enum:

```swift
extension NetworkState {

    var foregroundColor: Color {
        guard isConnected else { return .red }
        return hasFastNetworkConnection ? .green : .orange
    }

    var symbolName: String {
        switch self {
        case .offline:  "wifi.slash"
        case .wifi: "wifi"
        case .cellular: "antenna.radiowaves.left.and.right"
        default: "questionmark"     // Or derive a symbol based on various values
        }
    }
}
```

With these properties, we can use the struct's more fluent information to implement `View`, rather than switching over all known enum cases:

```swift
extension NetworkState: View {

    @ViewBuilder
    var body: some View {
        Label(connectionType, systemImage: symbolName)
            .foregroundColor(foregroundColor)
    }
}
```

So while enums and structs are different, you can use them in very similar ways. You can even use private struct initializers to make it impossible for developers to create more values.



## Controlling Struct Extensibility with Private Initializers

You can limit struct extensibility by making the initializer private, or internal in a Swift Package. This will force developers to use predefined cases:

```swift
struct Theme: Equatable {

    let primaryColor: Color
    let secondaryColor: Color
    let accentColor: Color
    
    // Private initializer prevents arbitrary theme creation
    private init(primaryColor: Color, secondaryColor: Color, accentColor: Color) {
        self.primaryColor = primaryColor
        self.secondaryColor = secondaryColor
        self.accentColor = accentColor
    }
    
    // Controlled, predefined themes
    static let light = Theme(
        primaryColor: .black,
        secondaryColor: .gray,
        accentColor: .blue
    )
    
    static let dark = Theme(
        primaryColor: .white,
        secondaryColor: .gray,
        accentColor: .orange
    )
    
    static let highContrast = Theme(
        primaryColor: .black,
        secondaryColor: .black,
        accentColor: .yellow
    )
}
```

Although we still have to define a `default` case when switching over a struct, we at least know that this will never happen:

```swift
switch theme {
    case .light: ...
    case .dark: ...
    case .highContrast ...
    default: ... // This can't happen
}
```

We must be careful with `default` struct cases, though, since the compiler won't fail if we add more predefined values to a struct. Compare this with an enum, where the compiler can use exhaustive switching to detect if we forget to add a case for our new value.


## Mixing enums and structs

Just because we decide to go with an enum over a struct, or vice versa, we can still use the other to simplify things.

For instance, I find it very tedious to write switches for enums that have many computed properties. Consider that the `Theme` from above would have been an enum instead of a struct:

```swift
enum Theme {
    case light, dark, highContrast

    var primaryColor: Color {
        switch self {
            case .light: .black
            case .dark: .white
            case .highContrast: .black
        }
    }

    var secondaryColor: Color {
        switch self {
            case .light: .gray
            case .dark: .gray
            case .highContrast: .black
        }
    }

    // etc...
}
```

Imagine an enum with many extensions. This excessive switching will become a problem, especially when you add a new enum case and every computed property crashes because of the missing case.

In this case, we could still use an enum, but also add a "view style" to handle all view-related values:

```swift
extension Theme {

    struct ViewStyle {
        let primaryColor: Color
        let secondaryColor: Color
        let accentColor: Color
    }
}

enum Theme {
    var viewStyle: ViewStyle {
        switch self {
            case .light: ViewStyle(primaryColor: .black, ...)
            case .dark: ViewStyle(primaryColor: .white, ...)
            case .highContrast: ViewStyle(primaryColor: .black, ...)
        }
    }
}
```

Likewise, a struct can use enums to limit the number of available states even though the struct itself can have an unlimited amount of unique values. 

For instance, we could refactor the `NetworkState` to still be a struct to allow unique values, but use enum-based properties to make extensions easier to manage:

```swift
struct NetworkState: Equatable {
    let name: String
    let speed: Double?
    
    static let offline = NetworkState(name: "Offline", speed: nil)
    static let wifi = NetworkState(name: "WiFi", speed: 100.0)
    static let cellular = NetworkState(name: "Cellular", speed: 25.0)
}

extension NetworkState {

    enum ConnectionType {
        case none, slow, fast
    }

    var connectionType: ConnectionType {
        guard let speed else { return .none }
        return speed > 25 ? .fast : .slow
    }
}

extension NetworkState {

    var foregroundColor: Color {
        switch connectionType {
        case .none: .red
        case .slow: .orange
        case .fast: .green
        }
    }
}
```

So as you can see, you can mix and match enums and structs to get the best of both worlds, based on your needs in each specific situation.


## When to Choose Which

Since you can achieve most of the same things with both enums and structs, I think the choice boils down to if you want a type to represent a finite set of *options*, or an extendable set of *values*.

**Choose Enums when:**
- You have a finite, well-defined set of states
- You want compile-time exhaustiveness checking
- States are mutually exclusive
- You need pattern matching with associated values

**Choose Structs when:**
- You need extensibility for future cases
- You're building a configuration or styling system
- You want to allow custom variations
- You need to store complex state with multiple properties

Both approaches have their place in Swift development. Enums provide safety and clarity for state management, while structs offer flexibility for extensible systems. 

Choose based on whether you prioritize compile-time safety or runtime flexibility, and remember that you can extend both with the other, should you need it.