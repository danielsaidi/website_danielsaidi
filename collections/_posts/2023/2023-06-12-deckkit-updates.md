---
title:  DeckKit Updates
date:   2023-06-12 22:00:00 +0000
tags:   swiftui open-source

image:  /assets/headers/deckkit.png
---

DeckKit 0.7 is out, with a new shuffle animation that makes shuffling a deck very enjoyable. Let's take a look at what's new in this minor update.

{% include kankoda/data/open-source.html name="DeckKit" version="0.7.0" %}

![DeckKit logo]({{page.image}})


## New shuffle animation

DeckKit 0.7 has a new `DeckShuffleAnimation` that can be used to shuffle a deck of cards with a nice physical shuffle animation.

All you have to do to use this animation is to create a `@StateObject` in the view with the `DeckView`, then bind the animation to the deck item views using the `withDeckShuffleAnimation` view modifier, then call `shuffle` to shuffle the deck with a nice shuffle animation.

The shuffle animation lets you customize the max rotation, as well as the max horizontal and vertical offset. You can also specify how many shuffles you want to make each time you call the function.


## Conclusion

Other than this, DeckKit 0.7 also adds a `shuffle` function to the `Deck` type and a convenience initializer to the `DeckView`. For more information, see the [project repository]({{project.url}}) and the [release notes]({{project-version}}).