---
title: Using Crittercism for iOS crash reporting
date:  2014-07-02 09:26:00 +0100
tags:  ios swift objc
---

**Note:** Crittercism has shut down. A great alternative to Crittercism, in 2017,
is [Fabric](https://fabric.io).

I am having a hard time finding time to write on the blog these days, with family,
work and hobby projects rushing on. And when I find the time, the posts I manage
to scribble together become rather short. And the even sadder thing is that this
post is no exception.

In this short post, I just want to strongly advice you to use Crittercism, if you
have an iOS or Android app where users experience bugs that you have a hard time
to reproduce.


## What is Crittercism?

Crittercism is a service that you can use to automatically send crash reports if
a user experience errors that cause the app to crash. Instead of relying on vague
user reports, Crittercism then provides you with a list of the most frequent bugs,
together with detailed information that helps you understand what led to the crash.

Setting up Crittercism is really easy, and is explained in detail after you have
created your account and registered your app. I am not going to repeat all this
information here, but just want to touch on a term that may confuse you or scare
you - dSYM files.


## What is a dSYM file?

A dSYM file is just a file that contains debug symbols for an app. Services like
Crittercism and Crashlytics use this file to analyze your code and send you more
meaningful information about your app crashes, instead of just sending a memory
dump that is really hard to understand.

To really benefit from Crittercism, you have to setup your app project to upload
a new dSYM file to Crittercism whenever your app is built... or rather, whenever
your app is built for distribution to beta testers or to the App Store.


## Uploading a dSYM file

On the Crittercism portal, you can either upload a zipped dSYM file manually (not
recommended) or find information about how to setup your projects for automatic
file upload. It is really straightforward, but I just want to mention two things:

* When I write this (July 2, 2014), the instructions include a typo - $"{SRCROOT}"
should be "${SRCROOT}". It took me some time to notice this the last time I setup
a project to use the latest release of Crittercism. So, mind the quote!

* When adding the build script to your project, I would advice you to check "Run
script only when installing" for the Run Script Build Phase, to avoid uploading
the dSYM file every time you build.

* After adding the build script, your builds will fail if the upload script fails.
This means that as soon as your build works, Crittercism should receive your dSYM
file. It make take some time for the file to show up, so be patient.


## Dig in!

Once Crittercism has received your dSYM file (building your app will fail if the
upload fails, so as soon as your build works) and your beta tester or real world
users start using your app, the app will start sending crashes to Crittercism.

You can now sign in, open your app and eat yourself full on these detailed crash
reports... and hopefully start to work your way towards a more stable app.