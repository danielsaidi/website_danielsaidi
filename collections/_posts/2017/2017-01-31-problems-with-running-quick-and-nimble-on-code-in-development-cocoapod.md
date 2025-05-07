---
title: Problems with Quick, Nimble and CocoaPods
date:  2017-01-31 21:50:00 +0100
tags:  swift testing
icon:  swift
---

When creating Swift libraries, I use [Quick](https://github.com/Quick/Quick) for tests and [Nimble](https://github.com/Quick/Nimble) for test assertions. They are great tools that speed up writing tests, but don't always play well with CocoaPods.

When creating new pods with `CocoaPods 1.1.1`, `Quick 1.0` and `Nimble 5.0 and 6.0`, I ran into problems with getting my tests to work.

First, creating new pods with the CocoaPods CLI still uses old project templates that aren't prepared for Swift 3. This means you have to replace Quick and Nimble with new versions, migrate the code then run `pod install` again to get the Swift 3 prepared pod versions.

If you now open the generated workspace, you'll find that the default Quick test file will run perfectly well. However, as soon as I imported the development pod and tried to write tests for my pod, Quick stopped working. It just cancels and says that all tests were successfully executed, while in fact it didn't run a single one.

Instead, it leaves all tests in the state they were in after the last test run that actually did execute. This means that if your last run resulted in all green lights, any failing tests will still result in a green run. Likewise, if the last run resulted in some errors and you fix the errors, Quick will still claim that some tests failed.

I could only get the unit tests to work by deleting the CocoaPods-generated test target and create a new test target with Xcode. I could now write tests for the development pod code with Quick and Nimble and successfully run them.
