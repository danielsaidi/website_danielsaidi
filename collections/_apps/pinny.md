---
name: Pinny

assets: /assets/apps/pinny/
image:  /assets/apps/pinny/header.jpg

tags: realm
---

Pinny was the second native iOS app that I created, as I set out to learn Swift as it was released in 2014. The idea was to let users create personal maps with features that Google Maps didn't provide.

![Pinny logo]({{page.image}})

Pinny lets you store pins on multiple maps and annotate them with custom icons and colors. Users can group pins in maps and categories and toggle what content to display.

![Pinny app]({{page.assets}}app-1.jpg){:style="width:350px"}

I have started many rewrites of the original app, and even if the code has been pretty easy to port from the earlier versions of Swift, the data persistency layer has been painful to maintain. 

After migrating from Core Data and a cloud-based database to Realm, I have waited for a swiftier way to handle and sync user data with the cloud. If such a solution arrives, I may rewrite Pinny in SwiftUI.

I just got a notice that Pinny will be taken down from the App Store due to inactivity. I am keen to rewrite it in SwiftUI, but at the same time I'm not sure that it is as relevant in 2022 as I found it to be in 2014.