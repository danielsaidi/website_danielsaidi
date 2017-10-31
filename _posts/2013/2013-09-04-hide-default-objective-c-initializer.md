---
title:  "Hide the default objective-c initializer"
date: 	2013-09-04 04:11:00 +0100
tags: 	ios objective-c
---


![Init where are you](/assets/img/blog/2013-09-04-init.png)


Languages like Java and C# let you override and hide the default constructors of
a class, to ensure that developers can only create valid instances of the class.
This is particularly useful for constructor injection, to ensure that a class can
not be created without providing it with a set of required components.

Objective C, however, uses the init pattern when initializing class instances. It
is all good, but have some implications. Consider a class that can be initialized
with a required component, as such:


```objc
- (id)initWithMoreStuff:(id)moreStuff {
   self = [self init];
   if (self) {
      self.moreStuff = moreStuff;
   }
   return self;
}
``


The big problem with this, is that if the class requires moreStuff to be set, and
the initializer above should be the *only* initializer available, a developer can
still use the default initializer to create an "invalid" instance, where moreStuff
is nil.

One way to force initWithMoreStuff: to be used, is to throw an exception whenever
an instance is created with the default initializer, but that is not good enough.
The app can still use the default initializer, with the incorrect usage not being
detected until the app crashes during runtime.

What we want is to hide the default init method and make `initWithMoreStuff:` the
only available init method.

Achieving this is easy. In the .h file, kist annotate the default initializer with
`unavailable`, like this:


```objc
- (id)init __attribute__((unavailable("...")));
``


Now, you will get a compilation error if you try to use the default initializer.

