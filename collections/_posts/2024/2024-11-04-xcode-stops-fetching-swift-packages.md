---
title:  Xcode stops fetching Swift packages
date:   2024-11-04 06:00:00 +0000
tags:   xcode git
---

Today, Xcode 16.1 suddenly stopped fetching Swift package dependencies, with a `Fatal: cannot use bare repository` warning. Let's see how to fix it, in case it starts happening to you.

<!--![Header image]({{page.image}})-->

I've used Xcode 16.1 for a while, without any Swift package problems. This problem appeared out of the blue, and stopped any external packages from being loaded. I tried resetting the package cache, cleaning the build folder, deleting derived data, rebooting, etc. but nothing helped. 

I eventually found [this thread](https://forums.swift.org/t/fatal-cannot-use-bare-repository/75588/1), in which people notice that SourceTree (my visual git client of choice) seems to have added a new configuration to the global `~/.gitconfig` file:

```
[safe]
    bareRepository = explicit
```

Removing or commenting out this from the file, then restarting Xcode seems to fix the problem for several persons in the thread. I tried it, and it sure enough worked for me as well.

I however don't know what this configuration does, so please share any unwanted side-effects that disabling this configuration may have.