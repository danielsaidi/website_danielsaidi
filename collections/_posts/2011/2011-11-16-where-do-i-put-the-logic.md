---
title: Where do I put the logic?
date:  2011-11-16 12:00:00 +0100
tags:  c# .net
---

I could use some advice regarding a project that I'm currently working on, where
people can sign up and join various groups (did I hear “Facebook is already
doing that”?). I'm now torn on some implementation details and would love some
discussions regarding where to put certain pieces of logic.

The purpose of the web site is not important, nor is the big domain model. Let’s
ignore all complexity and focus on the `Member` and `Group` entities. Also, when
this post mentions interfaces, it corresponds to protocols in Objective-C and Swift
and is a way to design for the what's and not the how's.


## Architecture

In this project, I have a repository interface for each entity. The repositories
main implementations have the sole responsibility to hide Entity Framework from
the rest of the system. I also have other repository implementations, e.g. fake
ones that just create fake data, cache decorators etc.

I then have service interfaces for more sophisticated functionality. Each service
uses one or several repositories, which means that a repository can be simple and
just handle data from a certain data source, while a service can do sophisticated
things with this data, without having to know or care about from where it comes. 
It also dramatically simplifies unit testing.

The application controllers will use services to do stuff. Controllers can talk to
services. Services can talk to each other and repositories. Repositories can not
talk to anything else.

The architecture can be illustrated like this:

![A simplified view of the architecture](/assets/blog/2011/2011-11-16.png "A simplified view of the architecture.")

It's a clean architecture, but leaves me with some problems regarding where to put 
certain functionality.


## The problem

Here's my dilemma - where do I put additional logic? For instance, say that I want
to find all members that are in the same groups as a certain member. 

There are a couple of alternatives:

- Add a `GetGroupFriends` method to `Member`. The method could then iterate over
all the groups that a member is a member of, but that means that the Member must
be fully populated (Member -> GroupMember -> Group), which means that it must use
Entity Framework. 

- Add a `GetGroupFriendsForMember` method to `MemberService` and have the service
do the same thing as above, but with the difference that it uses a repository to
retrieve data instead of drilling down through the member object.

- Add a `GetGroupFriendsForMember` method to `GroupService` and have the service
do the same thing as above, but with the difference that it uses a repository to
retrieve data instead of drilling down through the member object.

- Add a `GetGroupFriends` method to `Member` and a `GetGroupFriendsForMember` to
one of the service classes, and make the service call the entity method.

There are pros and cons will all the approaches above, for instance:

- Placing the logic in ` Member` and using it is simple, but causes coupling to
EF. This ruins the abstract system design. Also, `Member` isn't abstract, which
means that we can't mock it easily. This is not an alternative.

- Placing the logic in a service is also simple, especially since the service is
abstract, but which service should own this logic? I would go for the group service,
but I'm really not sure.

I started placing the methods in the entities, trying to be a good OO programmer,
but the responsibilities of the entities kept growing. I now have all my relation
(is X a member of Y, does X know Z) and permission (can X edit Y, can X invite Y
to Z) logic in the various services. It’s quite nice, but in some cases, I find
that the services have to evaluate data in non-optimal ways.

There are tons of solutions to this problem. I would just really like to discuss
it here. If you have found a way that works for you, please share.