---
title: Building a video streaming app for tvOS in SwiftUI
date:  2020-12-09 07:00:00 +0100
tags:  apps swiftui tvos video
assets: /assets/blog/2020/201209/
image:  /assets/blog/2020/201209/image.jpg

cineasterna: https://www.cineasterna.com/en/
collection-view: https://github.com/defagos/SwiftUICollection
defagos: https://twitter.com/defagos
---

In this post, I'll discuss how I built a movie-streaming app for tvOS in SwiftUI, for the Swedish streaming service [Cineasterna]({{page.cineasterna}}), which lets people stream videos using their public library card.

![A screenshot of the app]({{page.assets}}image.jpg)


## Main tabs

The app is oriented around four main tabs - Discover (Selected Titles), All Titles, Search and Settings. All text in the app is translated using a central localization data source.

![A screenshot of the main tabs]({{page.assets}}tabs.jpg)

If a user is logged in and has any favorites, an additional tab is added to let the user quickly find and manage her/his favorites.


### Discover

The Discover screen is a vertical list with horizontally scrolling shelves. When the user scrolls to a new section, the header transforms to a navigation link that takes the user to the specific list. 

![A screenshot of a focused list header]({{page.assets}}headers.jpg)

Navigating to a list renders it as a vertical grid. Grids use the same underlying collection view as shelves, but with a different layout.

![A screenshot of a list screen]({{page.assets}}list-screen.jpg)

This screen lazy loads more content as the user scrolls down and displays the last content on the page. More about lazy loading later.


### Favorites

The Favorites screen only shows up if the user has any favorites. Unlike the Discover screen, it only has a single section and therefore uses a grid instead of shelves.

![A screenshot of the Favorites screen]({{page.assets}}favorites.jpg)

This screen doesn't load more content as the user scrolls, since the api returns all user favorites at once.


### All Titles

The All Titles screen can be used to explore all the movies Cineasterna have to offer. Just like Favorites, it has a single section and therefore uses a grid.

![A screenshot of the All Movies screen]({{page.assets}}all-movies.jpg)

Since the Cineasterna service offers thousands of movies, I added filtering options topmost. Filtering is done in a custom picker, since native SwiftUI pickers didn’t work on tvOS.

![A screenshot of an All Movies filter screen]({{page.assets}}all-movies-filter.jpg)

When a filter is active, the filter button is tinted with the app's yellow accent color. Many filters can be active at the same time.

This screen lazy loads more content as the user scrolls down. As we'll discuss later, shelves and grids handle lazy loading differently.


### Search

The Search screen can be used to search for movies. Just like Favorites and All Titles, it has a single section and therefore uses a grid.

![A screenshot of the Search screen]({{page.assets}}search.jpg)

Search has a custom-made header, since there's no native search component in SwiftUI (yet). It opens a full screen input and performs a search when tapping ”done”.

![A screenshot of the search input view]({{page.assets}}search-input.jpg)

I faced two problems with this. First, dictation inserts invalid chars into the dictation string, so it must be cleaned up. Also, there's no native way to change "done" to "Search" without wrapping a native UIKit text field.

This screen also lazy loads more content as the user scrolls.


### Settings

The settings screen is pretty limited in design and functionality so far. It lets the user login, logout, switch library and get more information about the service. 

![A screenshot of the Settings screen]({{page.assets}}settings.jpg)

This screen will have more information and will also have QR codes that lead to support pages, so that user can scan the QR codes on her/his phone to get help.


## Movie Screen

The movie screen is clean, with a fullscreen background and information added on top. 

![A screenshot of the Movie screen]({{page.assets}}movie.jpg)

Loaning a movie, which is free (public libraries, remember?), opens a movie player as a new full screen modal. Trailers are currently YouTube links, so they show a scannable QR code:

![A screenshot of the Movie screen]({{page.assets}}movie-qr.jpg)

Overall, I like the way QR codes can be used to let users explore more content on their mobile devices, but I think it has to be better explained, since users may not be all that famoliar with scanning QR codes.


## Technology

Let's go through some technological aspects of the app as well.


### Performance

I first built shelves with LazyVStack and nested LazyHStacks and grids with LazyVGrid, but performance was horrible. I tried *everything* and eventually found [this great collection view]({{page.collection-view}}) by [@defagos]({{page.defagos}}).

This collection view wraps a native `UICollectionView` and uses the latest collection view techniques, like diffable data sources. It works GREAT, has amazing performance and also remembers scroll offset.

The only drawbacks with this wrapper, is that I have to resize images in a very precis manner for them to look good. Also, navigation links don’t work, which probably has to do with the fact that the movie covers are rendered within a hosting controller. On tvOS, I can use sheets to work around this.

Since having these performance problems, I have verification from Apple engineers, that scrolling stacks and grids is broken in SwiftUI on tvOS. I just wish they could have mentioned this somewhere.


### Async Images

Movie covers are downloaded with Kingfisher, which needed some fiddling to perform well on the TV. I use a pre-processor that scales images down to exact points and use disk cache.

Note that tvOS apps are recycled way less often than iOS apps. You should therefore consider setting a manual cache lifetime limit, to avoid that Kingfisher keeps images around forever.


### Lazy Loading

Both shelves and grids can lazy load more content as the user scroll down and reaches the end of the fetched content. This was easy to do, by looking at the movie when rendering a list item. 

For shelves to trigger a lazy load, the movie must be the first movie in the last available list. For grids, it must be the last available movie. 

If this is true, the collection view automatically triggers an injected action that performs an async data fetch and puts more content to the end of the collection. The collection is observed and automatically updates the view.


### Video Player

The video player was easy to build, by wrapping an `MPPlayerViewController` and giving it a url and start position. It remembers the position of each unique movie and restores it the next time that movie is played. Reaching the end resets this position and closes the player.



## Conclusion

To wrap things up, SwiftUI is amazing, but tvOS support is not good and the scroll performance of stacks and grids is horrible. Many views and api:s are still missing, so you have to wrap UIKit components. 

Focus is also a real problem when navigating back, which causes the tab view to reclaim focus. (**Update**: As of spring 2022, this seems to have been solved and now works great).

All in all, this was a very fun project that I'm proud to release. I’m happy to help services like [Cineasterna]({{page.cineasterna}}) and public libraries help people to discover culture from all over the world.