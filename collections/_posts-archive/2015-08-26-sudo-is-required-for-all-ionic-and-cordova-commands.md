---
title: sudo is required for all Ionic and Cordova commands
date:  2015-08-26 08:00:00 +0100
tags:  ios android hybrid-apps
---

I'm currently building my first Ionic Framework app. It's fast to setup and performs well, but I ran into a problem that forces me to use `sudo` for all Ionic & Cordova commands.

This problem only occurs at work, not at home. I understand that some global commands require `sudo`, but at work I also have to use sudo for commands like:

* `ionic platform add android`
* `ionic build ios`
* `ionic run android`

If I don't use sudo, Ionic isn't able to remove assets in the platform folders. I can also not open the project in Xcode, due to missing permissions.

I finally found a terminal command that fixes these problems whenever they happen:

```sh
sudo chown -R $(whoami) ~/<path to your project folder>
```

Run it, and you will no longer have to use sudo when running Ionic & Cordova commands. You will also be able to open the Xcode project without warnings.