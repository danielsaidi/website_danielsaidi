---
title: Using Crittercism for iOS crash reporting
date:  2014-07-02 09:26:00 +0100
tags:  ios swift objc
icon:  swift

firebase: https://firebase.google.com
---

In this post, let's look at how to use Crittercism to let your iOS or Android
users report bugs and provide you with automated crash reports.

Note that since writing this post, Crittercism has shut down. It was replaced 
with Fabric,  which was later integrated into [Firebase]({{page.firebase}}), which 
was the way to do these things as this post was updated in 2022.


## What is Crittercism?

Crittercism is a service that lets you send automated crash reports if a user
experience errors that cause your app to crash. Instead of relying on vague
user reports, Crittercism provides you with a list of the most frequent bugs,
together with detailed information that helps you understand what led to the crash.

Setting up Crittercism is really easy, and is explained in detail after you have
created your account and registered your app. I'm not going to repeat all this
information here, but just want to touch on a term that may confuse or scare
you - dSYM files.


## What is a dSYM file?

A dSYM file is a file that contains debug symbols for an app. Services like
Crittercism and Crashlytics use this file to analyze your code and send you more
meaningful information about your app crashes, instead of just sending a memory
dump that is really hard to understand.

To really benefit from Crittercism, you have to setup your app project to upload
a new dSYM file to Crittercism whenever your app is built for distribution to beta
testers or to the App Store.


## Uploading a dSYM file

In the Crittercism portal, you can upload a zipped dSYM file manually or find
information about how to setup a project for automatic file upload. It's really
straightforward, but I just want to mention two things:

* When I write this (July 2, 2014), the instructions include a typo - `$"{SRCROOT}"`
should be `"${SRCROOT}"`. It took me some time to notice this, so mind the quote!

* When adding the build script to your project, make sure to check "Run script
only when installing" for the Run Script Build Phase, to avoid uploading the dSYM
file every time you build.

* After adding the build script, your builds will fail if the upload script fails.
This means that as soon as your build works, Crittercism should receive your dSYM
file. 

It make take some time for the file to show up, so be patient.


## Dig in!

Once Crittercism has received your dSYM file and your beta tester or real world
users start using your app, the app will start sending crashes to Crittercism.

You can now sign in, open your app and eat yourself full on these detailed crash
reports and hopefully start to work your way towards a more stable app.