---
title:  Simplify enum testing with CaseIterative
date:   2018-11-09 15:00:00 +0200
tags:	swift enum
---

In this blog post, I will show how to reduce the amount of code you have to type
when testing enums, by using `CaseIterable`.


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
        return "com.mydomain.notifications.user.\(rawValue)"
    }
}

public extension Notification.Name {
    
    public static func user(_ notification: UserNotification) -> Notification.Name {
        return Notification.Name(rawValue: notification.id)
    }
}
```

Simple enough, right? Still, if we want to test this enum's behavior, we have to
write a lot of code to test all available cases, for instance:

```swift
import Quick
import Nimble
import MyLibrary

class UserNotificationsTests: QuickSpec {
    
    override func spec() {

        describe("id") {
            
            it("is valid for didLogin") {
                let id = UserNotification.didLogin.id
                assert(id).to(equal("com.mydomain.notifications.user.didLogin"))
            }
            
            it("is valid for didLogout") {
                let id = UserNotification.didLogout.id
                assert(id).to(equal("com.mydomain.notifications.user.didLogout"))
            }
            
            it("is valid for loginStateDidChange") {
                let id = UserNotification.loginStateDidChange.id
                assert(id).to(equal("com.mydomain.notifications.user.didLogout"))
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

Even if we'd move duplicated logic into help functions, we still have to write a
test for each available case, if we want to fully test the enum. This is tedious
and error-prone, so we really shouldn't be doing it like this. `CaseIterable` to
the rescue!


## Testing iterable enums

`CaseIterable` is a Swift 4.2 protocol that adds an `allCases` property to enums
that implement it, which means that we can iterate over all available cases.

Using it, we can greatly reduce the amount of code we have to write in our tests.
First of all, make `UserNotification` implement `CaseIterable` like this:

```swift
public enum UserNotification: String, CaseIterable {
    
    ...
}
```

You can now reduce the amount of test code you have to write, by using `allCases`:

```swift
import Quick
import Nimble
import MyLibrary

class UserNotificationsTests: QuickSpec {
    
    override func spec() {

        describe("id") {

            it("is valid for all notifications") {
                UserNotification.allCases.forEach {
                    expect($0.id).to(equal("com.mydomain.notifications.user.\($0.rawValue)"))
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
