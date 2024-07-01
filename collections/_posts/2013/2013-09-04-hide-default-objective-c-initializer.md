---
title: Hide the default Objective-C initializer
date:  2013-09-04 04:11:00 +0100
tags:  ios
icon:  swift

assets:  /assets/blog/13/0904/
image:   /assets/blog/13/0904.png
---

Languages like Java and C# let you override and hide the default constructors of a class. Let's take a look at how to do the same in Objective-C.

![Init where are you]({{page.image}})

Hiding the default constructors of a class is particularly useful for constructor injection, to ensure that a class can not be created without providing it with required values. 

Objective C, however, uses the init pattern when initializing class instances. It's all good, but have some implications. 

Consider a class that can be initialized with a required component, as such:

```objc
- (id)initWithMoreStuff:(id)moreStuff {
   self = [self init];
   if (self) {
      self.moreStuff = moreStuff;
   }
   return self;
}
```

The big problem with this, is that if the class requires `moreStuff`, a developer can still call the default initializer to create an instance, where `moreStuff` is nil.

One way to enforce `initWithMoreStuff` is to throw exceptions if another initializer is used. However, this is not good, since incorrect usages are not detected until the app crashes at runtime. We instead want make `initWithMoreStuff` the only available initializer.

This is easy. In the .h file, just annotate the default initializer with `unavailable`, like this:

```objc
- (id)init __attribute__((unavailable("A descriptive reason")));
```

Now, you will get a compilation error if you try to use the default initializer.