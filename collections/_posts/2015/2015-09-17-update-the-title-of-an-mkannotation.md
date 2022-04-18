---
title: Update the title of an MKAnnotation
date:  2015-09-17 22:04:00 +0100
tags:  ios swift geo
---

I'm building a map app where users can save personalized content and present it 
with custom pins, icons, colors etc. I therefore need to update how an `MKAnnotation`
is presented and its title.

When the user taps a pin on the map, the app will reverse geocode its coordinate
and use it to display the full address in the callout subtitle.

![iOS Simulator Screen](/assets/blog/2015/2015-09-17-simulator.png)

This may seem trivial, but was a hassle. If the geocoding operation takes too long
and completes after the callout has been shown, the label text is not changed.

Turns out that you have to add some more code to make this work. Instead of just
setting the subtitle, you have to tell the app that you *will* change it, change
it, then tell the app that you *have* changed it.

The code for this becomes:

```swift
myAnnotation.willChangeValueForKey("subtitle")
myAnnotation.subtitle = "An address somewhere in the world"
myAnnotation.didChangeValueForKey("subtitle")
```

As soon as I added this extra piece of code to the app, the reverse geocoder could
update the subtitle without problems, even when the operation took some time.

Since my annotations inherit a class called `MapAnnotation`, I decided to specify
the subtitle as such:

```swift
var subtitle: String? {
   willSet { willChangeValueForKey("subtitle") }
   didSet { didChangeValueForKey("subtitle") }
}
```

This triggers the required events in the correct order, every time the subtitle
property value is changed.