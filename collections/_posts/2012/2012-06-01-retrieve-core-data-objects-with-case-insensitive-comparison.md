---
title: Retrive Core Data objects with case-insensitive comparison
date:  2012-06-01 12:45:00 +0100
tags:  ios
icon:  swift
---

I'm currently building an iOS app that uses core data for data persistency. It
works great, but as I started retrieving entities by name, I noticed that they
arrived in a strange order:

* Object 1
* Object 3
* object 2

There is probably some case-sensitive sorting issue showing its ugly face, right.
This is the code I used to sort the data:

```objc
NSSortDescriptor *sortDescriptor = [[NSSortDescriptor alloc] 
    initWithKey:@"name" 
    ascending:YES
];
```

The solution was to add selector to the sort, like this:

```objc
NSSortDescriptor *sortDescriptor = [[NSSortDescriptor alloc] 
    initWithKey:@"name" 
    ascending:YES 
    selector:@selector(caseInsensitiveCompare:)
];
```

And voilá - the list will now look like this:

* Object 1
* object 2
* Object 3

This also works for local-specific characters, like Swedish å, ä and ö.