---
name: Cineasterna

image:  /assets/headers/cineasterna.jpg
url:    https://cineasterna.com

screenshot-1: /assets/blog/2020/201209/image.jpg
screenshot-2: /assets/blog/2020/201225/3-discover-ipad.jpg
---

[Cineasterna]({{page.url}}) is a streaming video service that lets users watch movies for free with a public library card. The service is available in Sweden, Norway & Finland.

{%- assign band = site.data.bands[id] -%}

![Cineasterna logo]({{page.image}})

After being a Cineasterna user for many years, I approached them in 2020, due to the lack of a native app for iOS and tvOS.

After showing a [SwiftUI]({{site.swiftui}}) demo, Cineasterna trusted me with developing a first app for tvOS:

![Screenshot from the Cineasterna iPad app]({{page.screenshot-1}})

Once the tvOS app was out, we could reuse much of the code and views to build a version of the app for iOS & iPadOS:

![Screenshot from the Cineasterna iPad app]({{page.screenshot-2}})

The app lets users browse movies in shelves & grids and through filters & search. Users can manage their favorites, switch libraries and watch any movies they like for free.

The app syncs data between devices and remembers movie position to let users resume a previous watch. The iOS app also supports AirPlay & Chromecast.

Since these apps, Cineasterna has trusted my company with developing an Android app as well. This app is developed by the [Studio Violet](https://studioviolet.io) with me as project lead.