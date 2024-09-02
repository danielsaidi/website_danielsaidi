---
title: CLGeocoder fails silently
date:  2012-08-08 20:48:00 +0100
tags:  archive geo

icon:  swift
assets: /assets/blog/12/0808
image:  /assets/blog/12/0808.png
---

I'm currently developing a location-based app for iPad and iPhone, that will allow users to store locations and use custom icons and colors for the pins.

This is how it currently looks:

<img src="{{page.image}}" width="250" alt="App Screenshot" />

In the app, you can pin your location, pin any place by pressing the map or search for an address, which uses `CLGeocoder` to find a coordinate for an address.

Searching for addresses hasn't been working great, though. If I search for a street in my current city, I get results in other towns.

For clarity, this is the exact method body I use to kick off a search operation:

```objc
CLLocationCoordinate2D *coord = self.mapView.userLocation.location.coordinate;
CLRegion *region = [[CLRegion alloc] initCircularRegionWithCenter:coord
radius:10000
identifier:nil];

id<ForwardGeocoder> geocoder = [ObjectFactory getForwardGeocoder];
geocoder.delegate = self;
[geocoder encodeAddressString:self.searchBar.text inRegion:region];
```

I first accepted the bad result as a limitation in CLGeocoder’s Swedish geocoding abilities, but then noticed that the same problem occurred for American addresses as well.

As I couldn't get things to work, I decided to return to the Google-based geocoder I used before - [Björn Sållarp's BSGeocoder](https://github.com/bjornsallarp/BSForwardGeocoder). I tried `CLGeocoder`, since `BSForwardGeocoder` didn't support viewport biasing. As it now does, I decided to give it another try.

The app uses an abstract protocol to define how geocoding is done. This makes it really easy to switch implementations. As I replaced Björn's approach with `CLGeocoder`, I also changed the protocol to use `CLRegion` instead of `MKCoordinateRegion`.

When I re-added Björn's code, I changed my wrapper class (that implements the protocol and serves as a bridge between the app and the geocoding) so it used a `CLRegion` as well. As I did, I faced the same behavior using Bjorn's implementation.

Since, Björn's code *used to work* and now didn't, I realized that the only difference between the old and new implementations was that I `CLRegion` instead of `MKCoordinateRegion`. This made me look into the resulting `CLRegion` and notice that the center lat/long was zero!

How could this be? I verified that the user location was not zero, so how could
the resulting `CLRegion` have a zero center? Reading the documentation, I found 
the answer:

> The identifier must not be nil!

So, all this was due to me using a nil identifier, but the app never gave me so much as a silent warning about this. If I pass in nil as an identifier, which I did:

* I don't get a nil CLRegion in return, but a region with a nil center.
* I don't crash the application.
* I don't get any errors whatsoever.

I instead end up with a valid CLRegion, but invalid properties. Talk about silent crashes.