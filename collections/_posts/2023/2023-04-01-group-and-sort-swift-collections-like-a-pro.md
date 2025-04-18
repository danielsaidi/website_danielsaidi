---
title:  Group and sort collections in Swift like a pro
date:   2023-04-01 06:00:00 +0000
tags:   swift

assets: /assets/blog/23/0401/
image:  /assets/blog/23/0401/image.jpg
image-show: 0

tweet:  https://twitter.com/danielsaidi/status/1642412403542433792?s=20
toot:   https://mastodon.social/@danielsaidi/110127782998293315
---

Swift provides powerful ways to group and sort collections. Let's take a look at some ways to do this and how to change the sort logic a bit.

Imagine having a type that can be grouped into named collections, for instance a `Person`:

```swift
struct Person {
    var name: String
    var city: String
}
```

Let's now say that we have a couple of persons:

```swift
let persons: [Person] = [
    .init(name: "Johanna", city: "Stockholm"),
    .init(name: "Daniel", city: "Stockholm"),
    .init(name: "Joe", city: "Washington"),
    .init(name: "Kamala", city: "San Francisco")
]
```

Using `Dictionary(grouping:by:)` we can create an extension to group persons by city:

```swift
extension Collection where Element == Person {

    func groupedByCity() -> Dictionary<String, [Element]> {
        Dictionary(grouping: self, by: { $0.city })
    }
}
```

and call it like this:

```swift
persons.groupedByCity()
```

This returns a dictionary with city name keys and people array values:

```swift
[0] = {
  key = "Stockholm"
  value = 2 values {
    [0] = {
      name = "Johanna"
      city = "Stockholm"
    }
    [1] = {
      name = "Daniel"
      city = "Stockholm"
    }
  }
}
[1] = {
  key = "San Francisco"
  value = 1 value {
    [0] = {
      name = "Johanna"
      city = "Stockholm"
    }
  }
}
...
```

Dictionaries are great for key-value data, but a problem is that keys are unordered, which means that the result above will be shuffled every time you create it.

We could get and sort all keys, then iterate over the sorted keys to fetch users, but let's instead add this generic, named collection to get a bit more structure:

```swift
struct NamedCollection<Item> {

    var name: String
    var items: [Item]
}
```

We can now map the dictionary to a `[NamedCollection]` like this:

```swift
extension Collection where Element == Person {

    func groupedByCity() -> [NamedCollection<Element>] {
        Dictionary(grouping: persons, by: { $0.city })
            .map { NamedCollection(name: $0, items: $1) }
    }
}
```

Since the keys are unordered, we still get random order every time we do this:

```swift
[0] = {
  name = "Stockholm"
  items = 2 values {
    [0] = {
      name = "Johanna"
      city = "Stockholm"
    }
    [1] = {
      name = "Daniel"
      city = "Stockholm"
    }
  }
}
[1] = {
  name = "Washington"
  ...
```

But it's now very easy to add sorting to the mix and sort the groups by city name:

```swift
extension Collection where Element == Person { 

    func groupedByCity() -> [NamedCollection<Element>] { 
        Dictionary(grouping: persons, by: { $0.city })
            .map { NamedCollection(name: $0, items: $1) }
            .sorted { $0.name < $1.name }   // <--
    } 
} 
```

With this, we always get the cities in alphabetic ascending order:

```swift
[0] = {
  name = "San Francisco"
  items = 1 value {
    [0] = {
      name = "Kamala"
      city = "San Francisco"
    }
  }
}
[1] = {
  name = "Stockholm"
  ...
```

Now consider a situation where we want to sort this in alphabetical order, but also want to place some cities topmost. We may have a "city" of the week, a currently selected city etc.

This would have been easy to do with the dictionary, where we could fetch a certain city key, remove that key, then listed the remaining keys (although in random order).

With the named collection, we have a sorted array, where we want to place a certain item first, then list the rest in alphabetical order. This could involve iterating the collection many times to find and remove the city we're after, then append the remaining, sorted cities.

A more efficient approach is to create a custom sort function for the `NamedCollection`:

```swift
public extension Collection where Element == NamedCollection {

    func sorted(firstmost: [String]) -> [Element] {
        sorted {
            for name in firstmost {
                if $0.name == name { return true }
                if $1.name == name { return false }
            }
            return $0.name < $1.name
        }
    }
}
```

All this does is to check if any compared element name is in the list of names to place first. Since we iterate over the `firstmost` collection, the provided order will be preserved.

We can now do this:

```swift
persons
  .groupedByCity()
  .sorted(firstmost: ["Washington"])
```

This will place `Washington` first, then sort the rest in alphabetical order. We could replace `return $0.name < $1.name` with `return $0.name > $1.name` to use a descending order, and `return Bool.random()` to sort randomly.

We could extend `Collection where Element == Person` further, to specify the topmost cities at the same time as grouping them. The possibilities are basically endless, so you are free to come up with a design that fits your domain and use-case.

That's it, you're now a Swift collection grouping and sorting pro! 🎉