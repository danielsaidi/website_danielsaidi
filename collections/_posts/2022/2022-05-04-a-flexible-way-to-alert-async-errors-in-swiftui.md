---
title:  A flexible way to handle async errors in SwiftUI
date:   2022-05-04 12:00:00 +0000
tags:   swiftui async-await error-handling

icon:   swiftui
tweet:  https://twitter.com/danielsaidi/status/1521909311471034373?s=20&t=wF1kbk5Nxm27t6vxQ1OeLQ

alert-post: https://danielsaidi.com/blog/2020/06/07/swiftui-alerts
---

In this post, let's take a look at how to handle async errors in a flexible and scalable way in SwiftUI. We'll cover both completion block- and async/await-based use cases.

{% include kankoda/data/open-source.html name="SwiftUIKit" %}


## TL;DR

This post describes an observable `AlertContext` that can used to present alerts from any place in the app, an `ErrorAlertConvertible` protocol that can be implemented by `Error` types that can map the error to an `Alert`, and an `ErrorAlerter` protocol to present errors.

Although the post contains a lot of text, the total amount of code is actually not that much. You can have a look at the already implemented code in [SwiftUIKit]({{project.url}}).



## The traditional way to show alerts

Consider the case where we use a standard `.alert` modifier to show an alert if something goes wrong. In it's easiest form, such a setup could look something like this:

```swift
struct MyView: View {

    @State 
    private var isAlertPresented = false

    enum MyError: Error {
        case somethingWentWrong
    }

    var body: some View {
        Button(action: doSomething) {
            Text("Do something")
        }.alert(isPresented: $isAlertPresented) {
            Alert(title: Text("Something went wrong"))
        }
    }
}

private extension MyView {

    func doSomething() {
        doSomethingAsync { error in
            isAlertPresented = error != nil
        }
    }

    /// Fake an async call to test what happens.
    func doSomethingAsync(completion: (Error?) -> Void) {
        completion(MyError.somethingWentWrong)
    }
}
```

Here, we fake an async call and sets an `isPresented` flag to `true` to simulate an error. It's super-simple and not applicable in real life, but you get the idea.

To create a more flexible and robust setup, let's come up with a way to have a single alert that can be used within the entire app, then find a way to trigger that alert in a single way.



## Setting up the alert

I have alreaty written about an easier way to handle alerts in SwiftUI in [this post]({{page.alert-post}}). You can read it for more information, but let's quickly just put it to work here as well.

Although we'll only use alerts in this post, let's create an observable class that can be used to manage any presentable content, such as alerts, full-screen covers, sheets, etc.:

```swift
open class PresentationContext<Content>: ObservableObject {
    
    public init() {}
    
    @Published
    public var isActive = false
    
    @Published
    public internal(set) var content: (() -> Content)? {
        didSet { isActive = content != nil }
    }
    
    public var isActiveBinding: Binding<Bool> {
        .init(get: { self.isActive },
              set: { self.isActive = $0 }
        )
    }
        
    public func dismiss() {
        isActive = false
    }
    
    public func presentContent(_ content: @autoclosure @escaping () -> Content) {
        self.content = content
    }
}
```

This context holds the active/presented state as well as a `content` builder that generates the content view that should be presented. 

Calling `presentContent(...)` sets the content builder, which in turn sets `isActive` to `true`, while calling `dismiss()` sets `isActive` to `false`. 

The code is not clean. The binding is called `isActiveBinding` instead of `isActive` and the presentation function `presentContent(...)` instead of `present(...)`. You'll soon see why.

We can now subclass `PresentationContext` to create a context for handling alerts:

```swift
public class AlertContext: PresentationContext<Alert> {
    
    public func present(_ alert: @autoclosure @escaping () -> Alert) {
        presentContent(alert())
    }
}
```

This class is super simple. By inheriting `PresentationContext` and binding it to `Alert`, we get a context that can be used to present alerts and alerts alone. 

The class also has a cleaner `present(...)` function that calls `presentContent(...)`. This is the function that is meant to be used, so depending on how you setup these contexts, you could make `presentContent` `internal` to avoid exposing it altogether. 

We'll also create a view modifier soon, that will use the `isActiveBinding`, which means that we don't have to expose that either.

Having a non-generic `presentContent` function in the base class also lets us avoid generic types by creating generic functions instead, for instance to present any view as a sheet:

```swift
public class SheetContext: PresentationContext<AnyView> {
    
    public func present<Sheet: View>(_ sheet: @autoclosure @escaping () -> Sheet) {
        presentContent(sheet().any())
    }
}
```

With these things in mind, I'm happy to have a noisy base class, while the subclasses that are meant to be used are cleaner and tighter.


## Binding the alert to the view

With `AlertContext` in place, we can now create a view modifier to bind a context to a view:

```swift
public extension View {
    
    func alert(_ context: AlertContext) -> some View {
        alert(
            isPresented: context.isActiveBinding,
            content: context.content ?? { Alert(title: Text("")) }
        )
    }
}
```

This lets us use `myView.alert(...)` just like before, but instead of a binding and a view or an `item` that was later introduced, we can provide a context to get a more flexible setup.


## Presenting an alert

With `AlertContext` and the `alert(...)` view modifier in place, we can clean up the code:

```swift
struct MyView: View {

    @StateObject
    private var alert = AlertContext()

    enum MyError: String, Error {
        case somethingWentWrong
    }

    var body: some View {
        Button(action: doSomething) {
            Text("Do something")
        }.alert(alert)
    }
}


private extension MyView {

    func doSomething() {
        doSomethingAsync { error in
            guard let error = error else { return }
            let title = Text(error.localizedDescription)
            alert.present(Alert(title: title))
        }
    }

    /**
     Fake an async call to test what happens.
     */
    func doSomethingAsync(completion: (Error?) -> Void) {
        completion(MyError.somethingWentWrong)
    }
}
```

Here, we create a `StateObject` context and apply it to our button, then call `.present(...)` to present an alert when our fake operation fails.

Although this is already more flexible, having this context gives us even more freedom. We could for instance inject a context from the outside, using `.environmentObject(...)`:

```swift
struct ParentView: View {

    @StateObject
    private var alert = AlertContext()
    
    var body: some View {
        MyView()
            .environmentObject(alert)
    }

}
```

which can be accessed by replacing the `@StateObject` property with `@EnvironmentObject`:

```swift
struct MyView: View {

    @EnvironmentObject
    private var alert: AlertContext
```

We can also inject it with the `MyView` initializer and set it up as an observed object instead:

```swift
struct ParentView: View {

    @StateObject
    private var alert = AlertContext()
    
    var body: some View {
        MyView(alert: alert)
    }
}
```

```swift
struct MyView: View {

    init(alert: AlertContext) {
        self._alert = ObservedObject(wrappedValue: alert)
    }
    
    @ObservedObject
    private var alert: AlertContext

    ...
}
```

Having this context gives us a lot more flexibility. In fact, we don't even have to bind it in `MyView`. We could just bind it within `ParentView` and just pass it into the view hierarchy:

```swift
struct ParentView: View {

    @StateObject
    private var alert = AlertContext()
    
    var body: some View {
        MyView(alert: alert)
            .alert(alert)
    }
}

struct MyView: View {

    init(alert: AlertContext) {
        self._alert = ObservedObject(wrappedValue: alert)
    }
    
    ...

    func alert(_ text: String) {
        alert.present(
            Alert(title: text)
        )
    }
}
```

With this rather long detour, and with this setup in place, we can now start looking at some interesting ways to present error alerts when operations fail.


## Alerting errors

We can now use `AlertContext` to present any error as an alert. Given a general `Error`, it could look something like this:

```swift
func alert(_ error: Error) {
    alert.present(
        Alert(title: error.localizedDescription)
    )
}
```

However, we can do better. Let's use protocols to get a more flexible way to handle alerts.

First, let's define a protocol that can be implemented by any error that can be presented as an alert within our apps:

```swift
protocol ErrorAlertConvertible: Error {
    
    var errorTitle: String { get }
    var errorMessage: String { get }
}
```

With the required properties, the protocol can be extended to create an alert like this:

```swift
extension ErrorAlertConvertible {
    
    var errorAlert: Alert {
        Alert(
            title: Text(errorTitle),
            message: Text(errorMessage),
            dismissButton: .default(Text("OK"))  // Use localized strings though
        )
    }
}
```

We can now use this protocol to define app- or domain-specific errors, for instance:

```swift
enum MyError: ErrorAlertConvertible {
    
    case general
    
    var errorTitle: String {
        switch self {
        case .general:
            return "Something went wrong"
        }
    }
    
    var errorMessage: String {
        switch self {
        case .general:
            return "Please try again"
        }
    }   
}
```

We can also create app-specific enums, where each case can be converted to an `Alert`.

Next, let's define a protocol that can be implemented by types that can present errors:

```swift
protocol ErrorAlerter {
    
    var alert: AlertContext { get }
}
```

With this single requirement, we can extend any type that implements `ErrorAlerter` with functions that try to perform async operations and automatically alert any errors that occur.

For instance, adding this function lets an alerter alert any errors:

```swift
@MainActor
extension ErrorAlerter {
    
    func alert(error: Error) {
        if let error = error as? ErrorAlertConvertible {
            return alert.present(error.errorAlert)
        }
        alert.present(
            Alert(
                title: Text(error.localizedDescription),
                dismissButton: .default(Text("OK"))
            )
        )
    }
}
```

Notice that since the function will change the context state, we should apply a `@MainActor` to the extension. This lets us use it with async/await, since we can just `await` the alert.

To support block-based operations, we could add a non-async function version as well:

```swift
extension ErrorAlerter {
    
    func alertAsync(error: Error) {
        DispatchQueue.main.async {
            alert(error: error)
        }
    }
}
```

With the `ErrorConvertible` and `ErrorAlerter` protocols in place, we're ready to put it all together. Let's start with a block-based example.


## Alert errors from block-based functions

Let's put `ErrorAlerter` to more work by adding an extension that can call any completion block-based operation and automatically alert any errors that occur:

```swift
extension ErrorAlerter {
    
    typealias BlockResult<ErrorType: Error> = Result<Void, ErrorType>
    typealias BlockCompletion<ErrorType: Error> = (BlockResult<ErrorType>) -> Void
    typealias BlockOperation<ErrorType: Error> = (BlockCompletion<ErrorType>) -> Void
    
    func tryWithErrorAlert<ErrorType: Error>(_ operation: @escaping BlockOperation<ErrorType>, completion: @escaping BlockCompletion<ErrorType>) {
        operation { result in
            switch result {
            case .failure(let error): alertAsync(error: error)
            case .success: break
            }
            completion(result)
        }
    }
}
```

Here, we define a generic `BlockResult` that we use in a generic `BlockCompletion`, which we then use in a generic `BlockOperation`. This makes the rest of the code more readable.

We then define a `tryWithErrorAlert` function that can use any parameterless function. If an error occurs, it calls `alertAsync`. If the error implements `ErrorAlertConvertible`, it has full control over how it will be presented, otherwise the localized description will be used.

Note that the provided `completion` is called as well, to give us a way to handle the result. We can handle the error as well, we just don't have to care about alerting it.

With this, any view with an `AlertContext` can now present any error using the same alert within the entire app, by just implementing the `ErrorAlerter` protocol. Pretty neat, right?

Let's now look at how to achieve the same with Swift concurrency and async/await.


## Alert errors from async functions

The code becomes cleaner with async/await, since we don't need results, completions, etc. An async alternative to the block-based `tryWithErrorAlert` function looks like this:

```swift
typealias AsyncOperation = () async throws -> Void

func tryWithErrorAlert(_ operation: @escaping AsyncOperation) {
    Task {
        do {
            try await operation()
        } catch {
            await alert(error: error)
        }
    }
}
```

This means that we can perform any parameterless async throwing function and alert any error that is thrown, using the async alert function in the `@MainActor` extension. 

Using this approach, and with `MyError` implementing `ErrorAlertConvertible` from before, the initial example could look like this, if it implements `ErrorAlerter`:

```swift
struct MyView: View, ErrorAlerter {

    @StateObject
    var alert = AlertContext()

    var body: some View {
        Button(action: doSomething) {
            Text("Do something")
        }.alert(alert)
    }
}

extension MyView {

    func doSomething() {
        tryWithErrorAlert(doSomethingAsync)
    }

    /// Fake an async call to test what happens.
    func doSomethingAsync() async throws {
        throw(MyError.general)
    }
}
```

We can also inject the alert context from the outside or into the rest of the view hierarchy, and use `tryWithErrorAlert` in any view that implements `ErrorAlerter`.

This is a lot cleaner than the block-based approach, but if your app targets an iOS version that doesn't support async/await, you may have to stick with the block-based one for now.


## Conclusion

In this article, we created an observable `AlertContext` that can be passed around in an app to present alerts from anywhere with a single binding.

We then added an `ErrorAlertConvertible` protocol that can be implemented by any `Error` that can generate an `Alert`, and an `ErrorAlerter` protocol that adds extra functionality to anything with an `AlertContext` instance.

Although the post contains a lot of text, the total amount of code is actually not that much. You can have a look in [SwiftUIKit]({{project.url}}) and use it in your own apps.

I hope you find the approach as usable as I do. I'd love to hear your thoughts on this, so don't hesitate to comment or reach out with any thoughts you may have.