---
title:  Extending local authentication with async support
date:   2022-04-29 07:00:00 +0100
tags:   quick-tip swift

icon:   swift
assets: /assets/blog/2022/2022-04-29/

lib:    https://github.com/danielsaidi/SwiftKit
post:   https://danielsaidi.com/blog/2020/04/27/local-biometric-authentication
---

In this post, let's take a look at how to extend the `LocalAuthentication` framework with an `async` way to perform local authentication.

Apple's `LocalAuthentication` framework makes it easy to perform local authentication for a user in many ways, e.g. with biometric authentication using TouchID or FaceID, which I wrote about in [this post]({{page.post}}) a while back. You can also let users use their passcode, Apple Watch etc.

To achieve this, you use the `LAContext` class, which however only offers a completion block-based way to perform authentication. This means that your code will look something like this:

```swift
let policy = LAPolicy.deviceOwnerAuthenticationWithBiometrics
let reason = "The app needs your biometric information to unlock this feature."
let result = LAContext().evaluatePolicy(policy, localizedReason: reason) { result, error in
    // Handle the result or error
}
```

This code is still *asynchronous*, but doesn't use the new `async` capabilities of Swift, which means that it does't play all that way with other async functions.

Apple will probably extend `LAContext` with more async capabilities soon enough, but until they do, you can easily do it yourself by extending `LAContext` with a throwing async function:

```swift
@available(iOS 15.0, macOS 12.0, *)
extension LAContext {
    
    func evaluatePolicy(_ policy: LAPolicy, localizedReason reason: String) async throws -> Bool {
        return try await withCheckedThrowingContinuation { cont in
            LAContext().evaluatePolicy(policy, localizedReason: reason) { result, error in
                if let error = error { return cont.resume(throwing: error) }
                cont.resume(returning: result)
            }
        }
    }
}
```

By wrapping the code in a `withCheckedThrowingContinuation`, you can create a throwing async function that can be called like this:

```swift
let context = LAContext()
let policy = LAPolicy.deviceOwnerAuthenticationWithBiometrics
let result = try await context.evaluatePolicy(.policy, reason: "...")
```

I think that this is a lot cleaner, and easier to use in other async functions.

This approach can be used for all kind of functionality that is not yet extended to support async/await. I'm really glad that Apple made it so easy, since it will take time for them to add this support everywhere.


## Source code

I have added this extension to my [SwiftKit]({{page.lib}}) library. Feel free to try it out and let me know what you think!