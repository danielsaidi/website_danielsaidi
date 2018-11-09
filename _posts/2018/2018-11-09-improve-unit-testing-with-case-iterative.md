---
title:  Improve unit testing with CaseIterative
date:   2018-11-09 15:00:00 +0200
tags:	swift enum
---

In this blog post, I will show how to reduce the amount of code you have to type
when you unit test enums that implement `CaseIterable`.


## Testing enums that do not implement `Caseiterable`

Consider a simple notification enum that contains user-related notifications and
a way to create a `Notification.Name` using it.

The enum is super-simple:

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

Simple enough, right? However, if we now want to unit test this enum, we have to
write a lot of code to test all enum cases:

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

As you can see, even if we'd move duplicated logic into help functions, we still
have to write a test for each case. This is both tedious and error-prone.


## Testing enums that implement `Caseiterable`

We can greatly reduce the amount of code by letting `UserNotification` implement
`CaseIterable` as such:

```swift
public enum UserNotification: String, CaseIterable {
    
    ...
}
```

That's it! You only have to add `CaseIterable` to your enum to be able to reduce
the amount of test code you have to write:

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

And the absolute best part of this is that you don't have to write a single test
if you add new cases to `UserNotification`.


