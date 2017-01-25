---
title:  "Using Swift protocols in Objective-C"
date: 	2014-09-04 15:15:00 +0100
categories: apps
tags: 	ios objective-c swift core-meta
---


I am currently creating two new games for iOS. One is made in Swift and SpriteKit,
while the other is made in Objective-C and UIKit.


## The good part

The apps include a custom core lib that is written in Objective-C. Each app also
uses [CoreMeta](https://github.com/jgretz/CoreMeta) - an IoC library written in
Objective-C - to bootstrap the app and select which implementation to use for a
certain protocol.

All Objective-C protocols in the library work great in Swift. To implement these
protocols in Swift, I just add them to my bridging header. I'm then good to go.

One protocol that I reuse in every game is an `Animal` protocol. For Objective-C
and UIKit, it is implemented by `AnimalView`, which inherits `UIView`. In Swift
and SpriteKit, however, it is implemented by `AnimalNode`, which inherits `SKNode`.

This works absolutely great.


## The bad part

All in all, Swift is a really nice experience. Less code, easier to read - gotta
love it. However,  as I created the Swift protocol and tried to register it with
CoreMeta, I ran into problems.

Basically, CoreMeta is written in Objective-C, which lets you pass in protocols
as method parameters. You do this by writing @protocol(ProtocolName). In Swift,
on the other hand, you call ProtocolName.self.

This works great for Objective-C protocols, but does not work at all for Swift
protocols. Since Swift protocols cannot be cast to Protocol *, they cannot be
used as method parameter together with CoreMeta.


## The solution

Searching for solutions to more advanced Swift problems often results in nothing,
since Swift is so new. Still, lucky as I am, I finally found the solution to how
to use Swift protocols in Objective-C.

Just add @objc before the `protocol` keyword.

After adding @objc to my Swift protocols, I could use them with CoreMeta and
bootstrap them as I do with the Objective-C protocols. Hopefully, Swift evolves
in a way that makes this not required.

