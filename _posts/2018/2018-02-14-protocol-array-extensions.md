---
title:  Protocol array extensions not working
date:   2018-02-14 08:00:00 +0100
tags:	ios swift
image:  http://danielsaidi.com/assets/blog/2018-02-14.png
---


In an app I'm working on, I had an idea on how to redesign the way we extend our
protocol-based domain model. However, what first looked like a walk in the park,
turned into a `Swift` nightmare, with problems that I am still struggling with.
Join me in my endeavors...and reach out a helping hand if you have one.


## Adding a simple `Person` protocol

In this post, I'll use a fictional, super simple model, so if you think the model
itself makes no sense (e.g. "why the hell are you querying a memory collection
instead of using a search service"), keep in mind that the code you're about to
read is just fictional. Relax, put on your problem solving hat and let's go.

In Swift, protocol extensions is a nice way to provide protocol implementations
with a bunch of automatically logic. It reduces the need for duplicate code and
base classes by using the functionality implemented by an implementation, and
using it to provide calculated properties, extra functions etc.

For instance, consider a `Person` protocol that requires its implementations to
have two properties: `firstName` and `lastName`. Instead of also requiring that
they implement a `fullName` property, we can add it to all implementaitons by
using a protocol extension:

```swift
extension Person {

    var fullName: String {
        return "\(firstName) \(lastName)"
    }
}
```

Now, on to what I'm currently struggling with: protocol collection extensions.


## Extending `Person` with friends

Let's extend the `Person` protocol a bit. If we consider that a person should be
able to have friends (seems nice), we could add a `friends: [Person]` property to
the `Person` protocol.

If we then want to be able to search for a person's friends, we could use `filter`
to find all friends that match a certain query:

```swift
let matchingFriends = person.friends.filter { $0.fullName.contains(query) }
```

However, if we are to do this many times, we will duplicate a piece of logic that
in fact actually corresponds to a domain action, which may change over time.

A better approach would be to encapsulate this logic in an extension, as such:

```swift
extension Person {

    func friends(matchingQuery query: String) -> [Person] {
        return friends.filter { $0.fullName.contains(query) }
    }
}
```

You can now use this extensions instead:

```swift
let matchingFriends = person.friends(matchingQuery: query)
```

In my opinion, this is much more readable. It also gives us the possibility to
change and improve the underlying "search" logic in one single place, instead of
having to change every `filter` call everywhere.

Still...


## Extending `Person` collections instead

Even though the extension above is ok, I think that it does **not** belong to
the `Person` protocol, but rather to the `Friend` collection. The code above
means that the query logic only applies to friends of a certain person, while
in fact, it should apply to any `Person` collection, friends or not, right?

So I decided to convert it to a collection extension instead, as such:

```swift
extension Collection where Element: Person {

    func matchingQuery(_ query: String) -> [Person] {
        return filter { $0.fullName.contains(query) }
    }
}
```

Even better! You can now use this extensions for every person collection you
may stumble upon:

```swift
let matchingFriends = person.friends.matchingQuery(query)
```

...or CAN YOU?

NO

YOU

CAN'T! (At least not without any additional piece of code)

Since the `Person` property has a `friend` array of type `Person`, and not an array
of a concrete type (why would it!?) the `person.friends.matchingQuery(query)` fails
with this error:

`Using 'Person' as a concrete type conforming to protocol 'Person' is not supported`

This does not happen if I perform the same operation on an array that contains a
**type** that implements `Person`, e.g.:


```swift
struct PersonStruct: Person {
    var firstName: String
    var lastName: String
}

let friends = [PersonStruct(firstName: "foo", lastName: "bar")]
let matchingFriend = friends.matchingQuery("foo")   // Great success!
```

However, if I specify friends to be a `[Person]` array, the error arises once more.

If you want to play around with the code, you can grab sample code [here](https://gist.github.com/danielsaidi/8c5ffb82d68b8dd4e869062bdcbfc7ff).


## Conclusion

I have no conclusion yes. I'm lost. I can not believe that `Swift` can not handle
this scenario. It makes protocol driven development really painful, and forces us
to place our logic where it does not belong.

But maybe I'm just missing a piece of code somewhere? If you know how to solve it,
or if you know that this absolutely can not be done, please leave a comment below.

Until then, I'll be rocking in a corner somewhere.

Thanks for reading.
