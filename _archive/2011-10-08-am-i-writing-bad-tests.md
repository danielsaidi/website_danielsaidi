---
title: Am I writing bad tests?
date:  2011-10-08 12:00:00 +0100
tags:  archive
icon:  dotnet
---

To grow as a developer, there's nothing better than to invite others to criticize
your potential flaws. This post will expose my shortcomings as a unit test loving
developer. Enjoy!

In real life, aiming to be a pragmatic programmer, I aim to do what is “best” for
the project, even that means releasing a project with flaws instead of polishing
it to perfection. In hobby projects, however, I more than often find myself striving
for perfection, where this strife often proves folly in hindsight.

I now suspect that I have become trapped in another bad behavior – to unit test
everything. At its worst, I may not even be writing unit tests. I thus invite all
readers to comment on if I'm out of line here.


## The standard setup

Let's consider a pretty common scenario, where we have a service interface, an 
interface implementation and an implementation test class:

- `IGroupInviteService` – an interface with several methods.
- `GroupInviteService` – a standard, core implementation of IGroupInviteService.
- `GroupInviteServiceBehavior` – a test class that tests the standard implementation.

This setup works great. However, as I add more specific implementations, things become
a bit nasty.


## The e-mail setup

Consider extending the service model above with an service implementation that sends
out e-mails when an invite is successfully created:

- `EmailSendingGroupInviteService` – wraps any IGroupInviteService and adds sends e-mails.
- `EmailSendingGroupInviteServiceBehavior` – a test class that...well, this is the problem.

How can I ensure that this test class only tests the e-mail extensions in this new service?


## EmailSendingGroupInviteService

Let’s take a look at the `EmailSendingGroupInviteService` class.

![EmailSendingGroupInviteService](/assets/blog/2011/111008-1.png "EmailSendingGroupInviteService")

The e-mail sending part is not yet developed. 😉 As you can also see, the methods only
call the base instance.


## EmailSendingGroupInviteServiceBehavior

Let’s take a look at the `EmailSendingGroupInviteServiceBehavior` test class.

![EmailSendingGroupInviteServiceBehavior](/assets/blog/2011/111008-2.png "EmailSendingGroupInviteServiceBehavior")

As you can see, all that I can test is that the base instance is called properly
and that the base instance result is returned.


## Conclusion

Testing the decorator class like this is time-consuming, and for each new method,
I have to write more of these tests. I want to ensure that the base service is 
properly used, but I really hate writing these tests.

Wouldn't it be better to only test the stuff that differ, i.e. keep the
`CreateInvite_ShouldSendEmail` and skip the rest? Let me know what you think.