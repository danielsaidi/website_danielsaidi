---
title: Using Crittercism for iOS crash reporting
date:  2014-07-02 09:26:00 +0100
tags:  swift
icon:  swift

firebase: https://firebase.google.com
---

In this post, let's look at how to use Crittercism to let your iOS or Android users report bugs and provide you with automated crash reports.


## Update 2024

Since writing this post, Crittercism has shut down. It was replaced with Fabric,  which was later integrated into [Firebase]({{page.firebase}}), which is still the current tool in 2024.


## What is Crittercism?

Crittercism is a service that lets your app send automated crash reports if users experience errors that cause your app to crash. Instead of vague user reports, it provides stack traces, a list of the most frequent bugs, information to you understand what caused a crash, etc.

Setting up Crittercism is easy, with easy steps that are shown after you create an account and register your app. I'm not going to repeat all that information here, but want to touch on a term that may confuse or scare you - dSYM files.


## What is a dSYM file?

A dSYM file is a file that contains debug symbols for an app. Services like Crittercism and Crashlytics use this file to analyze your code and send you more meaningful information about your app crashes, instead of just sending a raw memory dump.

To benefit from Crittercism, you have to setup your app project to upload a new dSYM file to Crittercism whenever your app is built for distribution to beta testers or to the App Store.


## Uploading a dSYM file

The Crittercism dashboard lets you upload a zipped dSYM file manually or find information about how to setup a project for automatic file upload. 

It's really straightforward, but I just want to underline two things:

* When I write this (July 2, 2014), the instructions include a typo - `$"{SRCROOT}"` should be `"${SRCROOT}"`. It took me time to notice this, so mind the quote!

* When adding the build script, make sure to check "Run script only when installing" for the Run Script Build Phase, to avoid uploading the dSYM file on every build.

* After adding the build script, your builds will fail if the upload fails. This means that it a build works, Crittercism should receive your dSYM file. 

It can take some time for the file to show up, so be patient.


## Dig in!

Once Crittercism has received your dSYM file and your beta tester or real world users start using your app, the app will start sending crashes to Crittercism.

You can now sign in, open your app and dig in on these detailed crash reports and start to work your way towards a more stable app.