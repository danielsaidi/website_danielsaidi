---
title:  "IoC and dependency injection - the basics"
date:   2020-05-26 20:00:00 +0100
tags:   swift ioc dependency-injection
icon:   swift

lib:    https://github.com/danielsaidi/SwiftKit
source: https://github.com/danielsaidi/SwiftKit/Sources/SwiftKit/IoC
tests:  https://github.com/danielsaidi/SwiftKit/Tests/SwiftKit/IoC

dip:        https://github.com/AliSoftware/Dip
swinject:   https://github.com/Swinject/Swinject
---

In this post, we'll look at the basics of Inversion of Control (IoC) and Dependency Injection and how to reduce coupling within your codebase.


## The basics

Consider that you have an app that lets a user to log in and out. For the sake of simplicity, let's say that calling a global `login` function calls an external api to get the job done.

If your app calls `login` directly, you have a strong dependency to the function and api call. If you want to change the login behavior to e.g. add additional functionality, you have to change the function implementation. Also, testing the parts of your codebase that depend on this function will be hard in a clean way.

What you can do then, is to create a class that performs the login operation, for instance a `LoginService` class. You can then replace all `login` calls with using this service. However, you *still* have a strong dependency to the same functionality. You just moved it somewhere else.

You can then remove the strong dependency to the login service class by creating a `protocol` that instead describes what a "LoginService" should be able to do (when you're in this mindset, you often start designing the protocols before creating the implementations) then let your app depend on the protocol instead of the concrete class.

Dependending on protocols instead of concrete types gives you *lot* of flexibility, where you can replace implementations without having to change any logic. You can also compose various implementations (e.g. using the **decorator pattern**) to enrich the feature set of a component type without changing the functionality of each implementation.

To manage dependencies, you have various alternatives, where **dependency injection** is one. Injecting dependencies means that you provide components with their dependencies instead of having your types defining what kind of implementations they want.

There are numerous tools that let you implement dependency injection, where [Dip]({{page.dip}}) and [Swinject]({{page.swinject}}) are two great ones. Give them a look and start breaking up your strong dependencies 👍


## Example

Consider that you have a class, view, view controller or anything that should be able to login the user. Also, say that you have a `LoginService` protocol and an `ApiLoginService` implementation, e.g.:

```swift
protocol LoginService {

    func login(userName: String, password: String, completion: (Bool) -> Void)
}

class ApiLoginService: LoginService {

    func login(userName: String, password: String, completion: (Bool) -> Void) {
        ...
    }
}
```

Let's say that you want to use this functionality in a `LoginScreen` and login users when they tap a login button. You don't want to do it like this:

```swift
struct LoginScreen: View {

    ...

    func loginButtonTapped() {
       ApiLoginService().login(...)
    }
}
```

This would be one of the worst ways to depend on `ApiLoginService`, since it's resolved within a function, perhaps deep in the code, making the dependency very obscure. 

Moving out the dependency and convert it to a property wouldn't improve things much:

```swift
struct LoginScreen: View {

    private let loginService = ApiLoginService()

    ...

    func loginButtonTapped() {
       loginService.login(...)
    }
}
```

Here, you still let your login screen define what kind of service to use...although the hard dependency is easier to see. 

Instead, you want to inject *some* login service into the class, preferably when creating the instance of the screen:



```swift
struct LoginScreen: View {

    init(loginService: LoginService) {
        self.loginService = loginService
    }

    private let loginService: LoginService

    ...

    func loginButtonTapped() {
       loginService.login(...)
    }
}
```

This is much better! The screen doesn't know what service it's going to use. It just tells the system that it requires *a* login service to function.

You don't need a dependency manager to handle this kind of dependencies, although it helps. You could, however, also use static properties and factory functions or many other ways to handle dependencies. The last code block above is key, though, since it keeps the screen independent, gives you flexibility, simplifies testing etc.

And that wraps up the basics of dependency injection, more or less. But there is one more thing I want you to consider before you run off and abstract all the things.


## Don't rely on your dependency manager

So, you have decided to use a dependency manager after reading the text above? Wow, that's great! I hope that it feels nice to reduce the hard dependencies in your code and manage them in a central manner. God knows I can't code in any other way. 

But with this way of handling dependencies, how much do you depend on your...dependency manager?

Since I aim to reduce coupling *everywhere*, I also always create an abstraction layer between my systems and how they resolve dependencies. I have added such tools to my [SwiftKit]({{page.lib}}) library, which contains a lot of additional functionality for Swift, like extensions, types, utilities etc.

The `IoC` model let's you register any dependency manager, as long as you make it implement a simple `IoCContainer` protocol. The library also contains commented out implementations for `Dip` and `Swinject`. The solution is abstract and allows for testing, mocking, composition and all those good things.

You can find the source code [here]({{page.source}}) and the unit tests [here]({{page.tests}}). There is also a demo app that demonstrates how you can register and resolve dependencies. Feel free to give it a try and let me know what you think.