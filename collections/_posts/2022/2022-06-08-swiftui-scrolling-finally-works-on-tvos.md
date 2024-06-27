---
title:  SwiftUI scrolling finally works in tvOS 16
date:   2022-06-08 01:00:00 +0000
tags:   swiftui tvos

assets: /assets/blog/22/0608/
image:  /assets/blog/22/0608/apple-tv.jpg
tweet:  https://twitter.com/danielsaidi/status/1534436700344164352?s=20&t=FH9px9RehnPiSAQmZMizQg

post:   https://danielsaidi.com/blog/2020/12/09/building-a-tvos-app-in-swiftui
demo:   https://twitter.com/danielsaidi/status/1534413867648000000?s=20&t=0v0mkS3sNxXNptr_rBafmA

bookbeat: https://www.bookbeat.com
cineasterna: https://www.cineasterna.com
---

To all of you who have struggled with SwiftUI and slow scrolling on tvOS - happy news! With Xcode 14 and SwiftUI 4, scrolling finally seems to work in tvOS 16.


## Background

When SwiftUI launched in 2019, it was an enabler for many who like me mostly work on a single Apple platform. Even though UIKit worked great with tvOS & watchOS (with some quirks), being able to use one UI framework on all platforms was a dream come true...

...just not yet. SwiftUI had a rough start, with many things shown on WWDC deprecated or drastically changed during summer, which rendered much of what we've learned unusable. There were also serious bugs on all platforms, which undermined developer trust.

I think these first months are a big reason to why people are still so hesitant to use SwiftUI, and why we have all these "Are SwiftUI ready for production yet?".

However, I decided to go all in and work around the bugs and limitations, to be ready when SwiftUI started to live up to its promise, and have done almost no UIKit since its release. I have introduced SwiftUI in various projects, starting with a watchOS app for [BookBeat]({{page.bookbeat}}).

The early adoption has been very rewarding, since SwiftUI fits my mental model perfectly, unlike UIKit, which I've never enjoyed working with. I've had to dig deep to get some things to work, through the deep Mines of Moria, wrestling the occasional Balrog. However, while frustrating, I've always (so far) resurfaced with my love for SwiftUI intact.


## The silent tvOS scrolling debacle

One time, SwiftUI ended up costing me both time and money, was the first tvOS project I did on my newly started company.

I reached out to a company called [Cineasterna]({{page.cineasterna}}), that lets you watch public library movies for free. They only had a web app, which was the perfect service to turn into a tvOS app. After showing a quick tvOS demo for them in SwiftUI, they were eager to get started.

I started with the domain logic, API integrations etc. and then started with the UI, just to notice that the scrolling was off. Scrolling in `HStack` & `VStack`, and later `LazyHStack`, `LazyVStack`, `LazyHGrid` & `LazyVGrid` just didn't work. The scrolling was lagging so badly.

I finally solved it by wrapping a UIKit `UIScrollView`, which I describe in [this article]({{page.post}}). It was not ideal, but did let me ship the app with minimal changes to the overall architecture. The app was well received by users and was later ported to iOS & iPadOS, where SwiftUI did finally deliver on its promise.

Apple have never mentioned these problems or recognized my bug reports, but I finally got confirmed during last year's WWDC labs, that the problem originates from the framework, and not from my code. I'd like for Apple to communicate these limitations, instead of letting each developer finding it out the hard way and going through many hours of trial and error.

Since this project, I've kept an eye on the tvOS updates, which have improved things step by step. tvOS have gotten closer and closer to delivering on the initial promise of SwiftUI, with strange focus problems being fixed, albeit without ever being mentioned. 


## How tvOS 16 finally solves the scroll problems

Today, I downloaded the new Xcode 14 beta, and upgraded my Apple TV to the tvOS 16 beta, to be able to check if the scrolling has finally been fixed.

I created a super simple test app with the following code:

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

In tvOS 15 and earlier, these stacks would not scroll smoothly, but freeze and jerk. The scrolling would technically "work", but not be remotely near as smooth as in e.g. Netflix.

I wonder if this is why some apps, like HBO Max and Amazon Prime have such strange scrolling, where you go through the list item by item, which is slow and not enjoyable.

In tvOS 16, these stacks were now actually scrolling smoothly. As I deployed the app to my upgraded Apple TV, running the tvOS 16 beta, I could verify that they scroll smoothly there as well. You can see this in action in [this tweet]({{page.demo}}).

Apple are finally delivering a knock-out SwiftUI experience for tvOS. To all of you who have also struggled with this, I hope that this will bring you a lot of joy and that we have a bright SwiftUI future ahead of us, on all Apple platforms.