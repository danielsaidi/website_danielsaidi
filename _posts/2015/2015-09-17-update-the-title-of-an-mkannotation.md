---
title:  "Update the title of an MKAnnotation"
date: 	2015-09-17 22:04:00 +0100
tags: 	ios swift mapkit mkannotation
---


In an app that I am building, I have a map where users can save personal content
and present it in a beauuutiful way. Ok, enough with the sales pitch.

When the user taps a pin on the map, the app will reverse geocode its coordinate
and use it to display the full address in the callout's subtitle.

![iOS Simulator Screen](/assets/blog/2015-09-17_simulator.png)

This may seem trivial, but turned out to be a hassle. If the geocoding operation
takes too long and completes after the callout has been shown, the label text is
not changed.

Turns out that you have to add some more code to make this work. Instead of just
setting the subtitle, you have to tell the app that you *will* change it, change
it, then finally tell the app that you *have* changed it.

The code for this becomes:

```swift
myAnnotation.willChangeValueForKey("subtitle")
myAnnotation.subtitle = "An address somewhere in the world"
myAnnotation.didChangeValueForKey("subtitle")
```

I really don't know why this has to be added, but as soon as I added this extra
piece of code to the app, the reverse geocoder could update the subtitle without
problems, even when the operation took some time.

Since all my annotations inherit a class called MapAnnotation, I then decided to
specify the subtitle property as such:

```swift
var subtitle: String? {
   willSet { willChangeValueForKey("subtitle") }
   didSet { didChangeValueForKey("subtitle") }
}
```

This triggers the required events in the correct order, every time the subtitle
property value is changed.
