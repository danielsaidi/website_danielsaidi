---
title:  "Perform biometric authentication with FaceID or TouchID"
date:   2020-04-28 00:00:00 +0100
tags:   swiftui swift local-authentication ios ipados macos authentication

lib:    https://github.com/danielsaidi/SwiftKit
source: https://github.com/danielsaidi/SwiftKit/Sources/SwiftKit/Authentication
tests:  https://github.com/danielsaidi/SwiftKit/Tests/SwiftKit/Authentication
---

In this post, we'll look at how to perform local biometric authentication with `FaceID` or `TouchID` on Apple's various platforms, using the `LocalAuthentication` framework.


## The basics

Biometric information is a powerful way to protect sensitive or valuable information. If your app stores this kind of data, you can use native biometric authentication metchanisms to protect it.

Performing biometric authentication with `FaceID` or `TouchID` is really easy and involves very little code. You just have to import the `LocalAuthentication` framework and use `LAPolicy` and `LAContext` to get the job done.

`LAPolicy` describes what kind of authentication to allow. `deviceOwnerAuthenticationWithBiometrics` requires biometric information like `FaceID` or `TouchID`, while `deviceOwnerAuthentication` also accepts a passcode.

You can then use `LAContext` to perform an authentication with your policy of choice. First use `canEvaluatePolicy` to check if the device can handle the policy:

```swift
let policy = LAPolicy.deviceOwnerAuthenticationWithBiometrics
var error: NSError?
let result = LAContext().canEvaluatePolicy(policy, error: &error)
```

The function takes an error pointer (not that Swifty) and your policy, then returns whether or not the device can use the policy to authenticate the user.

Since most phones support either `FaceID` or `TouchID`, most phones will be able to perform biometric authentication. The same goes for most iPads and MacBooks, since most come with `TouchID`. However, there are older and more basic device types that lack both `FaceID` and `TouchID`. You must take this into consideration when choosing which policy to use.

To actually perform the authentication, you just have to call `evaluatePolicy` on the context with a localized reason that describes to the user why your app needs to authentication her/him:

```swift
let policy = LAPolicy.deviceOwnerAuthenticationWithBiometrics
let reason = "The app needs your biometric information to unlock this part of the app"
let result = LAContext().evaluatePolicy(policy, localizedReason: reason) { result, error in
    // Handle the result or error
}
```

...and that's basically it. The above code is all you need to perform biometric authentication on your iPhone or iPad. I think Apple deserves praise for making these powerful tools so accessible.


## Taking things further

I have added various authentication services to my (so far brand new and still pretty empty) [SwiftKit]({{page.lib}}) library, which includes additional, reusable functionality for Swift.

This service model is abstract and allows for dependency injection, composition, testing and mocking and all those good things, so it's a good fit for systems where you need more flexibility than just calling in the code above directly.

You can find the source code [here]({{page.source}}) and the unit tests [here]({{page.tests}}). Feel free to give it a try and let me know what you think.