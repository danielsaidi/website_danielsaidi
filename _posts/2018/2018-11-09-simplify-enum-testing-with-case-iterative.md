---
title:  Simplify enum testing with CaseIterative
date:   2018-11-09 15:00:00 +0200
tags:	swift enum
---

In this blog post, I will show how to reduce the amount of code you have to type
when testing enums, by using the new `CaseIterable` protocol.


## Testing non-iterable enums

Consider a simple enum setup that contains user-related notifications as well as
`Notification.Name` extensions for it:

```swift
public enum UserNotification: String {
    
    case
    didLogin,
    didLogout,
    loginStateDidChange
    
    public var id: String {
        return "notifications.user.\(rawValue)"
    }
}

public extension Notification.Name {
    
    public static func user(_ notification: UserNotification) -> Notification.Name {
        return Notification.Name(rawValue: notification.id)
    }
}
```

Simple enough, right? Still, if we want to test this enum's behavior, we have to
write some extra code to test all cases. The most exhausting would be to write a
lot of code e.g.:

```swift
import Quick
import Nimble
import MyLibrary

class UserNotificationsTests: QuickSpec {
    
    override func spec() {

        describe("id") {
            
            it("is valid for didLogin") {
                let id = UserNotification.didLogin.id
                assert(id).to(equal("notifications.user.didLogin"))
            }
            
            it("is valid for didLogout") {
                let id = UserNotification.didLogout.id
                assert(id).to(equal("notifications.user.didLogout"))
            }
            
            it("is valid for loginStateDidChange") {
                let id = UserNotification.loginStateDidChange.id
                assert(id).to(equal("notifications.user.loginStateDidChange"))
            }
        }
        
        describe("notification name") {
            
            it("is valid for didLogin") {
                let notification = UserNotification.didLogin
                let name = Notification.Name.user(notification)
                assert(name.rawValue).to(equal(notification.id))
            }
            
            it("is valid for didLogout") {
                let notification = UserNotification.didLogout
                let name = Notification.Name.user(notification)
                assert(name.rawValue).to(equal(notification.id))
            }
            
            it("is valid for loginStateDidChange") {
                let notification = UserNotification.loginStateDidChange
                let name = Notification.Name.user(notification)
                assert(name.rawValue).to(equal(notification.id))
            }
        }
    }
}
```

This would be a pain to maintain, though, especially for an enum with many cases.
Each new case would require you to add 9 more lines of code, with the additional
risk of copy/paste bugs etc.

This could be easily fixed, though, by creating a test array with all cases. The
test class would then become:

```swift
import Quick
import Nimble
import MyLibrary

class UserNotificationsTests: QuickSpec {
    
    override func spec() {

        let notifications: [UserNotification] = [.didLogin, .didLogout, .loginStateDidChange]

        describe("id") {

            it("is valid for all notifications") {
                notifications.forEach {
                    expect($0.id).to(equal("notifications.user.\($0.rawValue)"))
                }
            }
        }
        
        describe("notification name") {

            it("is valid for all notifications") {
                notifications.forEach {
                    let name = Notification.Name.user($0)
                    expect(name.rawValue).to(equal($0.id))
                }
            }
        }
    }
}
```

However, this approach would still require you to remember to add every new case
to the array. This is tedious and error-prone, and completely unnecessary, since
we now have the brand new `CaseIterable` to help us out.


## Testing iterable enums

`CaseIterable` is a new Swift 4.2 protocol that adds an `allCases` property to
enums that implement it. With it, we can reduce the amount of code we have to
write in our tests.

First of all, make `UserNotification` implement `CaseIterable` like this:

```swift
public enum UserNotification: String, CaseIterable {
    
    ...
}
```

You can now reduce the manually managed test array, by using `allCases` instead:

```swift
import Quick
import Nimble
import MyLibrary

class UserNotificationsTests: QuickSpec {
    
    override func spec() {

        describe("id") {

            it("is valid for all notifications") {
                UserNotification.allCases.forEach {
                    expect($0.id).to(equal("notifications.user.\($0.rawValue)"))
                }
            }
        }
        
        describe("notification name") {

            it("is valid for all notifications") {
                UserNotification.allCases.forEach {
                    let name = Notification.Name.user($0)
                    expect(name.rawValue).to(equal($0.id))
                }
            }
        }
    }
}
```

Another benefit is that you don't have to remember to write new tests every time
you add new cases to `UserNotification`.


## Internally iterable enums

If you only want to use the `CaseIterable` capabilities within your library and
tests, you can make the implementation internal:


```swift
public enum UserNotification: String {   

    ...
}

extension UserNotification: CaseIterable {}
```

To make this available to your tests, your must now use `@testable import`:

```swift
import Quick
import Nimble
@testable import MyLibrary

class UserNotificationsTests: QuickSpec {
    
    ...
}
```

This means that you can benefit from `CaseIterable` capabilities in your library
and tests, without having to expose them outside these boundaries.


