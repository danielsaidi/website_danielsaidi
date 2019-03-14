---
title:  SwiftyDropbox crashes in Swift 4.2
date:   2018-09-19 08:00:01 +0200
tags:	swift xcode
---


After installing Xcode 10 yesterday, I started migrating some libraries to Swift
4.2. While most migrations were painless, one caused me some headache, since the
library depends on `SwiftyDropbox` which does not yet support Swift 4.2.

Adding `SwiftyDropbox` to the library makes it compile, but the demo app crashes
at runtime, with the following error:

```swift
dyld: Symbol not found: _$S8Dispatch0A3QoSV0B6SClassO7utilityyA2EmFWC
  Referenced from: /Users/admin/Library/Developer/CoreSimulator/Devices/69F0BD96-7BB8-4B29-BE96-A423BA2FBD3C/data/Containers/Bundle/Application/CAAB9A58-4F89-4C85-BCDA-8ECF22D11731/VandelayExample.app/Frameworks/Alamofire.framework/Alamofire
  Expected in: /Users/admin/Library/Developer/CoreSimulator/Devices/69F0BD96-7BB8-4B29-BE96-A423BA2FBD3C/data/Containers/Bundle/Application/CAAB9A58-4F89-4C85-BCDA-8ECF22D11731/VandelayExample.app/Frameworks/libswiftDispatch.dylib
 in /Users/admin/Library/Developer/CoreSimulator/Devices/69F0BD96-7BB8-4B29-BE96-A423BA2FBD3C/data/Containers/Bundle/Application/CAAB9A58-4F89-4C85-BCDA-8ECF22D11731/VandelayExample.app/Frameworks/Alamofire.framework/Alamofire
(lldb) 
```

So, the problem is not caused by `SwiftyDropbox` itself, but rather that is uses
an old version of Alamofire. `4.5.0` to be exact. You can solve this by adding a
later version of Alamofire to your `Cartfile` or `Podfile`.

So, instead of having this in my `Cartfile`:

```swift
github "dropbox/SwiftyDropbox" ~> 4.6.0
```

I have this:

```swift
github "dropbox/SwiftyDropbox" ~> 4.6.0
github "Alamofire/Alamofire" ~> 4.7.0
```

This is just a temporary fix until `SwiftyDropbox` updates the Alamofire version,
which hopefully will not take too long. I have sent a PR to the team and hope it
will be approved and merged shortly.