---
title: Mojave backslash keybinding
date:  2018-08-13 21:00:00 +0100
tags:  macos
---

This year, I decided not to wait a while year before installing the latest macOS,
so I grabbed it as a beta, installed it and didn't look back. Before doing so, I
also created a setup script that quickly can setup a clean computer from scratch.
I am not that brave.

Everything have worked out really well, but I have faced some strange keybinding
issues with my Swedish keyboard. One example is `backslash`, which now opens the
help menu instead of entering a backslash. Perhaps this is a beta issue? 

To restore the old pre-Mojave behavior, just open `Keyboard Settings` in `System
Preferences` and select `Shortcuts`. Select `Application shortcuts` then uncheck
`Show help menu`, like this:

[![Keyboard Settings]({{ "assets/blog/2018/2018-08-13.png" | absolute_url }})]()

After this, the standard backslash keybinding should work once again.