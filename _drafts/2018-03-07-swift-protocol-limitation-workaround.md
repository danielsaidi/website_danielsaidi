---
title:  Working around Swift's protocol limitations
date:   2018-03-07 21:00:00 +0100
tags:	ios swift
---


Swift has big limitations when it comes to working protocol-driven. In
this post, I'll discuss some problems that exist when extending arrays
whose elements implement a certain protocol.


## Background

In the app I'm currently working with, I have a protocol-based domain,
where the entire model and all services are represented by protocols
and the app cares nothing about any implementation details.

The big win with this approach is obvious if you have ever worked with
unit tests, dependency injection etc. For services, the app only cares
about what a protocol can do, not how it does it. It enables a flexible
architecture that allows us to dynamically create the service stack we
need in the app and later change it at any time without having to change
a single thing in the app except possibly registering a new implementation
in our IoC container (if you are not familiar with dependency injection,
don't worry - I won't talk more about it from now on).

When it comes to a protocol-based model, the big wins are pretty obvious
here as well. By designing our domain model around protocols, we do not
have to know or care about if an entity comes from the database, the api,
a disk cache etc. This gives us a lot of flexibility and never paints us
into a corner. If we for instance have a movie service, we only care about
that the service returns something that conforms to the `Movie` protocol.
Where it originates from is completely irrellevant.

So, this is all very nice indeed...but as you venture deeper and deeper
into Swift's internal protocol workings, you will eventually face some
surprising limitations, which has left us baffled, intrigued and confused.
There are so many strange things when it comes to Swift protocols, like
generics, convenience initializers etc., but I'll focus on one thing that
annoys me quite a bit: the limitations of extending arrays whose elements
inherit a certain protocol.

In this blog post, I'll use a super simple example model, to be able to
focus on the disussion and code, rather than the model itself. 


## The model

Imagine that we have a music app with a `Band` and a `BandMember` model,
as such:

```swift
protocol BandMember {
    var name: String { get }
}

protocol Band {
    var name: String { get }
    var members: [BandMember] { get }
}
```

We also have standard implementations of these protocols:

```swift
class StandardBandMember: BandMember, Codable {

    init(name: String) {
        self.name = name
    }

    let name: String
}

class StandardBand: Band, Codable {
    
    init(name: String, members: [BandMember]) {
        self.name = name
        self._members = members.map { StandardBandMember(name: $0.name) }
    }
    
    let name: String
    var members: [BandMember] { return _members }

    let _members: [StandardBandMember]    // Private in real life
```swift

This is super-simple, but let's just explain what is going on in these
standard classes. 

As you see, the standard implementations are codable. However, since
the protocols are not, `StandardBand` must have a standard `_member`
array, which will be encoded and decoded. The `members` property on the
other hand, is calculated and will not be encoded or decoded.

We will now add a simple extension method that lets us find members by
name. Should be easy, right? 


## Attempt #1 - extend `[BandMember]`

My first attempt was to add a protocol extension that extends arrays
that contains `BandMember`. It should look something like this:

```swift
extension Array where Element: BandMember {
	
	func withName(_ name: String) -> [BandMember] {
		return filter { $0.name == name }
	}
}
```

SUPER simple and so beautiful, right. Let's try this out:

```swift
let band = StandardBand(name: "U2", members: [StandardBandMember(name: "Bono")])
let members = band.members.withName("Bono")
```

This should work, but instead of a filtered array, we get an error:

```swift
Using 'BandMember' as a concrete type conforming to protocol 'BandMember'
is not supported.
```

You may have stumbled over this error before. Basically, what it says
is that you can't use the extension on `[BandMember]`, since `BandMember`
is a protocol. However, you can use it on `[StandardBandMember]`:

```swift
let band = StandardBand(name: "U2", members: [StandardBandMember(name: "Bono")])
let members = band._members.withName("Bono")	// Returns `[{name "Bono"}]`





various implementations of a protocol without
