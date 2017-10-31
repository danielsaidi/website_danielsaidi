---
title:	"Spotify background playback problem on iOS"
date:	2011-01-21 12:00:00 +0100
tags: 	spotify ios
---


This is not a development-related blog post, but well worth mentioning to all of
you who have been experiencing playback problems when the Spotify app for iOS is
sent to the background.

For me, the problems turned out to occur when there are too many active programs
in the background (I guess the device has little available memory or something).
Whenever this happens, Spotify does not seem to be able to add playback features
to the background process, an can thus not continue to play when the app is sent
to the background.

To solve this, I just kill off a few apps, then restart Spotify. This solves the
problem and makes background playback work again. At least for me. Hopefully for
you as well.

