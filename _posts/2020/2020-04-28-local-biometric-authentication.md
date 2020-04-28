---
title:  "Performing local biometric authentication"
date:   2020-04-28 00:00:00 +0100
tags:   swiftui swift local-authentication ios ipados

lib:    https://github.com/danielsaidi/SwiftKit
source: https://github.com/danielsaidi/SwiftKit/Sources/SwiftKit/Authentication
tests:  https://github.com/danielsaidi/SwiftKit/Tests/SwiftKit/Authentication
---

In this post, we'll look at how to perform local biometric authentication with `FaceID` or `TouchID` on iOS or ipadOS, using the `LocalAuthentication` framework.


## The basics

Biometric information is a powerful way to protect sensitive or valuable information. If your app stores this kind of data, you can use the built-in biometric authentication metchanisms to protect it.

Performing biometric authentication with `FaceID` or `TouchID` is really easy and involves very little code. You just have to import the `LocalAuthentication` framework and use `LAPolicy` and `LAContext` to get the job done.

`LAPolicy` describes what kind of authentication that should be allowed. `deviceOwnerAuthenticationWithBiometrics` requires biometric authentication like `FaceID` or `TouchID`, while `deviceOwnerAuthentication` also accepts a passcode.

You can then use `LAContext` to perform authentication with your policy of choice. First use `canEvaluatePolicy` to check if the device can handle the policy:

```swift
let policy = LAPolicy.deviceOwnerAuthenticationWithBiometrics
var error: NSError?
let result = LAContext().canEvaluatePolicy(policy, error: &error)
```

The function takes an error pointer (not that Swifty) and your policy, then returns whether or not the device can use the policy to authenticate the user.

To perform the authentication, you just have to call `evaluatePolicy` on the context with a localized reason that describes to the user why your app needs to perform the authentication:

```swift
let policy = LAPolicy.deviceOwnerAuthenticationWithBiometrics
let reason = "The app needs your biometric information to unlock this part of the app"
let result = LAContext().evaluatePolicy(policy, localizedReason: reason) { result, error in
    // Handle the result or error
}
```

That's basically it. The above code is all you really need to perform biometric authentication on your iPhone or iPad.


## Code

I have added various authentication services to my [SwiftKit]({{page.lib}}) library, which includes a bunch of additional functionality for Swift.

These services implement biometric authentication as well as ways to cache the result of any authentication service, to avoid having to trigger the underlying authentication mechanism every time.

The service model is abstract to allow for extensive testing and mocking, as well as using the services in well-tested systems. Feel free to give it a try and let me know what you think.

You can find the source code [here]({{page.source}}) and the unit tests [here]({{page.tests}}).