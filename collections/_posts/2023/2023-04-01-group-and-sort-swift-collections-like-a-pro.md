---
title:  Group and sort Swift collections like a pro
date:   2023-04-01 06:00:00 +0000
tags:   swift

icon:   swift
---

Swift provides many powerful ways to sort collections, but what if you want to be able to curate the sort order slightly? Let's take a look at one way to do this.

Imagine having a type that can be grouped into named collections, for instance a `Person` that defines a city of residence:

```swift
struct Person {

    var name: String
    var city: String
}
```

In the example above, each person only defines a `name` and a `city` and the named collection has a `name` and a generic list of `items`.

Let's now say that we have a couple of persons:

```swift
let persons: [Person] = [
    .init(name: "Johanna", city: "Stockholm"),
    .init(name: "Daniel", city: "Stockholm"),
    .init(name: "Joe", city: "Washington"),
    .init(name: "Kamala", city: "San Francisco")
]
```

We can easily group persons by city and get a `Dictionary` as result:

```swift
Dictionary(grouping: persons, by: { $0.city })
```

This results in a `[String: Person]` dictionary, with one `Stockholm` key with Daniel and Johanna, one `Washington` key with `Joe` and one `San Francisco` key with Kamala:

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
  ...
```

Dictionaries are great for key-value structured data, but one problem is that the keys are unordered, which means that the result above will be shuffled every time you create it.

We could get all keys, sort them in a certain order and iterate over the sorted keys to fetch data for each key, but sometimes you may want an array with a built-in order.

Let's instead add this generic, named collection to get a bit more structure:

```swift
struct NamedCollection<Item> {

    var name: String
    var items: [Item]
}
```

We can now map the dictionary to a `[NamedCollection]` like this:

```swift
Dictionary(grouping: persons, by: { $0.city })
    .map { NamedCollection(name: $0, items: $1) }
```

Since the keys are unordered, we still get random sort order every time we do this:

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

However, it's now very easy to sort the collection by city:

```swift
Dictionary(grouping: persons, by: { $0.city })
    .map { NamedCollection(name: $0, items: $1) }
    .sorted { $0.name < $1.name } 
```

With this, we now get always get the cities in alphabetic ascending order:

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

However, consider the fact where we perhaps don't care about the overall order, but want to add one or several items topmost, for instance, in this case perhaps a currently selected city.

We can easily fix this by adding a `NamedCollection` extension that allows us to curate the content of such a collection:

```swift
public extension Collection where Element == KeyboardThemeCollection {

    func curated(topmost: [String]) -> [Element] {
        sorted {
            for name in topmost {
                if $0.name == name { return true }
                if $1.name == name { return false }
            }
            return $0.name < $1.name
        }
    }
}
```

All this does is to look if either of the compared elements has the same name as a customizable list of names to place topmost.

We can now do this:

```swift
Dictionary(grouping: persons, by: { $0.city })
    .map { NamedCollection(name: $0, items: $1) }
    .curated(topmost: ["Washington"])
```

This will always place Washington topmost then sort the rest of the cities in alphabetical order. If you want a completely random sort order for the rest, you can replace the last `return $0.name < $1.name` with `return Bool.random()`.

That's it, you're now a Swift collection grouping and sorting pro! 🎉