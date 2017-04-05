---
title:  "UIViewController Custom Back Button Problem"
date: 	2013-04-23 20:14:00 +0100
categories: mobile
tags: 	ios objective-c
---


If you want your apps to have an identity of their own, you should put some time
into adjusting the default appearance, such as fonts and standard iconography.

One thing that may be a bit tricky, is to replace the default back button with a
custom one. It is a very simple thing to achieve, but in one app of mine, I just
could not get it to work.

The process is very simple. In `viewDidLoad`, I just called some code that should
replace the standard back button with a nice, custom arrow. It took me like five
minutes to get everything up and running, including importing the image files to
the project, do 1000 push-ups and watch some epic rap battles on YouTube.

However, when I decided to revisit an old app of mine and add this feature to it
as well, I could not get it to work. The following kept me busy a whole evening:

* The app navigates to a table view controller from another table view controller
* If I call the back button replacement code in `viewDidLoad`, the original back
button doesn't disappear. Instead, the new back button is added on top of it.
* If I call the code in `viewWillAppear` or `viewDidAppear`, nothing happens...
since these methods are never called (???)

I tried replacing the clean code I had with timers and other horrible hacks, but
nothing worked (luckily, I might add, since that is NOT the way you should solve
this problem). I then found [this post](http://smartercoder.com/2010/10/18/what-to-do-it-self-navigationitem-hidesbackbutton-true-doesnt-work/),
which suggests adding a fake back button, then remove it when replacing the real
back button with a custom one, like this:

{% highlight objc %}
self.navigationItem.leftBarButtonItem = [[UIBarButtonItem alloc] initWithCustomView:[[UIView alloc] init]];
self.navigationItem.leftBarButtonItem = nil;
self.navigationItem.hidesBackButton = YES;
self.navigationItem.leftBarButtonItem = /* Set your bar button here */;
{% endhighlight %}

This solution is just strange, and should not work either...which it didn't. The
original back button shows briefly, before it disappears. Nothing is solved.

I am still stuck with this problem, with no idea what is causing it. I don't see
this strange behavior in my other apps, even when adding the same original code.
I even tried recreating the view controllers in a new project, with the same set
of base classes and view lifecycle events, and it works just fine.

Any ideas, world?