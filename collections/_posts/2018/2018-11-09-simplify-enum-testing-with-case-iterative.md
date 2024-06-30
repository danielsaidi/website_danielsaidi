---
title: Simplify enum testing with CaseIterative
date:  2018-11-09 15:00:00 +0200
tags:  swift testing
icon:  swift
---

In this post, I will show how to reduce the amount of code you have to type when testing enums, by using the new `CaseIterable` protocol.


## Testing non-iterable enums

Consider this `UserNotification` enum and `Notification.Name` extension:

```swift
enum UserNotification: String {
    
    case
    didLogin,
    didLogout,
    loginStateDidChange
    
    var id: String {
        return "notifications.user.\(rawValue)"
    }
}

extension Notification.Name {
    
    static func user(_ notification: UserNotification) -> Notification.Name {
        return Notification.Name(rawValue: notification.id)
    }
}
```


## Separate tests for each enum case

To test this enum, we could write a lot of code to test all enum cases, for instance (in the code below, I use Quick and Nimble):

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

However, this code would still be a pain to maintain. Each new case would require you to add more code, with the additional risk of copy/paste bugs etc. 


## Iterate over a fixed array

You could simplify the test suite above by creating an array with all enum cases:

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

However, this still requires you to remember to add new cases to the array. It's tedious...and unnecessary, since we now have the brand new `CaseIterable` to help us out.


## Using CaseIterable

`CaseIterable` is a Swift 4.2 protocol that adds an `allCases` property to every enum that implements it. With this, we can reduce the amount of code we have to write in our tests.

First, make `UserNotification` implement `CaseIterable` like this:

```swift
public enum UserNotification: String, CaseIterable {
    
    ...
}
```

You can now reduce the test suite by using `allCases` instead:

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

Another benefit is that you don't have to remember to write new tests when you add a new cases to this enum.


## Internally iterable enums

If your enum is public, but you only want to use the `CaseIterable` capabilities within your library and tests, you can make the implementation internal:


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

This means that you can benefit from `CaseIterable` capabilities in your library and tests, without having to expose them outside these boundaries. You can also add `CaseIterable` extensions to the test bundle instead of the public project.


## Conclusion

`CaseIterable` makes it easy to test your enums, for instance to verify that some properties or functions behave correctly for all cases.

Not all enums can implement `CaseIterable` automatically. If an enum case has associated values, you must implement it manually, since there's an infinite amount of potential cases.