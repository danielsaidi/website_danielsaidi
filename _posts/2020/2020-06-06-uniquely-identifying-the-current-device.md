---
title:  "Uniquely identifying the current device"
date:   2020-06-06 10:00:00 +0100
tags:   swift keychain
icon:   swift

lib:    https://github.com/danielsaidi/SwiftKit
source: https://github.com/danielsaidi/SwiftKit/tree/master/Sources/SwiftKit/Device
tests:  https://github.com/danielsaidi/SwiftKit/tree/master/Tests/SwiftKitTests/Device

post:   https://danielsaidi.com/blog/2020-06-05/keychain-access
---

In this post, we'll look at how to uniquely identify the current device. We'll look at different way of persisting the unique identifier to make it available even if the app is uninstalled.


## The basics

Uniquely identifying a device is not that straightforward, since there is no such thing on iOS. Sure, you can get the device name with `UIDevice.current.name` but chances are that `John's iPhone` is not that unique.

You can use `UUID().uuidString` to generate a unique identifier, but we then need to persist it in a way that it does not change every time we ask for it.

The most obvious way to handle unique device identifier is to generate it the first time the app asks for it, then persist it in `UserDefaults` and return the persisted value.

This works well enough, but the identifier will be regenerated as soon as the user deletes and reinstalls the app. This may cause the same device to use multiple unique identifiers.

Instead of `UserDefaults`, we could store the data in the device keychain, to make sure that it's still around even if the user reinstalls the app. I wrote about the keychain in [this blog post]({{page.post}}).

Depending on your access strategy, keychain persistency is not 100% reliable, though, since you make choose a strategy that requires the device to be unlocked, have a passcode etc. You should therefore use both approaches.


## Device identifiers

To simplify working with device identifiers, I have created a couple of components that solves the task in various ways. First, I have a `DeviceIdentifier` protocol:

```swift
public protocol DeviceIdentifier: AnyObject {
    
    func getDeviceIdentifier() -> String
}

extension DeviceIdentifier {
    
    var key: String { "com.swiftkit.deviceidentifier" }
}
```

I then implement the protocol in one way, using `UserDefaults` as persistent store:

```swift
public class UserDefaultsBasedDeviceIdentifier: DeviceIdentifier {

    public init(defaults: UserDefaults = .standard) {
        self.defaults = defaults
    }
    
    private let defaults: UserDefaults
    
    public func getDeviceIdentifier() -> String {
        if let id = defaults.string(forKey: key) { return id }
        return generateDeviceIdentifier()
    }
}

private extension UserDefaultsBasedDeviceIdentifier {
    
    func generateDeviceIdentifier() -> String {
        let id = UUID().uuidString
        defaults.set(id, forKey: key)
        return id
    }
}
```

This service tries to retrieve an identifier from user defaults and generates a new id if no identifier was found in the persistent store.

To add keychain persistency on top of this, I create a separate service, that tries to fetch the information from the keychain, but fallback to user defaults if the keychain is empty:

```swift
public class KeychainBasedDeviceIdentifier: DeviceIdentifier {

    public init(
        keychainService: KeychainService,
        backupIdentifier: DeviceIdentifier = UserDefaultsBasedDeviceIdentifier()) {
        self.keychainService = keychainService
        self.backupIdentifier = backupIdentifier
    }
    
    private let backupIdentifier: DeviceIdentifier
    private let keychainService: KeychainService
    
    public func getDeviceIdentifier() -> String {
        if let id = keychainService.string(for: key, with: nil) { return id }
        let id = backupIdentifier.getDeviceIdentifier()
        keychainService.set(id, for: key, with: nil)
        return id
    }
}
```

This service uses a `KeychainService`, which I wrote about yesterday in [this blog post]({{page.post}}).

We now have two different ways of handling the unique identifier. If regeneration is not a problem for your app, you can just use the user defaults-based one, otherwise you can use the keychain-based one.


## Source code

I have added these services to my [SwiftKit]({{page.lib}}) library. You can find the source code [here]({{page.source}}) and the unit tests [here]({{page.tests}}).