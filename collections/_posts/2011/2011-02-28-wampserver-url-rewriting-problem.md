---
title: WampServer URL rewriting problem
date:  2011-03-09 12:00:00 +0100
tags:  php web
icon:  php
---

I have a hobby project that works great on MAMP, but that doesn't run so good on
WampServer, which seems to handle url rewriting differently. The fix turned out
to be easier than expected.

When investigating this problem, I found that this happens because an Apache URL
rewriting module that's enabled by default in MAMP, is disabled by default in 
WampServer. This causes url rewriting to behave differently in the two servers.

The url rewriting module can be enabled by doing this:

- Click on the WampServer icon in the system tray.
- Navigate to Apache/Apache Modules.
- Enable rewrite_module.

After this, URL rewriting should work the same way in both environments.