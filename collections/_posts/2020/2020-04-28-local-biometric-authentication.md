---
title: Perform biometric authentication with FaceID or TouchID
date:  2020-04-28 00:00:00 +0100
tags:  quick-tip swift authentication
icon:  swift

lib:    https://github.com/danielsaidi/SwiftKit
---

In this post, we'll look at how to perform local biometric user authentication with FaceID or TouchID on Apple's various platforms, using the `LocalAuthentication` framework.


## The basics

Biometric information is a powerful way to protect sensitive or valuable information. If your app stores this kind of data, you can use native biometric authentication to protect it.

Performing biometric authentication with FaceID or TouchID is really easy and involves very little code. You just have to import `LocalAuthentication` and use `LAPolicy` and `LAContext` to get it done.

You can use `LAPolicy` to describe the authentication to use. 

* `deviceOwnerAuthenticationWithBiometrics` requires biometrics like FaceID or TouchID.
* `deviceOwnerAuthentication` is more allowing and also accepts a passcode.

You can then use `LAContext` to perform authentication with the policy that suits your use-case best. 

First use `canEvaluatePolicy` to check if the device can handle the policy:

```swift
let policy = LAPolicy.deviceOwnerAuthenticationWithBiometrics
var error: NSError?
let result = LAContext().canEvaluatePolicy(policy, error: &error)
```

The function takes an error pointer (not that Swifty) and your policy, then returns whether or not the device can use the policy to authenticate the user.

Since most phones support either FaceID or TouchID, most phones will be able to perform biometric authentication. The same goes for most iPads and MacBooks, since most come with TouchID. 

However, there are older and more basic device types that lack both FaceID and TouchID. You must take this into consideration when choosing which policy to use.

To perform the authentication, you just have to call `evaluatePolicy` on the context with a localized reason that describes to the user why your app needs to authentication her/him:

```swift
let policy = LAPolicy.deviceOwnerAuthenticationWithBiometrics
let reason = "The app needs your biometric information to unlock this part of the app"
let result = LAContext().evaluatePolicy(policy, localizedReason: reason) { result, error in
    // Handle the result or error
}
```

...and that's basically it. The above code is all you need to perform biometric authentication on any Apple device. I think Apple deserves praise for making these powerful tools so accessible.


## Source code

I have added various authentication services to my [SwiftKit]({{page.lib}}) library, which contains a lot of additional functionality for Swift, like extensions, types, utilities etc.

The authentication service model is abstract and allows for testing, mocking, composition, dependency injection and all those good things, so it's a good fit for systems where you need more than just calling the code above directly.