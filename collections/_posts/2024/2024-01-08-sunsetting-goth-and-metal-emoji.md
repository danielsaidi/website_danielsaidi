---
title:  Sunsetting the Goth & Metal Emoji Apps
date:   2024-01-08 06:00:00 +0000
tags:   general

assets: /assets/apps/gothemoji/
image:  /assets/blog/2024/240108/header.jpg
metalassets: /assets/apps/metalemoji/
keyboard-header: /assets/headers/keyboardkit.png

goth:   https://itunes.apple.com/app/goth-emoji/id1070226733
metal:  https://itunes.apple.com/app/metal-emoji/id1070228823

keyboardkit:    https://keyboardkit.com
nattskiftet:    https://www.instagram.com/nattskiftet/
---

After keeping the Goth & Metal Emoji apps alive for 8 years, I'm sunsetting them together with some other apps, to allow me to focus on fewer things.

![Header image]({{page.image}})

I created Goth Emoji with an amazing artist friend, [Lisen Haglund]({{page.nattskiftet}}), who had an idea to create a custom keyboard, like the Kim Kardashian's Kimoji app, but filled with Goth-styled artwork.

We launched Goth Emoji after just two weeks of development. It was my first stab at building a custom keyboard. Little did I know, this project would later cause a huge pivot in my life.

![Goth Emoji websiste]({{page.assets}}website.jpg)

Goth Emoji launched with a bunch of amazing goth artwork. Users could copy any "emoji" and paste it into any app, using either the app, the sticker pack or the custom keyboard.

Thanks to Lisen's Instagram following, this launch was the closest I've ever come to anything viral. The app quickly became top 3 in its category across the world, and generated...around $4,000.

Sure, this was good money for just a few days, but my illusion of how becoming viral on the App Store equalling becoming financially set for life had eventually come to an end.

We quickly followed up Goth Emoji with Metal Emoji, which contained cool, metal-inspired artwork. But unlike Goth Emoji, Metal Emoji never took off in the same way.

![Metal Emoji websiste]({{page.metalassets}}website.jpg)

The two apps shared a common foundation, which made it easy to add new features, fix bugs, etc. but the asset management was still a chore, where new each emoji took some time to prepare.


## Technical challenges

While developing Goth Emoji, the severe limitations in Apple's custom keyboard APIs became painfully apparent, and forced me to be creative.

You see, custom keyboards basically only gets a reference to the text document, and can do very limited operations with it, like sending text to it, deleting backwards, moving the cursor, etc.

Furthermore, you don't even get a keyboard view, just a container area where you can put any view you like. Apple don't provide a keyboard view, so you basically have to build everything yourself.

Also, even though you can send text to the app, a seemingly equal like sending an image is *not possible*. Since Goth Emoji was based on images, this was indeed a problem.

All image-based custom keyboards have the same limitation, and most solve it by copying the image to the pasteboard, then showing a message to ask the user to paste it into the currently active app. 

But...custom keyboards are not allowed to access the pasteboard before the user has enabled a thing called Full Access, which gives access to more features, like the pasteboard, network access, etc.

Users have to go to System Settings to enable Full Access, and when they do, iOS shows a warning that keyboards with Full Access can detect *everything* you type and send it *anywhere*.

All in all, this required a lot more work than I first expected, and the user experience was quite bad. I would have loved to access the last string in the pasteboard without Full Access, but here we are.

Apple also rejected the first version of Goth Emoji, since we just blocked the keyboard if Full Access was disabled. Turns out the App Store Guidelines require that a keyboard works even without Full Access.

To work around this, I made the images just send the accessibility label to the app, like "Daniel sent you a glass of blood". Although being a completely useless replacement, this made the app pass review.


## User reception & Learnings

All in all, the technical limitations of custom keyboards became quite a problem for the Goth Emoji app. 

First of all, people HATED having to enable Full Access, and were very suspicious due to the alert. Despite describing this in the App Store description, it resulted in many one star reviews and angry e-mails.

Second, people utterly disliked the word "emoji" in the title, since these were images and not real emojis. And they were of course correct.

My ambition had been to make the copy/paste mechanism hidden and automatically trigger the paste, but I couldn't find a way to do this. People expected a seemless experience. I did too.

This eventually led us to remove the custom keyboard and only have the sticker pack, and to let people copy emojis from the main app. But reviews were still bad, even if people loved the artwork.


## Sunsetting the apps

After 8 years on the App Store, the apps still make a little money every month. Reviews are however bad, because of the misalign between user expectation and what the apps can deliver.

So sunsetting the apps wasn't a hard decision. I haven't actively worked on the apps for many years, and don't want to make money on what I fundamentally think are subpar user experiences.

And since there doesn't seem to be any adjustments to the limited custom keyboard APIs, I think it's time to call it a day.

However, even if I now say goodbye to these apps, working on them for a few weeks every now and then actually resulted in my life taking a quite drastic turn.


## Life changes

Being a tech nerd at heart, my frustrations with the limited keyboard APIs triggered something inside me. I just had to explore the field of custom keyboards further.

I started by creating an open-source project, into which I added all the extensions, utilities, etc. that I came up with during my work with the Goth Emoji app. If they could help others, that'd be great.

I then started looking into creating a basic, English keyboard, since the native APIs don't provide any views whatsoever. The first keyboard was written in UIKit and was very limited, but worked.

The open-source project evolved over time, and as Apple launched SwiftUI in 2019, everything came together. This had been the missing piece of the puzzle.

Being able to replace the hard to customize UIKit with the very flexible SwiftUI made it possible for me to create an almost pixel perfect keyboard view, add data-driven UI components and take the SDK further.

As [KeyboardKit]({{page.keyboardkit}}) become more and more capable, I started seeing some traction, and in 2020 I finally decided to quit my day job to work on a freelance basis, with a KeyboardKit project being my very first.


## Conclusion

Today, I've been a freelancer for almost 4 years. KeyboardKit is still evolving and I'm still learning new quirks of the many wonderful languages of our world.

KeyboardKit has let me meet so many amazing people and companies from all over the world. I'm so grateful for being able to work on such an complex and exciting project. It's truly a passion project.

And to think that it all started with a small app for letting people pick goth-related, emoji-like stickers.