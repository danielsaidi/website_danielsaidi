---
title: My first thoughts on TDD
date:  2009-05-05 13:16:00 +0100
tags:  testing
icon:  avatar
---

After an interesting conference talk on Test and Behavior Driven Development (TDD 
& BDD), I have now started using NUnit to write unit tests in C#. Here are some
initial thoughts from a TDD n00b.

My first reaction was (as many told me it would be) "why haven't I worked like this
before?". Perhaps I am having a honey-moon, but I sure am loving it, though some of
my coding habits translates badly to testable code. However, I have some thoughts
that I'd like to discuss.


## Test vs. Behavior

One major discussion regarding TDD is that "test" is not a good term to use when
striving for test-driven development, since it may give the wrong associations.

I will not go into details on this, but some advocates using the term "behavior"
instead of tests, so that test functions are instead be described as behaviors,
as such:

* ShouldNotContainAnyItems

The speaker advocated that instead of e.g. `ListTest`, a list test class could
instead be named after the condition and the name of the class that it tests. For
instance, an `EmptyListTest` class could be used to perform tests that apply to
empty lists. This approach prevents large classes that test everything.

However, if "test" is a bad word, why are the test classes still named with the
pattern **&lt;condition&gt;&lt;class name&gt;Test**? Wouldn't **&lt;condition&gt;&lt;class name&gt;Behavior** 
be a better name pattern, since the classes specify the behavior of a class for
a certain condition?

Also, what are we supposed to call the "test" functions? Behavior validations?


## Tests and keeping members private

I've had some discussions regarding how non-test driven coding habits easily
result in testing the wrong things and having to expose members that should be
private, just to be able to test the class.

I have played around with letting the behavior classes inherit the class they are
supposed to test. In that way, private members can be made protected, which will
expose them to sub classes, but not any other classes. Any thoughts on this? It
preserves a certain level of encapsulation, while still making everything accessible
to the test class.


### Update 2017

I cringe when I read the above section now. I clearly hadn't got it just yet. Still,
that learning process is a thing of naïve beauty, right?


## Testing UI/GUI functionality

During the conference, we discussed the difficulties involved with testing the UI
layer of an application. There are some software that specializes in this kind of
testing, but it would be nice to have automated, code-based tests that cover these
parts of an application as well.

My first thought was that it shouldn't be so hard for Windows Forms and ASP.NET
applications. I try to make any event handlers as thin as possible, so that
they only execute static or object functions. This makes it even easier to test.
However, shouldn't the event handler also be testable?

I have created a small test application that only has a text box and a button.
When I click the button, the application tries to parse the text to an integer
and sets the window width to the entered amount. I then wrote two tests, where
the first validates that only digits are entered, while the second applies the
size and verifies that the form is resized correctly. 

This seems to work well, but I guess that a world of unexpected challenges is
waiting once I get started with this for real.

What are your thoughts on Test and Behavior Driven Development?



