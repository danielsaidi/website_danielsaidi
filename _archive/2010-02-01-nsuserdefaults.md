---
title: NSUserDefaults
date:  2010-02-01 12:00:00 +0100
tags:  archive
icon:  swift
---

As I've started to look into iOS development, I have created a test app with a tab
view and four views (the $$$s are not far away). I now want to store data without a
database. Can this be done?

In the app, I want to persist small amounts of data, so the app can remember things
like the selected tab, list selections etc. the next time it's started. For the small
amount of data this involves, I don't want to go through the hassle of having to use
a database.

I found a great tutorial at [icodeblog.com](icodeblog.com) (now removed) that shows
how this can be achieved, using a class called `NSUserDefaults`. It has a singleton
instance that can be used right away, is thread-safe and seems ideal for saving small
amounts of data.

`NSUserDefaults` can even be subclassed and mocked, which looks great for more
complex apps and unit tests, where the peristency should be abstract and injectable.
I will try it out and hope it works well.