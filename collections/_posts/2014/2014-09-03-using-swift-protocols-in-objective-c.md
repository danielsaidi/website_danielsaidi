---
title: Using Swift protocols in Objective-C
date:  2014-09-03 15:15:00 +0100
tags:  ios objc swift dependency-injection

coremeta: https://github.com/jgretz/CoreMeta
---

I'm currently creating two new games for iOS. One is made in Swift and SpriteKit,
while the other is made in Objective-C and UIKit. To share logic, I need to use
Swift protocols in my Objective-C code.


## The good part

The apps share an Objective-C core library and use the [CoreMeta]({{page.coremeta}}) 
IoC library to bootstrap each app and select which implementation to use for a
certain protocol.

Objective-C protocols in the library work great in Swift. To implement these protocols
in Swift, I just add them to my bridging header.

One protocol that I reuse in every game is `Animal`. For Objective-C and UIKit, it's 
implemented by an `AnimalView` that inherits `UIView`. For Swift and SpriteKit, it's
implemented by an `AnimalNode` that inherits `SKNode`. This works absolutely great.


## The bad part

All in all, Swift is a really nice experience. Less code, easier to read - I love it!
However,  as I created the Swift protocol and tried to register it with CoreMeta, I
ran into problems.

CoreMeta is written in Objective-C, which lets you pass in protocols as parameters,
which you with the `@protocol(ProtocolName)` syntax. In Swift, on the other hand, you
use `ProtocolName.self`.

This works great for Objective-C protocols, but doesn't work at all for Swift
protocols. Since Swift protocols can't be cast to `Protocol *`, they can't be
used as method parameter with CoreMeta.


## The solution

Searching for solutions to more advanced Swift problems often results in nothing,
since Swift is so new. Still, I found how to use Swift protocols in Objective-C:
Add `@objc` before the `protocol` keyword.

After adding `@objc` to my Swift protocols, I could use them with CoreMeta and
bootstrap them as I do with the Objective-C protocols. Hopefully, Swift evolves
in a way that makes this not required.

