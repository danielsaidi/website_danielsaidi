---
title:  "CLGeocoder fails silently"
date: 	2012-08-08 20:48:00 +0100
categories: apps
tags: 	ios geocoding
---


I am currently developing a location-based app for iPad and iPhone. This is how
it looks:

<img src="/assets/img/blog/2012-08-08-app.png" width="250" alt="App Screenshot" />

In the app, you can pin your current location, pin any place by pressing the map
or search for an address.

Searching for addresses has not been working that great, though, even though I'm
using the native CLGeocoder class. I live in Stockholm, Sweden, where we have an
address for e.g. Kungsgatan 10. However, if I entered that search query, I would
get random hits in other Swedish cities, like Nykoping and Uppsala.

For clarity, this is the exact method body I use to kick off a search operation:

{% highlight obj-c %}
CLLocationCoordinate2D *coord = self.mapView.userLocation.location.coordinate;
CLRegion *region = [[CLRegion alloc] initCircularRegionWithCenter:coord
radius:10000
identifier:nil];

id<ForwardGeocoder> geocoder = [ObjectFactory getForwardGeocoder];
geocoder.delegate = self;
[geocoder encodeAddressString:self.searchBar.text inRegion:region];
{% endhighlight %}

I first accepted the bad search results as a limitation in CLGeocoder's geocoding
abilities for Swedish addresses, but since the same problem occurred for American
addresses as well, that could not be the case.

As I could not get things to work with the CLGeocoder, I decided to return to the
Google-based implementation I used before - [Björn Sållarp's BSGeocoder](https://github.com/bjornsallarp/BSForwardGeocoder).
I tried the CLGeocoder, since BSForwardGeocoder didn't support viewport biasing,
but as it now does, I decided to give it another go.

In the app, I use an abstract protocol to define how geocoding is done in the app.
This makes it really simple to replace one implementation with another one. As I
replaced Björn's approach with CLGeocoder, I changed the protocol to use CLRegion
instead of an MKCoordinateRegion.

When I now re-added Björn's code, I changed my wrapper class (that implements the
protocol and serves as a bridge between the app and the geocoding implementation)
so it used a CLRegion as well. And as I did...I faced the same, strange behavior,
using Bjorn's implementation.

So, Björn's geocoder *used* to work, and now didn't. Considering that, I realized
that the only difference between the old and new implementations (except that the
library was updated a bit by Björn) was that I now used the CLRegion class instead
of MKCoordinateRegion.

I therefore decided to inspect the resulting CLRegion instance in the code above,
and now noticed that the center lat/long was zero!

How can this be? I had verified that the user location was not zero, so how could
the resulting CLRegion have a zero center? Reading the documentation, I found the
answer:

> The identifier must not be nil!

So, all this was doe to the fact that I used a nil identifier, but the app never
gave me so much as a silent warning about this. If I pass in nil as an identifier,
which I did:

* I do not get a nil CLRegion in return, but a region with a nil center
* I do not crash the application
* I do not get any errors whatsoever

Instead, I end up with a valid CLRegion, but with invalid properties. Talk about
crashing silently.