---
title:  "Using keychain to read and store data on iOS"
date:   2020-06-05 10:00:00 +0100
tags:   swift keychain
icon:   swift

lib:    https://github.com/danielsaidi/SwiftKit
source: https://github.com/danielsaidi/SwiftKit/tree/master/Sources/SwiftKit/Keychain
tests:  https://github.com/danielsaidi/SwiftKit/tree/master/Tests/SwiftKitTests/Keychain

repo:   https://github.com/jrendel/SwiftKeychainWrapper
---

In this post, we'll look at how to read from and write to the keychain on iOS devices. We'll look at a great library for this and how we can make it more abstract.


## The basics

The device keychain lets us store small amounts of data outside of our application. This lets the data stick around even if a user reinstalls the application. The data can also be backed up and restored by encrypted backups.

Although we can use the keychain with vanilla Swift, it's not very convenient. Therefore, the [SwiftKeychainWrapper]({{page.repo}}) project is nice, since it lets you use the keychain much like you use `UserDefaults`. 

This project is well written, but unfortunately not maintained. To not depend on an outdated repository and avoid external dependencies, I have added the source code to my [SwiftKit]({{page.lib}}) library and bumped it up to the latest Swift version. You can find the source code [here]({{page.source}}).


## Making it abstract

To avoid having to depend on the `KeychainWrapper` class, I have created a couple of protocols that lets me better control how the keychain is used.

To read from the keychain, I have a `KeychainReader` protocol:

```swift
public protocol KeychainReader: AnyObject {

    func accessibility(for key: String) -> KeychainItemAccessibility?
    func bool(for key: String, with accessibility: KeychainItemAccessibility?) -> Bool?
    func data(for key: String, with accessibility: KeychainItemAccessibility?) -> Data?
    func dataRef(for key: String, with accessibility: KeychainItemAccessibility?) -> Data?
    func double(for key: String, with accessibility: KeychainItemAccessibility?) -> Double?
    func float(for key: String, with accessibility: KeychainItemAccessibility?) -> Float?
    func hasValue(for key: String, with accessibility: KeychainItemAccessibility?) -> Bool
    func integer(for key: String, with accessibility: KeychainItemAccessibility?) -> Int?
    func object(for key: String, with accessibility: KeychainItemAccessibility?) -> NSCoding?
    func string(for key: String, with accessibility: KeychainItemAccessibility?) -> String?
}
```

To write to the keychain, I have a `KeychainWriter` protocol:

```swift
public protocol KeychainWriter: AnyObject {

    @discardableResult
    func removeObject(for key: String, with accessibility: KeychainItemAccessibility?) -> Bool
    
    @discardableResult
    func removeAllKeys() -> Bool
    
    @discardableResult
    func set(_ value: Bool, for key: String, with accessibility: KeychainItemAccessibility?) -> Bool
    
    @discardableResult
    func set(_ value: Data, for key: String, with accessibility: KeychainItemAccessibility?) -> Bool
    
    @discardableResult
    func set(_ value: Double, for key: String, with accessibility: KeychainItemAccessibility?) -> Bool
    
    @discardableResult
    func set(_ value: Float, for key: String, with accessibility: KeychainItemAccessibility?) -> Bool
    
    @discardableResult
    func set(_ value: Int, for key: String, with accessibility: KeychainItemAccessibility?) -> Bool
    
    @discardableResult
    func set(_ value: NSCoding, for key: String, with accessibility: KeychainItemAccessibility?) -> Bool
    
    @discardableResult
    func set(_ value: String, for key: String, with accessibility: KeychainItemAccessibility?) -> Bool
}
```

I then have a `KeychainService` that implements both of these protocols:

```swift
public protocol KeychainService: KeychainReader, KeychainWriter {}
```

and a standard service implementation that wraps the `KeychainWrapper`:

```swift
public class StandardKeychainService: KeychainService {
    
    public init(wrapper: KeychainWrapper = .standard) {
        self.wrapper = wrapper
    }
    
    private let wrapper: KeychainWrapper
}

extension StandardKeychainService: KeychainReader {
    
    public func accessibility(for key: String) -> KeychainItemAccessibility? {
        wrapper.accessibility(for: key)
    }
    
    public func bool(for key: String, with accessibility: KeychainItemAccessibility?) -> Bool? {
        wrapper.bool(for: key, with: accessibility)
    }
    
    public func data(for key: String, with accessibility: KeychainItemAccessibility?) -> Data? {
        wrapper.data(for: key, with: accessibility)
    }
    
    public func dataRef(for key: String, with accessibility: KeychainItemAccessibility?) -> Data? {
        wrapper.dataRef(for: key, with: accessibility)
    }
    
    public func double(for key: String, with accessibility: KeychainItemAccessibility?) -> Double? {
        wrapper.double(for: key, with: accessibility)
    }
    
    public func float(for key: String, with accessibility: KeychainItemAccessibility?) -> Float? {
        wrapper.float(for: key, with: accessibility)
    }
    
    public func hasValue(for key: String, with accessibility: KeychainItemAccessibility?) -> Bool {
        wrapper.hasValue(for: key, with: accessibility)
    }
    
    public func integer(for key: String, with accessibility: KeychainItemAccessibility?) -> Int? {
        wrapper.integer(for: key, with: accessibility)
    }
    
    public func object(for key: String, with accessibility: KeychainItemAccessibility?) -> NSCoding? {
        wrapper.object(for: key, with: accessibility)
    }
    
    public func string(for key: String, with accessibility: KeychainItemAccessibility?) -> String? {
        wrapper.string(for: key, with: accessibility)
    }
}

extension StandardKeychainService: KeychainWriter {
    
    @discardableResult
    public func removeObject(for key: String, with accessibility: KeychainItemAccessibility?) -> Bool {
        wrapper.removeObject(for: key, with: accessibility)
    }
    
    public func removeAllKeys() -> Bool {
        wrapper.removeAllKeys()
    }
    
    @discardableResult
    public func set(_ value: Bool, for key: String, with accessibility: KeychainItemAccessibility?) -> Bool {
        wrapper.set(value, for: key, with: accessibility)
    }
    
    @discardableResult
    public func set(_ value: Data, for key: String, with accessibility: KeychainItemAccessibility?) -> Bool {
        wrapper.set(value, for: key, with: accessibility)
    }
    
    @discardableResult
    public func set(_ value: Double, for key: String, with accessibility: KeychainItemAccessibility?) -> Bool {
        wrapper.set(value, for: key, with: accessibility)
    }
    
    @discardableResult
    public func set(_ value: Float, for key: String, with accessibility: KeychainItemAccessibility?) -> Bool {
        wrapper.set(value, for: key, with: accessibility)
    }
    
    @discardableResult
    public func set(_ value: Int, for key: String, with accessibility: KeychainItemAccessibility?) -> Bool {
        wrapper.set(value, for: key, with: accessibility)
    }
    
    @discardableResult
    public func set(_ value: NSCoding, for key: String, with accessibility: KeychainItemAccessibility?) -> Bool {
        wrapper.set(value, for: key, with: accessibility)
    }
    
    @discardableResult
    public func set(_ value: String, for key: String, with accessibility: KeychainItemAccessibility?) -> Bool {
        wrapper.set(value, for: key, with: accessibility)
    }
}
```

Wrapping a wrapper may seem a bit too much, but it's to separate the protocols from the actual wrapping of the keychain and allow it to change its implementation without having to change the protocols.


## Source code

I have added this extension to my [SwiftKit]({{page.lib}}) library. You can find the source code [here]({{page.source}}) and the unit tests [here]({{page.tests}}).