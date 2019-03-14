---
title:  "WampServer URL rewriting problem"
date:   2011-03-09 12:00:00 +0100
tags: 	php web
---


I have a hobby project that works great on MAMP, but that doesn't run so good on
WampServer. It's quite frustrating to have to test all new components in two PHP
environments that should be more or less similar, so I decided to look into this.

Looking into this problem, I found that the problem is that an Apache module for
URL rewriting that is enabled by default in MAMP, is disabled in WampServer. The
module can be enabled by doing this:

- Click on the WampServer icon in the system tray
- Navigate to Apache/Apache Modules
- Enable rewrite_module

After this, URL rewriting should work.