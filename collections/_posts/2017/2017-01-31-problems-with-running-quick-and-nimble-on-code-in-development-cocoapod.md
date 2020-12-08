---
title: Problems with running Quick and Nimble on code in development CocoaPod
date:  2017-01-31 21:50:00 +0100
tags:  ios xcode cocoapods testing
redirect_from: /blog/mobile/2017/01/31/problems-with-running-quick-and-nimble-on-code-in-development-cocoapod
---

When creating CocoaPods, I use [Quick](https://github.com/Quick/Quick) for tests
and [Nimble](https://github.com/Quick/Nimble) for assertions. They are great and
really speed up writing tests.

However, when creating my two latest pods, using `CocoaPods 1.1.1`, `Quick 1.0.0`
and `Nimble 5.0.0 and 6.0.0`, I ran into problems with getting my tests to work.

First of all, creating new CocoaPods with the CocoaPods CLI, still generates old
projects, that are not prepared for Swift 3. This means that you have to replace
Quick and Nimble with new versions, migrate the code then run `pod install` once
more to get the latest Swift 3-prepared pod versions added to your project.

If you now open the generated workspace, you'll find that the default Quick test
file will run perfectly well. However, as soon as I imported the development pod
and tried to write tests for any class in my pod, the Quick just stopped working.
It just cancelled and said that all tests were successfully executed, but it did
not run a single one. Instead, it left all tests in the state they were in after
the last test run that actually did execute.

This means that if your last executed test run resulted in all green lights, any
failing tests will still result in a green test suite. Likewise, if the last run
resulted in some errors and you then fix the errors, Quick will still claim that
some tests failed, even though it did not run any tests at all.

I could only get the unit tests to work by deleting the CocoaPods-generated test
project and create a new test target with Xcode. I could now write tests for the
development pod code with Quick and Nimble and successfully run them.
