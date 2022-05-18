---
title:  Building a video streaming app for iOS in SwiftUI
date:   2020-12-25 07:00:00 +0100
tags:   swiftui
assets: /assets/blog/2020/2020-12-25/
image:  /assets/blog/2020/2020-12-25/1-title.jpg

post-tvos:  /blog/2020/12/09/building-a-tvos-app-in-swiftui
cineasterna: https://www.cineasterna.com/en/
---


This is a follow-up post to [this previous blog post]({{page.post-tvos}}), where I discussed my experiences with building a video streaming app for tvOS, using SwiftUI. This post will discuss how I ported the app to iOS. 

![A screenshot of the app]({{page.assets}}1-title-sml.jpg)


Throughout the post, I will refer to this app as an iOS app, although it targets both iOS and iPadOS.


## Project setup

The app is a universal SwiftUI app, which means that it runs on iOS and iPadOS as well as on macOS. However, due to time restrictions, I have not put any effort into making it functional on macOS.

The business logic is kept in a library that is shared by the tvOS and the iOS app. It's pulled in with Swift Package Manager (SPM), which is very convenient. 

Since the iOS app supports Chromecast, I had to use CocoaPods to pull in the external `GoogleCast` library as well. This makes the iOS project setup a bit messier than the tvOS project.


## Main tabs

Just like the tvOS app, this app has four main tabs: **Discover, All Movies (A-Z), Search** and **Profile**. 

![A screenshot of the main tabs]({{page.assets}}2-tabs.jpg)

If a logged in user has any favorites, an additional Favorites tab is added to the tab bar. If no user is logged in, the Profile tab says "Login" instead of "Profile".


### Discover

The Discover screen is a vertical list with horizontally scrolling "shelves". It first loads a couple of fixed lists for the selected library (news, popular etc.), then loads curated "themes" like "Christmast movies".

![A screenshot of the discover tab]({{page.assets}}3-discover.jpg)

This caused me much headache in tvOS, where stacks and grids have horrible performance (probably due to the focus engine). I had to work around that problem by using a wrapped `UICollectionView` instead of stacks and grids, which took a lot of time. 

This is not true for iOS, though, where stacks and grids have amazing performance. So I was able to just use native stacks and views without any issues whatsoever, which saved me a lot of time.

The layout is configured to look good on larger screens as well. This is how the same Discover tab looks on a 12.9" iPad Pro in landscape:

![A screenshot of the discover tab on iPad Pro 12.9"]({{page.assets}}3-discover-ipad.jpg)


### Lists and themes

Shelf sections are tappable and takes you to the specific list or theme, using a plain push animation:

![A screenshot of tappable shelf sections]({{page.assets}}4-shelf-sections.jpg)

Navigating to a list or theme renders it as a `LazyVGrid`, with 3 movies per row on compact devices.

![A screenshot of a list screen]({{page.assets}}6-list-grid.jpg)

However, 3 movies per row will obviously not work for large screens, since the height would become too large. So instead of a fixed item count, I chose a size range, which works great for iPads as well:

![A screenshot of a list screen on iPad]({{page.assets}}6-list-grid-ipad.jpg)

However, I have some layout problems that I'm still struggling with. First of all, I have to tweak the size range for iPad devices, since the covers become too small. Also, since I have to specify a width instead of a height, covers get varying heights, which doesn't look that good.

This screen lazy loads more content as the user scrolls down and displays the last content on the page. More about lazy loading later.


### Favorites

The Favorites screen only shows up if the user has any favorites. Unlike the Discover screen, it only has a single section and therefore uses a grid instead of shelves.

![A screenshot of the favorites screen]({{page.assets}}7-favorites.jpg)

This screen does not lazy load more content as the user scrolls, since the api returns all user favorites without pagination.


### All Movies

The All Movies screen can be used to explore all the movies that Cineasterna has to offer. Just like Favorites, it has a single section and therefore uses a grid.

![A screenshot of the favorites screen]({{page.assets}}8-all-movies.jpg)

Since this is a vast data source, I added filtering options topmost. The filtering is done in custom, simple pickers that support optionals and multi selections.

![A screenshot of the favorites filtering]({{page.assets}}8-all-movies-filter.jpg)

When a filter is active, the filter button is tinted with the app's yellow accent color. Many filters can be active at the same time.

This screen lazy loads more content as the user scrolls down. As we'll discuss later, shelves and grids handle lazy loading differently.


### Search

The Search screen can be used to search for movies. Just like Favorites and All Movies, it has a single section and therefore uses a grid.

![A screenshot of the search screen]({{page.assets}}9-search.jpg)

The search bar is custom made and decorates a standard `TextField` with a wrapped clear button and a trailing cancel button. I'd love a native way to do this instead.

Just like ”All Movies”, this screen lazy loads more content as the user scrolls. Performing a new search resets the previous search result.


### Settings

The Profile screen is limited in design and functionality. It lets the user login, logout, switch library and get more information about the service. It also has links to support and account pages.

![A screenshot of the settings screen]({{page.assets}}10-profile.jpg)

This screen will get more information and functionality later on. For instance, multi-user support would be nice, as well as more user preferences.


## Movie Screen

In all screens that we have looked at until now, users can tap any movie cover to navigate to that movie. 

Due to the screen design, with a prominent header, I chose to present the screen in a sheet instead of pushing it on the navigation stack.

![A screenshot of the Movie screen]({{page.assets}}11-movie.jpg)

Presenting it in a sheet works great on iPad as well, where the movie is presented in a center window: 

![A screenshot of the Movie screen on iPad]({{page.assets}}11-movie-ipad.jpg)

Looking at the design, I have adapted the tvOS screen to work as a modal sheet for smaller screens. Instead of a backdrop, the image is presented as a header, with the most important info and some action buttons added as overlays. More info is displayed below the header, together with primary actions.

The contributor list was a breeze to build in SwiftUI, using a scrolling `HStack` and a clip shape. I can’t even begin to imagine building it with a `UICollectionView`.

The video player was easy to build as well, by just reusing the player from the tvOS app and presenting it as a `fullScreenCover`. The player stores the position of each unique movie and restores it the next time that movie is played. Reaching the end resets position and closes the player.

Trailers are currently YouTube links, so they open either the YouTube app (if installed) or Safari. I will probably change this to open a Safari sheet instead, so that users don't have to leave the app.


## Chromecast

A fun addition to this app was to build Chromecast support with the `GoogleCast` library. If a user gives the app permission to detect Chromecast devices, a cast button is presented when a Chromecast device is available on the same network.

![A screenshot of the Chromecast button]({{page.assets}}12-chromecast.jpg)

According to the documentation, the Chromecast button should be added to *all* screens, so I added it to the movie screen as well.

![A screenshot of the Chromecast button on the movie screen]({{page.assets}}12-chromecast-movie.jpg)

I really like how the button only appears when there is a Chromecast device on the same network, which means that it only appears when it makes sense.

This was the first time I worked with Chromecast development. I found the docs to be great, the Swift sample code pretty nasty, the sample app badly focused at the core aspects of using Chromecast and the overall developer experience not that great.


## Technology

Finally, let's go through some technological aspects of the app.


### Performance

As I wrote in the [tvOS-specific blog post]({{page.post-tvos}}), SwiftUI stacks and grids have horrible performance on tvOS, which forced me to use a wrapped `UICollectionView`. This took a lot of time to get right.

Luckily, the same is not true for iOS, where native stacks and grids are very performant. Performance has never been an issue when porting the tvOS app to iOS.


### Async Images

Just like in the tvOS app, I use Kingfisher to handle async images. I use a pre-processor that scales images down to exact points and use disk cache.

![A screenshot of movie covers]({{page.assets}}13-covers.jpg)

I currently have problems with the disk cache, which I have configured to be valid for 1 day. However, when Cineasterna changes covers, the app still gets old ones, since the cache isn't invalidated.


### Lazy Loading

Both shelves and grids can lazy load more content as the user scroll down and reaches the end of the already fetched content.  

![A screenshot of movie covers]({{page.assets}}14-lazy-loading.jpg)

This was easy to do, by looking at the movie when rendering a list item. For shelves, a movie should trigger a lazy load if it's first in the last available list. For grids, it must be the last available movie.

When a lazy load is triggered, the stacks and grids triggers an injected action that performs an async fetch that appends more content to the movie collection. The collection is observed and automatically updates the view.


### Video Player

The video player was easy to build, by just wrapping an `MPPlayerViewController` and giving it a url and start position. It remembers the position of each movie and restores it the next time it is played. Reaching the end resets this position and closes the player.


## Conclusion

To wrap up, building this app in SwiftUI was a lot easier than to build it for tvOS, much since I know the HIG better and that grids and stacks work better. Some views and api:s are still missing, so you still have to wrap native UIKit components, but not as much as in tvOS.

All in all, this was another fun project that I'm proud to release. I’m super happy to help services like [Cineasterna]({{page.cineasterna}}) and the public libraries help people to discover culture from all over the world.