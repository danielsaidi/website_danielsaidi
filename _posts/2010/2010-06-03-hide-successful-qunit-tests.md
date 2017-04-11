---
title:	"Hide successful QUnit tests"
date:	2010-06-03 12:00:00 +0100
categories: web
tags: 	javascript jquery qunit
---


I am now using QUnit as TDD framework for my JavaScript development. It’s not as
sophisticated as NUnit is for .NET or SimpleTest for PHP, but it’s reaaally easy
to get started with.

However, QUnit strange enough QUnit lists *all* tests after a test run, not just
the ones that fails. With just a few executing tests, the report looks like this:

![QUnit - Full test report](/assets/img/blog/2010-06-03-1.png)

As you can see, QUnit lists all executed tests by default, even the ones that is
successfully executed.

The test suite above only includes 14 tests. Imagine having a hundred unit tests
or so! In my opinion, this way of presenting the test result hides the essential
outcome of the test suite – to discover tests that *fail*.

I get that one should be able to see that all tests are executed, but the number
of executed tests are listed in the result footer. Why would I need to see every
successful tests? Show me the ones that fail.

If anyone knows a built-in way to achieve this, please let me know. I chose this
following approach (applies to jQuery 1.4.2 – let me know if this is out-of date):

- Open the `qunit.js` file
- Find the block that begins with `var li = document.createElement("li");`
- Wrap the entire block in `if (bad) { ... }`

This will make QUnit only append the list element if a test is “bad”, that is if
it failed. The result will look like this:

![QUnit - Compact test result presentation](/assets/img/blog/2010-06-03-1.png)

Maybe there is a built-in way of making QUnit behave like this. If you know how,
please leave a comment.