---
title:  SwiftUI scrolling finally works in tvOS 16
date:   2022-06-08 01:00:00 +0000
tags:   swiftui tvos

assets: /assets/blog/2022/220608/
image:  /assets/blog/2022/220608/apple-tv.jpg
tweet:  https://twitter.com/danielsaidi/status/1534436700344164352?s=20&t=FH9px9RehnPiSAQmZMizQg

post:   https://danielsaidi.com/blog/2020/12/09/building-a-tvos-app-in-swiftui
demo:   https://twitter.com/danielsaidi/status/1534413867648000000?s=20&t=0v0mkS3sNxXNptr_rBafmA

bookbeat: https://www.bookbeat.com
cineasterna: https://www.cineasterna.com
---

To all of you who have struggled with SwiftUI and slow scrolling on tvOS - happy news! With Xcode 14, SwiftUI 4 and tvOS 16, scrolling finally seems to become super smooth.


## Background

When SwiftUI launched in 2019, it was an enabler for many developers who like me previously had only worked on a single Apple platform. Even though UIKit worked great with tvOS and watchOS (with some platform-specific quirks), being able to use one UI framework on all platforms was a dream come true...

...although the dream wasn't coming true, not just yet. SwiftUI had a rough start, with things shown on WWDC 2019 being deprecated and drastically changed during summer, rendering much of what we've learned unusable. There were also serious bugs on all platforms, which undermined the trust in SwiftUI for many developers, me included. I think these first months are a big reason to why people are still so hesitant to use SwiftUI, and why we have all these "Are SwiftUI ready for production yet?" discussions.

However, I decided to roll with the punches and work around the bugs and limitations, even during these troubled times. I wanted to be ready when SwiftUI started to live up to its promise, and have thus done almost no UIKit work since SwiftUI was released. I have done my best to introduce SwiftUI in the various projects I work with, starting with a watchOS app for [BookBeat]({{page.bookbeat}}), which turned out great.

Sometimes, this early adoption has been very rewarding, since SwiftUI fits my mental model perfectly, unlike UIKit, which I've never enjoyed working with. Other times, I've had to dig deep to get things to work, through the deep Mines of Moria, wrestling the occasional SwiftUI Balrog. However, as frustrating as some problems have been, I've always (so far) resurfaced with my love for SwiftUI intact.


## The silent tvOS scrolling debacle

One example where SwiftUI ended up costing me both time and money, was the first tvOS project that I did on my newly started company. I reached out to an amazing service called [Cineasterna]({{page.cineasterna}}), that lets you watch public library movies for free, who at the time only had a web app. To me, this was the perfect service to turn into a tvOS app. After putting together a quick demo for them, they agreed.

So, there I was - building a tvOS app for SwiftUI! I started building the domain logic, api integration etc. and then started working on the UI...just to notice that the scrolling was off. Scrolling with `HStack`, `VStack` (and in SwiftUI 2), `LazyHStack`, `LazyVStack`, `LazyHGrid`, `LazyVGrid` just didn't work. The scrolling was lagging so badly that the whole project was in jeopardy.

I finally solved this by wrapping a UIKit `UIScrollView` in SwiftUI, which I describe in [this article]({{page.post}}). It was not ideal, but did let me ship the app with minimal changes to the overall architecture. The app was well received by the users and later ported to iOS and iPadOS, where SwiftUI did deliver on its promise and let me create these new apps quickly, building on the same foundation as the tvOS app.

Apple have never mentioned these problems or recognized them in my bug reports, but I eventually got confirmation during last year's WWDC labs, that the problem does originate from the framework, and not from my code. I'd like for Apple to communicate these limitations, instead of letting each developer find it out the hard way and go through so many hours of trial and error, debugging and workarounds.

Since this project, I've kept an eye on the tvOS updates, which have improved things step by step. tvOS have gotten closer and closer to delivering on the initial promise of SwiftUI, with strange focus problems being fixed without being mentioned. 

With tvOS 16, it finally seems like the scroll errors are getting fixed as well. Let's  check it out.


## Scroll error solved?

Today, I downloaded the new Xcode 14 beta, which you can get from the Apple Developer Portal. I also upgraded my Apple TV to the tvOS 16 beta, to be able to check if the scrolling is finally fixed.

I setup a super simple test app, using the following code:

```swift
struct ContentView: View {
    var body: some View {
        VStack(spacing: 40) {
            ScrollView(.horizontal) {
                HStack {
                    items
                }.padding()
            }
            ScrollView(.vertical) {
                VStack {
                    items
                }.padding()
            }
        }
    }
}

private extension ContentView {

    var items: some View {
        ForEach(Array((1...100).enumerated()), id: \.offset) { _ in
            Button(action: {}) {
                Color.blue
                    .frame(width: 450, height: 200)
            }.buttonStyle(.card)
        }
    }
}
```

Earlier, these stacks would not scroll smoothly, but freeze and jerk. As SwiftUI 2 was released, it was with sadness that I noted that the problem was still around, and that using lazy stacks and grids didn't help - the scrolling was still broken. Slow item-by-item scrolling would work, but wouldn't be near the experience you get in apps like Netflix.

I wonder if this is why some apps, like HBO Max and Amazon Prime have such strange scrolling, where you go through the list item by item, which is slow and not enjoyable. However, I really doubt that these apps are built with SwiftUI. If anyone could confirm, that'd be interesting to hear.

As I now ran the code above on the tvOS simulator, the stacks were actually scrolling smoothly. As I then deployed the app to my upgraded Apple TV, running the tvOS 16 beta, I could verify that the stacks scroll smoothly there as well. You can see this in action in [this tweet]({{page.demo}}), where I've posted a few videos.

So, it seems that Apple are finally delivering a knock-out SwiftUI experience for tvOS. To all of you who have struggled as I have, I hope that these improvements will remain in future betas, and that we have a bright SwiftUI future ahead of us, on all Apple platforms.