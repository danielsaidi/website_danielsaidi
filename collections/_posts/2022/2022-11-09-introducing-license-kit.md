---
title:  Introducing LicenseKit
date:   2022-11-09 08:00:00 +0000
tags:   closed-source licenses

image:   /assets/blog/headers/licensekit.png

keyboardkit: https://getkeyboardkit.com
licensekit: https://getlicensekit.com
---

When building closed-source software, you don’t only have to protect your source code, but also the binaries themselves, so that you can distribute them freely without having to worry that they can be used by people who haven’t paid to use your software. One way to do this is with software licenses, which is why I have created LicenseKit.

![StoreKitPlus logo]({{page.image}})

When I created KeyboardKit Pro as a commercial add-on to the open-source library [KeyboardKit]({{page.keyboardkit}}), I realized that I needed to be able define licenses for my various customers. I therefore created a license engine as part of KeyboardKit Pro, and have been using that for over a year now.

With this engine proving successful, I decided to extract it from KeyboardKit Pro and turn it into its own thing. The result is [LicenseKit]({{page.licensekit}}) - a license engine that lets you protect your Swift-based software with commercial licenses. LicenseKit licenses can define a bunch of things, like expiration date, tier (basic, silver, gold etc.), supported platforms (iOS, macOS etc.), supported bundles etc. and can be defined in code, read from file or fetched from a remote api. You can combine multiple data sources, cache valid licenses etc. for maximum flexibility.

All in all, LicenseKit gives you a lot of flexibility to craft your own license model. It’s currently in early beta, and has a free tier that you can use in production with a capped number of licenses. More integrations are being built, with the hope of letting you validate licenses from 3rd party providers shortly. If you or anyone you know are looking for ways to handle licenses in your apps or libraries, just reach ut or have a look at the [LicenseKit website]({{page.licensekit}}) for more information. I’d be happy to answer any questions that you may have.

Please share if you find it interesting. I appreciate all help I can possibly get to get this into the hand of users. Also please do let me know if you have any comments or feedback. I’d love to hear it.