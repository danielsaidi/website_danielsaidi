---
title:  "Problems running Quick and Nimble tests on development pod code"
date:   2017-01-31 21:50:00 +0100
categories: apps
tags:	ios xcode cocoapods unit-tests quick nimble
---


When creating new CocoaPods, I use [Quick](https://github.com/Quick/Quick) as my
test runner and [Nimble](https://github.com/Quick/Nimble) for assertion. They are
great libraries that really speed up writing tests for my pods.

However, when I created my two latest pods, using `CocoaPods 1.1.1`, `Quick 1.0.0`
and `Nimble 5.0.0 and 6.0.0`, I ran into problems with getting my tests to work.

First of all, creating new CocoaPods with the CocoaPods CLI, still generates old
projects that are not prepared for Swift 3. This means that you have to replace
Quick and Nimble with new versions, migrate the code, then run `pod install` once
more to get the latest Swift 3-prepared pod versions added to your project.

If you now open the generated workspace, you'll find that the default Quick test
file that is added to new pods will run perfectly well.

However, as soon as I have imported the development pod in a test file and tried
to write tests for any class in the pod, the test runner just stopped executing.
It just cancels execution, tells you that tests were successfully executed, then
leaves all tests in the state they were in after the last test run that actually
did execute.

This means that if your last executed test run resulted in all green lights, any
test errors will still result in a green test suite. If your last executed test
run resulted in all some errors, and you then fix the errors and also test your
development pod, the test runner will say that some tests failed, even though it
did not run any tests at all.

I could only get unit testing of the development pod code to work by deleting the
CocoaPods-generated test project and create a new unit test target using Xcode. I
then adjusted the Example Project Podfile and updated the name of the test target,
ran `pod install` once more and opened the workspace. 

I could now write tests for the development pod code with Quick and Nimble, with
the test runner successfully executing the tests.
