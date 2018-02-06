---
title:  "Where do I put the logic?"
date:   2011-11-16 12:00:00 +0100
tags: 	architecture
---


I could use some advice regarding a project I'm currently working on. It's a web
site where people can sign up and join various groups (did I hear a “Facebook is
already doing that”?) and do stuff depending on what groups they belong to.

The purpose of the web site is not important, nor is the big domain model. Let’s
ignore all complexity and focus on the Member and Group entities.

Before we begin, I am going to talk about **interfaces** in this post, since the
project is written in C#. In Objective-C and Swift, the equivalence of interface
is `protocol`. That is, a way to design for the what's and not the how's.

In this project, I have a repository interface for each entity. The repositories
main implementations have the sole responsibility to hide Entity Framework from
the rest of the system. I also have other repository implementations, e.g. fake
ones that just create fake data, cache decorators etc.

I then have service interfaces (I know, I know, bad name) for more sophisticated
functionality. Each service uses one or several repositories, which means that a
repository can be really simple and just provide data from a certain data source,
while a service can do more sophisticated things with this data, without having
to know or care about from where the data comes. It also dramatically simplifies
unit testing.

The controllers in the application will then use the various services to do stuff.
Controllers can talk to services. Services can talk to each other and repositories.
Repositories can not talk to anyone else.

This architecture can be illustrated (quite simplified) as such:

![A simplified view of the architecture](/assets/blog/2011-11-16.png "A simplified view of the architecture.")

Now, here is my dilemma...**where do I put additional logic**? Say, for instance,
that I want to find all members that are in the same groups as a certain member.

I could then:

- Add a `GetGroupFriends` method to `Member`. The method could then iterate over
all the groups that a member is a member of, but that means that the Member must
be fully populated (Member -> GroupMember -> Group), which means that it must be
an Entity Framework. This leak EF out throughout the system.

- Add a `GetGroupFriendsForMember` method to `MemberService` and have the service
do the same thing as above, but with the difference that it uses a repository to
retrieve data instead of drilling down through the member object.

- Add a `GetGroupFriendsForMember` method to `GroupService` and have the service
do the same thing as above, but with the difference that it uses a repository to
retrieve data instead of drilling down through the member object.

- Add a `GetGroupFriends` method to `Member` and a `GetGroupFriendsForMember` to
one of the service classes, and make the service call the entity method.

There are pros and cons will all the approaches above:

- Placing the logic in the Member entity is simple, and using the method is also
quite straightforward, but it will cause coupling to EF. Also, the Member entity
is not abstract, which means that we can't mock it easily (although some mocking
frameworks support this.

- Placing the logic in a service is also simple, especially since the service is
abstract, but which service should then own this logic? I would go for the group
service, but I do not really know.

I started placing the methods in the entities, trying to be a good OO programmer,
but the responsibilities of the entities then grew and grew.

Now, I have all my relation- (is X a member of Y, does X know Z) and permission-
(can X edit Y, can X invite Y to Z) -related logic in the various services. It’s
quite nice, but in some cases, I find that the services have to evaluate data in
really non-optimal ways.

There are tons of various solutions to this problem. I would just really like to 
discuss this. If you have found a way that works for you, please share 🙂