---
title:  "Am I writing bad tests?"
date:   2011-10-08 12:00:00 +0100
tags: 	.net unit-testing tdd
---


To grow as a developer, there is nothing as good as inviting others to criticize
your potential flaws...that as well as reading a book every now and then.

In real life, you have to be a pragmatic programmer (provided that you are, in
fact, a programmer to begin with) and do what is “best” for the project, even if
that means releasing a project instead of polishing it to perfection.

In hobby projects, however, I more than often find myself reaching for this very
perfection all the time (however, my earlier attempts of perfection has involved
having standardized regions for variables, properties, methods, constructors etc.
as well as writing comments for all public members of every class...aka failing).
I was ruthlessly beaten out of this bad behavior by http://twitter.com/#!/nahojd,
to whom I hold eternal gratitude.

I now suspect that I have become trapped in another bad behavior – the unit test
everything trap. At its worst, I may not even be writing unit tests, so I invite
all readers to comment on if I am out on a bad streak here.


## The standard setup

In the standard setup, I have:

- `IGroupInviteService` – this interface has several methods, e.g. AcceptInvite and CreateInvite
- `GroupInviteService` – a standard implementation of IGroupInviteService that handles the core processes, with no addons
- `GroupInviteServiceBehavior` – a test class that tests every little part of the standard implementation

The setup above works great. It is the e-mail setup below that makes me doubt my
own competence.


## The e-mail setup

In the extended e-mail sending setup, I have:

- `EmailSendingGroupInviteService` – facades any IGroupInviteService and sends out an e-mail when an invite is created.
- `EmailSendingGroupInviteServiceBehavior` – a test class that...well, that is the problem.


## EmailSendingGroupInviteService

Before moving on, let’s take a look at the EmailSendingGroupInviteService class.

![EmailSendingGroupInviteService](/assets/blog/2011-10-08-1.png "EmailSendingGroupInviteService")

As you can see, the e-mail sending part is not yet developed. 😉 As you can also
see, the methods only call the base instance. Now, let’s look at some tests.


## EmailSendingGroupInviteServiceBehavior

Let’s take a look at some of the tests in EmailSendingGroupInviteServiceBehavior.

![EmailSendingGroupInviteServiceBehavior](/assets/blog/2011-10-08-2.png "EmailSendingGroupInviteServiceBehavior")

As you can see, all that I can test is that the base instance is called properly
and that the base instance result is returned.


## Conclusion

Testing the decorator class like this is really time-consuming, and for each new
method I add, I have to write more of these tests for each decorator class. That
could become a lot of useless tests. I just hate having to write them 🙂

So, this raises my final question:

Wouldn't it be better to only test the stuff that differ? In this case, keep the
CreateInvite_ShouldSendEmail and skip the rest.

Let me know what you think.