---
title: Protocol array extensions not working
date:  2018-02-14 08:00:00 +0100
tags:  ios swift
image: /assets/blog/2018/2018-02-14.png
---

In an app of mine, I had an idea on how to redesign how we extend protocol-based
domain models. However, what first looked like a walk in the park, turned into a
Swift nightmare, with problems that I am still struggling with.


## Disclaimer

In this post, I will intentionally use a simple example model. If you think that
it makes no sense (e.g. "why the hell are you using a memory collection, instead
of a search service"), just keep in mind that the code you are about to see is a
work of fiction. Relax, put on your problem solving hat and let's go.


## Protocol extensions

In Swift, protocol extensions is a nice tool to provide protocol implementations
with a bunch of logic that makes use of the protocol specification. This reduces
the need for duplicate code, base classes etc. by using the protocol contract to
provide calculated properties, additional functionality etc.

For instance, consider a `Person` protocol that requires implementations to have
two properties: `firstName` and `lastName`. Instead of also requiring that these
implementations implement `fullName`, we can provide it as a calculated property:

```swift
extension Person {

    var fullName: String {
        return "\(firstName) \(lastName)"
    }
}
```

This is really convenient in many cases. Just make sure that you do NOT use this
approach for functionality that should be impemented by each implementation.


## Protocol collection extensions

Now, let's move on to what I'm currently struggling with - extending collections
where the elements are of a certain protocol.

Let's extend the `Person` protocol a little. If we consider that a person should
be able to have friends (seems nice), we could add `var friends: [Person]` to it.

If we later would like to be able to search for a person's friends, we could now
use `filter` to find all friends that match a certain query:

```swift
let matchingFriends = person.friends.filter { $0.fullName.contains(query) }
```

However, if we do this in many places, we will duplicate a piece of logic that I
think should be a reusable function, since it defines a standard way to filter a
collection of persons.

If we were to go down the domain driven rabbit hole and talk services and how to
do this "correctly", let's just keep it simple and discuss how we could solve it
in the easiest possible way. 

One way could be to define this as an additional extension to `Person`, as such:

```swift
extension Person {

    func friends(matching query: String) -> [Person] {
        return friends.filter { $0.fullName.contains(query) }
    }
}
```

You could then use this extensions like this:

```swift
let matchingFriends = person.friends(matching: query)
```

In my opinion, this is much more readable. You can use the `friends` property to
get all friends and this extension to get a filtered collection. Still, I really
don't like this approach for many reasons. 


## Extending `Person` collections instead

If we keep the domain discussions away, one big drawback with the approach above
is that it's only valid when you have a person, while in reality, this extension
could apply to every collection where each element is a `Person`, friends or not.

So I decided to convert it to a collection extension instead:

```swift
extension Collection where Element: Person {

    func matching(_ query: String) -> [Person] {
        return filter { $0.fullName.contains(query) }
    }
}
```

Even better! You can now use this extensions for every person collection you may
stumble upon:

```swift
let matchingFriends = person.friends.matching("peter")
```

...or CAN YOU?

NO.

YOU.

CAN'T!

Since `Person` is a protocol and not a concrete type, the code above won't work!
If you try it, it will fail with this error:

```
Using 'Person' as a concrete type conforming to protocol 'Person' is not supported
```

This does not happen if I perform the same operation on an array that contains a
type that implements `Person`, e.g.:

```swift
struct PersonStruct: Person {
    var firstName: String
    var lastName: String
}

let persons = [PersonStruct(firstName: "sarah", lastName: "huckabee")]
let matches = persons.matching("ah huck")   // Great success!
```

However, if you convert friends as a `[Person]` array, the error arises:

```swift
let persons: [Person] = [PersonStruct(firstName: "sarah", lastName: "huckabee")]
let matches = persons.matching("ah huck")   // Great success!
```


## Conclusion

This was an unexpected and unfortunate discovery for me, since I based my entire
domain model on protocols. However, it led me to experiment more on these things,
where I eventually came to the conclusion that protocols are not good for models.
Instead, I now use structs to the greatest extent and use protocols for services
and other logic parts of my apps.

However, I think that Swift should improve the extension model so that the above
code works even for protocols.