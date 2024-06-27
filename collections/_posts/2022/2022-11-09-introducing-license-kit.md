---
title:  Introducing LicenseKit
date:   2022-11-09 08:00:00 +0000
tags:   swift closed-source licenses

image:  /assets/headers/licensekit.png
image-show: 0
---

When building closed-source software, you must protect your source code as well as the binaries themselves. One way to do this is with software licenses. This is why I'm excited to introduce LicenseKit - an SDK that protects your software with commercial licenses.

{% include kankoda/data/open-source.html name="KeyboardKit" %}{% assign keyboardkit = project %}
{% include kankoda/data/open-source.html name="LicenseKit" %}

![LicenseKit logo]({{page.image}})

When I created [KeyboardKit Pro]({{keyboardkit.url}}/pro), I had to be able define licenses for my customers. To do this, I created an on-device license engine, which I have been using for over a year now.

I have now extracted the engine and made it its own thing. The result is [LicenseKit]({{project.url}}) - an on-device license engine that lets you protect your software with commercial licenses. 

LicenseKit licenses can define expiration date, tier, supported platforms, supported bundle IDs, features, etc. Licenses can be defined in code, read from file or fetched from a remote api. You can also combine multiple data sources for maximum flexibility.


## Conclusion

LicenseKit gives you a lot of flexibility to craft your own license model. It’s currently in early beta, and has a free tier that you can use with a capped number of licenses.

If you or anyone you know are looking for ways to handle licenses in your apps or libraries, just reach ut or have a look at the [LicenseKit website]({{project.url}}) for more information.