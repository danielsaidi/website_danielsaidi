---
title: My first thoughts on TDD
date:  2009-05-05 13:16:00 +0100
tags:  testing
---

After a very interesting conference talk on Test Driven Development (TDD) and Behavior
Driven Development (BDD), I have finally started using NUnit to write unit tests while
developing new features in C#.

My first thought was (as so many have told me it would be) "why haven't I worked
like this before?". Perhaps I am having a honey-moon, but I am loving it, though
some of my coding habits translates badly to testable code.

I  have done some thinking and have some thoughts that I would like to discuss.


## Test vs. Behavior

One major discussion regarding TDD is that "test" is not a good term to use when
striving for test-driven development, since it may give the wrong associations.

I will not go into details on this, but some advocates using the term "behavior"
instead of tests, so that test functions are instead be described as behaviors,
as such:

* ShouldNotContainAnyItems

During the talk, the speaker discussed how behavior classes should be named.
Instead of (for instance) ListTest, the class should be named after the condition
and the name of the class that is tested, e.g. EmptyListTest for tests that apply 
to empty lists.

This gives a nice segmentation of the tests and prevents large test classes that
tests "everything". I've seen test classes with many many test functions...which
becomes really hard to grasp.

However, this is all good, but I have some thoughts that I gladly discuss further:

If now "test" is such a bad word, why are the classes still named with the pattern
&lt;condition&gt;&lt;class name&gt;Test? Shouldn't &lt;condition&gt;&lt;class name&gt;Behavior be a more
fitting name, since they specify the behavior of a class for a certain condition?

Also, what are we supposed to call the "test" functions? Behavior validations?


## Tests and keeping members private

I've had some discussions on how coding habits from a non-test driven development
methodology easily result in testing the wrong things and having to expose members
that should be private, just to be able to test the class.

I have played around with letting the behavior classes inherit the class they are
supposed to test. In that way, private members can be made protected, which will
expose them to any descendant, but not to other classes.

Any thoughts regarding this? It preserves a certain level of encapsulation, while
still making everything accessible to the test (sorry, behavior...hrmmm) classes.

**Update 2017** I cringe while reading this, since I clearly hadn't got it just
yet. Still, that learning process is a thing of naïve beauty, don't you think?


## Testing UI/GUI functionality

During the conference, we discussed the difficulties involved with testing the UI
layer of an application. There are some software that specializes in this kind of
testing, but it would be nice to have automated, code-based tests that cover these
parts of an application as well.

My first (once again, naïve?) thought was that it shouldn't be so hard, even for
Windows Forms and ASP.NET applications. I try to make any event handlers as thin
as possible, so that they only execute static or object functions. This makes it
even easier to test. However, but even event handlers should be testable.

I have created a small application that only features a text box and a button.
When I click the button, the application tries to parse the text to an integer
and sets the window width to the entered amount.

When I created the app, I wrote two tests. The first validates that only digits
are entered into the text box, while the second applies the size and verifies
that the form is resized correctly. This seems to work very well, but I guess
that a world of unexpected challenges is waiting for me once I get started.

What are your thoughts on Test Driven Development?



