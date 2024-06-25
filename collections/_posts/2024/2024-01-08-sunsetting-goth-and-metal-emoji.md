---
title:  Sunsetting the Goth & Metal Emoji Apps
date:   2024-01-08 06:00:00 +0000
tags:   general apps sunset

assets: /assets/blog/24/0108/
image:  /assets/blog/24/0108.jpg
image-show: 0

gothassets: /assets/apps/gothemoji/
metalassets: /assets/apps/metalemoji/

goth:   https://itunes.apple.com/app/goth-emoji/id1070226733
metal:  https://itunes.apple.com/app/metal-emoji/id1070228823

keyboardkit:    https://keyboardkit.com
nattskiftet:    https://www.instagram.com/nattskiftet/

tweet:  https://x.com/danielsaidi/status/1744297797447287111?s=20
toot:   https://mastodon.social/@danielsaidi/111719742304768223
---

After keeping the Goth & Metal Emoji apps alive for 8 years, I'm sunsetting them together with some other apps, to allow me to focus on fewer things.

![Header image]({{page.image}})

I created Goth Emoji with an amazing artist, [Lisen Haglund]({{page.nattskiftet}}), who had an idea to create a custom keyboard, like the Kim Kardashian's Kimoji app, but filled with Goth-styled artwork.

We launched Goth Emoji after just two weeks. It was my first stab at building a custom keyboard. Little did I know, this project would later cause a huge pivot in my life.

![Goth Emoji websiste]({{page.gothassets}}website.jpg)

Goth Emoji launched with a bunch of amazing goth artwork. Users could copy any "emoji" and paste it into any app, using either the app, the sticker pack or the custom keyboard.

Thanks to Lisen's Instagram following, this was the closest I've ever come to anything viral. The app rushed to top 3 in its category across the world, and generated...around $4,000.

Sure, this was good money for just a few days, but my illusion of how becoming viral on the App Store equalling becoming financially set for life had eventually come to an end.

We quickly followed up Goth Emoji with Metal Emoji, which contained cool, metal-inspired artwork. But unlike Goth Emoji, Metal Emoji never took off in the same way.

![Metal Emoji websiste]({{page.metalassets}}website.jpg)


## Technical challenges

The two apps shared a common foundation, which made it easy to add new features, but the asset management was a chore, where new each emoji took a long time to prepare.

Furthermore, the severe limitations in Apple's keyboard APIs became painfully apparent.

You see, custom keyboards basically only gets a reference to the text document, and can do limited operations with it, like inserting and deleting text, moving the cursor, etc.

You also don't get a keyboard view, just a container where you can add a custom view. You basically have to build the keyboard view and all its interactions from scratch.

Also, even though you can insert text, inserting an image is *not possible*. Since Goth Emoji was based on images, this was indeed a problem.


## How to insert images

All image-based custom keyboards have the same limitation, and most solve it by copying the image to the pasteboard, then asking the user to paste it into the target app. 

However, a custom keyboard isn't allowed to access the pasteboard until the user enables something called Full Access, which can only be enabled in System Settings.

Since this gives the keyboard access to the pasteboard, network access, etc. iOS shows a warning that the keyboard will be able to detect *everything* you type and send it *anywhere*.

Turns out that users neither like to copy/paste images from a keyboard, nor get a warning that makes them think that the keyboard will read everything they type.


## User reception

All in all, the technical limitations became quite a problem for the Goth Emoji app. People HATED to enable Full Access, and were very suspicious due to the alert. 

Despite describing this in the App Store description, the Full Access limitation resulted in many one star reviews and angry e-mails.

People also hated that we used the word "emoji" in the title, when in fact the content that the app provided were images and not real emojis.

As a technical person, I understand the nature (and limitations) of emojis and unicode, but of course this is not common knowledge. People were right in being disappointed.

While my initial ambition was to make a copy automatically trigger a paste, I couldn't find a way to do this. People expected a seemless experience. I did too.

This eventually led us to remove the custom keyboard and only keep the sticker pack and let people copy emojis from the main app. People loved the artwork, but reviews were bad.


## Sunsetting the apps

After 8 years, the apps still make money every month. Reviews are however bad, because of the misalign between user expectation and what the apps can deliver.

I have been waiting for some API updates to allow me to improve the apps, but since there doesn't seem to be any work done on these APIs, I think it's time to call it a day.

So, in the end, sunsetting the apps wasn't a hard decision. I don't want to make money on what I think are flawed products, especially when I can't improve them.

I have updated the apps a final time and will remove them from the App Store in February. Until then, I've made them free. Go and download them if you want some goth in your life.

You can download [Goth Emoji]({{page.goth}}) and [Metal Emoji]({{page.metal}}) from the App Store until February.


## Life changes

Even if I now say goodbye to these apps, they actually made my life take a drastic turn.

You see, my frustrations with the limited keyboard APIs triggered something inside me, and made me explore the field of custom keyboards further.

I started by creating an open-source project where I added all the tools that I came up with during my work with the Goth Emoji app. If they could help others, that'd be great.

I then started looking at creating a basic English keyboard view, since the native APIs don't provide any views. The first view was written in UIKit and was very limited, but worked well.

Theis project evolved over time, and as Apple introduced SwiftUI in 2019, everything came together. It was the missing piece of the puzzle.

Replacing UIKit with the more flexible SwiftUI made it possible for me to create a near pixel perfect keyboard view, add data-driven UI components and take the SDK further.

As [KeyboardKit]({{page.keyboardkit}}) become more capable, I started getting traction and eventually decided to release KeyboardKit Pro as a commercial add-on to the open-source foundation.

This eventually generated enough work, to in 2020 allowed me to quit my job to work on a freelance basis, with a KeyboardKit-related project being my very first.

Today, I've been a freelancer for almost 4 years. KeyboardKit is still evolving and supports 60+ languages, and features like autocomplete, dictation, etc.

KeyboardKit is truly a passion project. And to think it all started with a small app for letting people pick goth-related stickers. Life surely takes you to unexpected places.