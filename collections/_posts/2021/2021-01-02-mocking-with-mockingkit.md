---
title:  Mocking with MockingKit
date:   2021-01-02 07:00:00 +0100
tags:   swift unit-testing mocking
assets: /assets/blog/2021/2021-01-02/
image:  /assets/blog/2021/2021-01-02/title.png

unit-testing:  https://en.wikipedia.org/wiki/Unit_testing
mocking:       https://en.wikipedia.org/wiki/Mock_object
mockingkit:    https://github.com/danielsaidi/MockingKit
---

In this post, I'll demonstrate how to use [MockingKit]({{page.mockingkit}}) to mock protocols and classes. We can then use this technique in unit tests and to fake not yet implemented logic.


## Background

Mocking is a way to create an interactive, fake implementation of a protocol or class. It's a great tool when writing unit tests and to fake not yet implemented logic. If you're unfamiliar with [unit testing]({{page.unit-testing}}) or [mocking]({{page.mocking}}), you can read more by following these links.

I come from a .NET background and was fortunate to be around when the community was all about unit testing and how to write testable code. As I later started looking at iOS and Android development, I found that this was not as much discussed and that the mindset around mocking was more about mocking HTTP requests than protocols. The native tools were also very basic and lacked a lot of the power that I had come to expect from Visual Studio and the various .NET test libraries.

As Swift was later introduced, the limited introspection made mocking hard, where the few Swift-based libraries I found were more about generating boilerplate code than interactive mocks. So I made do with manual mocks for many years, writing a lot of manual code to register function invokations, tracking parameters, returning fake results etc. Let's look at this way of mocking.


## Manual mocks

We'e not going to get stuck on manual mocks, but I think it's good to take a quick look at it to be able to compare this approach to using real mocks.

Consider that we have a simple printer protocol:

```swift
protocol Printer {

    func print(_ string: String) -> Bool
}
```

A manual mock of this protocol could look something like this:

```swift
class MockPrinter: Printer {

    var printInvokeCount = 0
    var printInvokeArgs = [String]()
    var printInvokeResult = false

    func print(_ string: String) -> Bool {
        printInvokeCount += 1
        printInvokeArgs.append(arg)
        return printInvokeResult
    }
}
```

Now, for any class that needs a `Printer`:

```swift
class MyClass {

    init(printer: Printer) {
        self.printer = printer
    }

    private let printer: Printer

    func doSomething() {
        let result = printer.print("I'm doing it!")
        ...
    }
}
```

we can now inject a mock printer instead of a real one:

```
let printer = MockPrinter()
let obj = MyClass(printer: printer)
```

and inspect how the mock is used by the class like this:

```swift
obj.doSomething()
obj.doSomething()
printer.printInvokeCount   // 2
printer.printInvokeArgs    // ["I'm doing it!", "I'm doing it!"]
```

In lack of other tools, this works fairy well. However, manual mocks are *tedious and time-consuming* and dulicates the same boilerplate code and logic over and over again.

We can do better.


## MockingKit

![MockingKit]({{page.assets}}title.png)

[MockingKit]({{page.mockingkit}}) is a Swift-based mocking library that makes it easy to mock mock protocol implementations and classes. It lets you invoke function calls, inspect invokations and register function results.

### Terminology

Before we continue, let's clarify what this means in detail.

`Invokation` is to `record` a function call. It saves information about how many times a function has been called, with which arguments and the returned result.

`Inspection` is to look at recorded invokation information and use it e.g. in a unit test. For instance, we can verify that a function has been triggered only  once, with a certain argument.

`Registration` is to pre-register function results, based on the arguments with which a function is called.

### Creating a mock

To create a mock with MockingKit, you can inherit the `Mock` base class and implement any protocol that you want to mock. If your mock has to inherit another class (e.g. when mocking system classes like `UserDefaults`), you can implement the `Mockable` protocol instead and provide a custom mock. 

`Mock` is basically just a `Mockable` that uses itself as mock.

In other words, you can either do this:

```swift
import MockingKit

class MockStringValidator: Mock {}
```

or this:

```swift
import MockingKit

class MockStringValidator: Mockable {

    let mock = Mock()
}
```

The two alternatives are identical except from the `mock` property. You use them in exactly the same way.


### Invoking function calls

To be able to invoke functions, you need to create a `MockReference` for each function that you want to mock. A mock reference is basically just a reference to a function with a unique identifier.

Let's implement the `Printer` protocol and mock the `print` function:

```swift
import MockingKit

class MockPrinter: Mock, Printer {

    lazy var printRef = MockReference(print)

    func print(_ text: String) {
        invoke(printRef, args: (text))
    }
}
```

Note how a reference must be lazy, since it refers to an instance function. The `printRef` now refers to `print` and is invoked whenever `print` is called.


### Inspecting invokations

As we mentioned before, calling `invoke` records a function call so that you can inspect it later.

Given the `MockPrinter` above, we could inspect it like this:

```swift
let printer = MockPrinter()
printer.print("Hello!")
let inv = printer.invokations(of: printer.printRef)     // => 1 item
inv[0].arguments                                        // => "Hello!"
printer.hasInvoked(printer.printRef)                    // => true
printer.hasInvoked(printer.printRef, numberOfTimes: 1)  // => true
printer.hasInvoked(printer.printRef, numberOfTimes: 2)  // => false
```

MockingKit has a bunch of handly inspection alternatives, like checking if a function has been invoked or not, how many times, with what arguments, what it returned etc. Since `print` has no return value, this information is not available in this case.


### Registering return values

Say that we have a protocol that has a function that returns a value:

```swift
protocol StringConverter {

    func convert(_ text: String) -> String
}
```

A MockingKit mock would look like this:

```swift
class MockStringConverter: Mock, StringConverter {

    lazy var convertRef = MockReference(convert)

    func convert(_ text: String) -> String {
        invoke(convertRef, args: (text))
    }
}
```

Unlike the void invokation in `MockPrinter`, this `invoke` actually returns a value. Since the function body is a one-lines, you can omit `return`.

If the return value is optional, it is *optional* to register a return value before invoking the function. Calling `invoke` before registering a return value will return `nil`.

If the return value is non-optional, it is *mandatory* to register a return value before invoking the function. Calling `invoke` before registering a return value will cause a crash. 

Registering a return value is easy:

```swift
let mock = MockConverter()
let result = mock.convert("banana") // => Crash!
converter.registerResult(for: mock.convertRef) { input in String(input.reversed()) }
// or, shorter: 
converter.registerResult(for: mock.convertRef) { String($0.reversed()) }
let result = mock.convert("banana") // => Returns "ananab"
```

Note how we can use the input argument to determine the returned result. This gives function registration a lot of power in MockingKit.


### Multiple function arguments

If a mocked function has multiple arguments, inspection behaves a little different, since function arguments are handled as a tuple.

Say that we have a protocol that looks like this:

```swift
protocol MyProtocol {

    func doStuff(int: Int, string: String) -> String
}
```

A MockingKit mock would look like this:

```swift
class MyMock: Mock, MyProtocol {

    lazy var doStuffRef = MockReference(doStuff)

    func doStuff(int: Int, string: String) -> String {
        invoke(doStuffRef, args: (int, string))
    }
}
```

Since function arguments are handled as tuples, you now use tuple positions when referring to the arguments:

```swift
let mock = MyMock()
mock.registerResult(for: mock.doStuffRef) { args in String(args.1.reversed()) }
let result = mock.doStuff(int: 42, string: "string")    // => "gnirts"
let inv = mock.invokations(of: mock.doStuffRef)         // => 1 item
inv[0].arguments.0                                      // => 42
inv[0].arguments.1                                      // => "message"
inv[0].result                                           // => "gnirts"
mock.hasInvoked(mock.doStuffRef)                        // => true
mock.hasInvoked(mock.doStuffRef, numberOfTimes: 1)      // => true
mock.hasInvoked(mock.doStuffRef, numberOfTimes: 2)      // => false
```

There is no upper-limit to the number of function arguments you can use in a mocked function.


### Multiple functions with the same name

If your mock has multiple methods with the same name:

```swift
protocol MyProtocol {
    func doStuff(with int: Int) -> Bool
    func doStuff(with int: Int, string: String) -> String
}
```

your must explicitly specify the function signature when creating references:

```swift
class MyMock: Mock, MyProtocol {

    lazy var doStuffWithIntRef = MockReference(doStuff as (Int) -> Bool)
    lazy var doStuffWithIntAndStringRef = MockReference(doStuff as (Int, String) -> String)

    func doStuff(with int: Int) -> Bool {
        invoke(doStuffWithInt, args: (int))
    }

    func doStuff(with int: Int, string: String) -> String {
        invoke(doStuffWithIntAndStringRef, args: (int, string))
    }
}
```

This is actually nice, since it gives you unique references to each function. It also makes the unit test code easier to write.


### Properties

In MockingKit, properties can't be mocked since you really shouldn't have to. Just set the properties of the mock right away. If you for some reason want to inspect how a property is called and modified (you probably shouldn't have to), you can just invoke a custom reference in the getter and/or setter.


### Async functions

There are currently no built-in tools for working with async functions. An async function is just a void function and any completion blocks you provide it with are just arguments like any others. You must manually call the completions from within your mocks.


## Conclusion

[MockingKit]({{page.mockingkit}}) is a tiny library that simplifies working with mocked logic. If you are into unit testing and mocking, I'd love for you to try it out and tell me what you think.