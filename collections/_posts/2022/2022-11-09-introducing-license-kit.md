---
title:  Introducing LicenseKit
date:   2022-11-09 08:00:00 +0000
tags:   swift closed-source licenses

image:  /assets/headers/licensekit.png
---

When building closed-source software, you must not only protect your source code, but must protect the binaries themselves as well, so that you can distribute them without having to worry that they are used by people who shouldn't access them. One way to do this is with software licenses.

{% include kankoda/data/open-source.html name="KeyboardKit" %}{% assign keyboardkit = project %}
{% include kankoda/data/open-source.html name="LicenseKit" %}

![LicenseKit logo]({{page.image}})

When I created my commercial [KeyboardKit Pro]({{keyboardkit.url}}/pro) product, I had to be able define licenses for my various customers. I therefore created an on-device license engine, and have been using it for over a year now.

I have now extracted the engine from KeyboardKit Pro and made it its own thing. The result is [LicenseKit]({{project.url}}) - an on-device license engine that lets you protect your apps and software with commercial licenses. 

LicenseKit licenses can define expiration date, tier (basic, silver, gold, etc.), supported platforms (iOS, macOS, etc.), supported bundle IDs, custom features, etc. Licenses can be defined in code, read from file or fetched from a remote api. You can also combine multiple data sources for maximum flexibility.


## Conclusion

LicenseKit gives you a lot of flexibility to craft your own license model. It’s currently in early beta, and has a free tier that you can use with a capped number of licenses. More integrations are also being built. 

If you or anyone you know are looking for ways to handle licenses in your apps or libraries, just reach ut or have a look at the [LicenseKit website]({{project.url}}) for more information.

Please share if you find it interesting. I appreciate all help I can possibly get to get this into the hand of users. Also please do let me know if you have any comments or feedback. I’d love to hear it.