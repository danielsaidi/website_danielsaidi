---
name: BookBeat

bonnier: https://bonnierbooks.com
---

BookBeat is an audiobook and e-book subscription service from Swedish [Bonnier]({{page.bonnier}}). The service is live on 28 European markets, where some are focus markets while others feature general content.

![BookBeat title image](/assets/work/bookbeat-title.jpg)

BookBeat lets users listen to audiobooks and read e-books on a monthly subscription plan. The app has support for multiple profiles (like Netflix) and has a Kids mode that protects children from adult content.


## Work

During my time as iOS and Mobile Architecture Lead, we evolved the iOS app to a distributed mobile system and also developed apps for Car Play and watchOS.


## Technologies

The BookBeat apps are written in Swift. The main app uses SwiftUI where applicable (it supports iOS 11, so it must support UIKit as well) while the watchOS app is entirely written in SwiftUI.

The apps share business logic through a set of shared, well-tested libraries, that provides shared business models, network logic, offline support etc. This makes the apps very stable and reliable.