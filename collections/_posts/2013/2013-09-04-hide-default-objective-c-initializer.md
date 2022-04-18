---
title: Hide the default Objective-C initializer
date:  2013-09-04 04:11:00 +0100
tags:  ios objc
---

Languages like Java and C# let you override and hide the default constructors of
a class, to ensure that developers can only create valid instances of it. Let's
take a look at how to do the same in Objective-C.

![Init where are you](/assets/blog/2013/2013-09-04-init.png)

Hiding the default constructors of a class is particularly useful for constructor 
injection, to ensure that a class can not be created without providing it with a 
set of required components. Objective C, however, uses the init pattern when initializing class instances. It's all good, but have some implications. 

Consider a class that can be initialized
with a required component, as such:

```objc
- (id)initWithMoreStuff:(id)moreStuff {
   self = [self init];
   if (self) {
      self.moreStuff = moreStuff;
   }
   return self;
}
```

The big problem with this, is that if the class requires `moreStuff`, a developer 
can still call the default initializer to create an instance, where `moreStuff` is nil.

One way to force `initWithMoreStuff` to be used, is to throw an exception whenever
an instance is created with the default initializer. However, this is not good, since
developers can still use the default initializer, with the incorrect usage not being
detected until the app crashes at runtime.

What we want is to hide the default initializer and make `initWithMoreStuff` the
only available one.

Achieving this is easy. In the .h file, just annotate the default initializer with
`unavailable`, like this:

```objc
- (id)init __attribute__((unavailable("A descriptive reason")));
```

Now, you will get a compilation error if you try to use the default initializer.