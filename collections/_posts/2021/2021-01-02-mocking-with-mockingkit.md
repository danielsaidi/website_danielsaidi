---
title:  Mocking with MockingKit
date:   2021-01-02 07:00:00 +0100
tags:   article swift unit-testing mocking
assets: /assets/blog/2021/2021-01-02/
image:  /assets/blog/2021/2021-01-02/title.png

unit-testing:  https://en.wikipedia.org/wiki/Unit_testing
mocking:       https://en.wikipedia.org/wiki/Mock_object
mockingkit:    https://github.com/danielsaidi/MockingKit
---

In this post, I'll demonstrate how to use [MockingKit]({{page.mockingkit}}) to create dynamic mocks of protocols and classes, that let you record and inspect function calls, register conditional returns etc. We can then use this in unit tests and to fake not yet implemented logic.


## Background

Mocking is a way to create interactive, fake implementations of protocols and classes. It's a great tool when writing unit tests and to fake not yet implemented logic. If you're unfamiliar with [unit testing]({{page.unit-testing}}) or [mocking]({{page.mocking}}), you can read more by following these links.

I come from a .NET background and was fortunate to be around when the community was all about unit testing and how to write testable code. As I later started with at iOS and Android development, I found that this was not as much discussed in these communities, and that the mindset around mocking was more about mocking HTTP requests than protocols. The native tools, if any, were also very basic and lacked a lot of the power that I had come to expect from Visual Studio and various .NET test libraries.

As Swift was later introduced, the limited introspection made mocking hard, where the few Swift-based libraries I found were more about generating boilerplate code than interactive mocks. So I made do with manual mocks for years and wrote manual code to register function invokations, tracking parameters, returning fake results etc. 

Let's look at this way of mocking, before we see how to implement real, dynamic mocks.


## Manual mocks

We'e not going to get stuck on manual mocks, but I think it's good to take a quick look at it to be able to compare this approach to using real mocks.

Consider that we have a simple `Printer` protocol:

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

we can inject a `MockPrinter` instead of a real one:

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

In lack of other tools, this works fairy well. However, manual mocks are tedious and time-consuming to write, and dulicates the similar code and logic over and over again.

We can do better.


## MockingKit

![MockingKit]({{page.assets}}title.png)

[MockingKit]({{page.mockingkit}}) is a Swift-based mocking library that makes it easy to create dynamic and interactive mocks of protocols and classes. It lets you invoke and inspect function calls, register function results etc.

### Terminology

Before we continue, let's clarify what this means in detail.

`Invokation` is to call a function. In a mock, this `record` the call and saves information about how many times a function has been called, with which arguments, the returned result etc.

`Inspection` is to look at recorded invokation information and use it e.g. in a unit test. For instance, we can verify that a function has been triggered only  once, with a certain argument.

`Registration` is to pre-register a dynamic return value for a function, based on the arguments with which the function is called.

### Creating a mock

To create a mock with MockingKit, you can inherit the `Mock` class and implement any protocol that you want to mock. If a mock has to inherit another class (e.g. to mock system classes like `UserDefaults`), you can implement the `Mockable` protocol instead.

`Mock` is basically just a `Mockable` implementation that uses itself as mock. All the mock functionality is provided by `Mockable`.

In other words, you can either do this:

```swift
import MockingKit

class MockStringValidator: Mock, StringValidator {}
```

or this:

```swift
import MockingKit

class MockUserDefaults: UserDefaults, Mockable {

    let mock = Mock()
}
```

The two options are identical except from the `mock` property. You use them in exactly the same way.


### Invoking function calls

To be able to invoke functions, you need to create a `MockReference` for each function that you want to mock. A mock reference is basically just a function reference that is used for invokation and inspection.

Let's implement the `Printer` protocol from earlier and mock the `print` function:

```swift
import MockingKit

class MockPrinter: Mock, Printer {

    lazy var printRef = MockReference(print)

    func print(_ text: String) {
        invoke(printRef, args: (text))
    }
}
```

Note how references must be lazy, since they refer to an instance function. `printRef` now refers to `print` and is invoked whenever `print` is called.


### Inspecting invokations

As we mentioned before, calling `invoke` records a function call so that you can inspect it later. Since the invokation is done by the mock as its functions are called, you can use the mock as you would use any other implementation of the same protocol.

When inspecting invokations for the various functions in a mock, we have to use the function references instead of the functions themselves.

Given the `MockPrinter` above, we could inspect it like this:

```swift
let printer = MockPrinter()
printer.invokations(of: printer.printRef)               // => 0 items
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

Unlike the void invokation in `MockPrinter`, this `invoke` actually returns a value. Since the function body is a one-liner, you can omit `return`.

If the return value is optional, it's also optional to register a return value before invoking the function. Calling `invoke` before registering a return value will return `nil`.

If the return value is non-optional, you must register a return value before invoking the function. Calling `invoke` before registering a return value will cause a crash. 

Registering a return value is easy:

```swift
let mock = MockConverter()
let result = mock.convert("banana") // => Crash!
converter.registerResult(for: mock.convertRef) { input in String(input.reversed()) }
// or shorter: 
converter.registerResult(for: mock.convertRef) { String($0.reversed()) }
let result = mock.convert("banana") // => "ananab"
```

Note how we can use the input argument of the function call to determine the returned result. This gives function registration a lot of power in MockingKit.


### Multiple function arguments

If a mocked function has multiple arguments, inspection behaves a little different, since arguments are handled as tuples.

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

Since function arguments are handled as tuples, you now use tuple positions to inspect arguments:

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

This is actually nice, since it gives you a unique references for each function. It also makes the unit test code easier to write.


### Properties

In MockingKit, properties can't be mocked with function references, since the function reference model requires a function. To fake the value of a mock property, just set the properties of the mock right away. 

If you however for some reason want to inspect how a property is called, modified etc., you can invoke custom references to private functions in the getter and/or setter.


### Async functions

Async functions are just void return functions and its completion blocks are just arguments like any others. You must however manually call the completions from within your mocks.


## Conclusion

[MockingKit]({{page.mockingkit}}) is a tiny library that simplifies working with mocked logic. If you are into unit testing and mocking, I'd love for you to try it out and tell me what you think.