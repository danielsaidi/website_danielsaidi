---
title:  "Reset Xcode \"load plugin bundles\" warning"
date:   2016-09-05 17:42:00 +0100
categories: mobile
tags: 	ios xcode xcode-plugins
---


Today, I managed to click the "Skip Bundles" button instead of the "Load Bundles"
button, when I started up Xcode after adding two new plugins.

With that highlighted, blue button, can you blame me?

![Xcode Load Plugin Bundles Warning Dialog](/assets/img/blog/2016-09-05_bundles.png)

If you too have managed to make this mistake, you may have noticed that killing
and restarting Xcode will not solve the problem - the plugins won't load and you
are never again prompted about these plugins.

So, if you really want to load these bundles, you have to resort to the Terminal.
Open up the terminal and enter the following command:

{% highlight shell %}
defaults delete com.apple.dt.Xcode DVTPlugInManagerNonApplePlugIns-Xcode-7.3.1
{% endhighlight %}

Then restart Xcode and you will once more be prompted about loading these bundles.
This time, press "Load Bundles"! Unless you really enjoyed all this nice work.