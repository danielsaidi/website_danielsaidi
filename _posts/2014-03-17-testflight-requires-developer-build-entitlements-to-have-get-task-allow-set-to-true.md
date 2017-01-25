---
title:  "TestFlight requires developer build entitlements to have get-task-allow set to true"
date: 	2014-05-21 10:54:00 +0100
categories: apps
tags: 	ios testflight xcode code-signing
---


UPDATE 2014-05-02: As JN commented (see below), it may be enough to just restart
Xcode after refreshing the provisioning profiles. Try this before going through
the tedious process of generating new certificates. I know I will 🙂

I recently created a new iOS developer account. Things went smooth when creating
this new account. Even transferring an app from my personal account to this new
company account was really simple and worked well.

However, one thing did not work; uploading my Ad Hoc build to TestFlight. When I
did (no matter how I signed and provisioned my app when archiving it), TestFlight
just told me that:

`Invalid Profile: developer build entitlements must have get-task-allow set to true.`

No matter what I did, I could not get it to work, until I found the answer at an
iOS developer forum. A guy who had had the same problem said that he just deleted
the distribution certificate and generated new provisioning profiles. After that,
everything worked fine for him.

I just went through those very steps, and can confirm that this indeed works!