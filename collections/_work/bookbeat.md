---
name: BookBeat

bonnier: https://bonnierbooks.com
---

BookBeat is a subscription service that lets users listen to audiobooks and read e-books. The app has support for multiple profiles and a Kids mode that protects children from adult content.

![BookBeat title image](/assets/work/bookbeat-title.jpg)

## Work

I joined BookBeat as iOS Lead, shortly after the first public iOS release. During my time there, BookBeat went from 4 to 28 markets and reached almost 500.000 monthly users.

As iOS and Mobile Architecture Lead, I evolved the iOS app from a single app monolith to a distributed mobile system that consisted of several feature-specific libraries . This architecture made it possible to develop apps for iOS (with Car Play support), watchOS and tvOS, with no business logic in the apps.

BookBeat was the first Swedish autobook service to get support for CarPlay and watchOS, and the first streaming audio service to get support for downloading files directly on the watch.

During my time at BookBeat, I had the opportunity to focus heavily on accessibility, taking help from Swedish accessibility agencies and work with EAA. THe app has support for dark mode, high contrast colors, dynamic type and more.

## Technologies

The BookBeat apps are written in Swift. The main app uses SwiftUI where applicable (it supports iOS 11, so it must support UIKit as well) while the watchOS app is entirely written in SwiftUI.

The apps share business logic through a set of shared, well-tested libraries, that provides shared business models, network logic, offline support etc.