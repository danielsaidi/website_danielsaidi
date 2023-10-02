---
name: Cineasterna

image:  /assets/headers/cineasterna.jpg
url:    https://cineasterna.com

screenshot-1: /assets/blog/2020/2020-12-09/image.jpg
screenshot-2: /assets/blog/2020/2020-12-25/3-discover-ipad.jpg
---

[Cineasterna]({{page.url}}) is a Swedish streaming video service, where users can loan movies for free with their public library card. The service is available in Sweden, Norway and Finland.

{%- assign band = site.data.bands[id] -%}

![Cineasterna logo]({{page.image}})

I've been a Cineasterna user for many years, and approached the company behind Cineasterna in 2020, due to their lack of native apps for iOS and tvOS.

After a demo of what [SwiftUI]({{site.swiftui}}) (which had released just a year prior) was capable of, Cineasterna trusted my company [Kankoda]({{site.kankoda}}) with developing an app for tvOS and the Apple TV.

![Screenshot from the Cineasterna iPad app]({{page.screenshot-1}})

Once the tvOS app was out and proven to work well, we could reuse much of the functionality and views to develop a mobile version for iOS and iPadOS.

![Screenshot from the Cineasterna iPad app]({{page.screenshot-2}})

The app lets users browse movies in shelves and grids and explore the total movie stock through filters and search. Users can manage their favorites, switch libraries and loan/watch any movie they like.

The app syncs data between devices and remembers movie position (locally only) when a user resumes a previous watch. The iOS app for iPhone and iPad also supports AirPlay and Chromecast.

Since the release of these apps, Cineasterna has trusted Kankoda with developing an Android app as well. This app is developed by the amazing [Studio Violet](https://studioviolet.io) with Kankoda as project lead.