---
title:  "sudo is required for all Ionic and Cordova commands"
date:   2015-08-26 08:00:00 +0100
categories: apps
tags: 	ios android ionic cordova
---


I am currently building my very first app with Ionic Framework. So far, Ionic is
super fast to setup and performs really well, so I hope performance doesn't drop
once we begin to add images and content to the app.

However, while the development setup works just fine at home, I have run into an
annoying permission-related problem at work, that forces me to use `sudo` for all
Ionic and Cordova commands.

Sure, installing Ionic and Cordova globally requires you to use sudo, but I also
have to use sudo for these commands:

* ionic platform add android
* ionic build ios
* ionic run android

If I do not use sudo, Ionic is not able to remove assets in the platform folders.
Also, I cannot open the project in Xcode, due to missing permissions.

I finally found a terminal command that fixes these problems whenever they happen:

{% highlight shell %}
sudo chown -R $(whoami) ~/<path to your project folder>
{% endhighlight %}

Run it once, and you will no longer have to use sudo when executing the Ionic and
Cordova commands. You will also be able to open the Xcode project without warnings.