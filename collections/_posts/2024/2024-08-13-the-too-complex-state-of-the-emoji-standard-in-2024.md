---
title:  The (too?) complex state of the Emoji standard in 2024
date:   2024-08-13 06:00:00 +0000
tags:   ios emojis

assets: /assets/blog/24/0813/
image:  /assets/blog/24/0813.jpg
image-show: 0

tweet:  https://x.com/danielsaidi/status/1823333023103799668
toot:   https://mastodon.social/@danielsaidi/112954669061960259
---

In this post, we'll take a look at the current state of the emoji standard, and it's support for skin tone, gender and direction variants...and how it may have reached its peak as Apple for the first time only has *removed* emojis in iOS 17.4.

{% include kankoda/data/open-source.html name="SwiftUIKit" %}


## The straw that broke the camel's back?

Before we start looking at the emoji standard as a whole, let's take a quick look at what I believe is a tell that Apple may have had enough of its massive leap in complexity.

You see, while iOS 17.4 did add some new emojis, like 🙂‍↔️, 🙂‍↕️, 🐦‍🔥, 🍋‍🟩, 🍄‍🟫, ⛓️‍💥, and many new direction variants of people, it also did something unprecedented - *removing* emojis!

Yes, for the first time ever, Apple have *removed* emojis, as iOS 17.4 removes *all* skin tone and gender varied family emojis.

In iOS 17.4, Apple have replaced all family emojis with simple badges, that are now listed under "Symbols" instead of next to the kissing people under "Smileys & People":

![A native emoji keyboard with symbols]({{page.assets}}keyboard-symbols.jpg)

This means that there are no more family emojis under "Smileys & People", which reduces the list of multi-component emojis with skintone support in this category:

![A native emoji keyboard with people]({{page.assets}}keyboard-people.jpg)

However, Apple has also *added* new direction variants for some emojis, so many emojis now have left and right variations, with preserved gender and skin tone variations:

![A native emoji keyboard with people moving]({{page.assets}}keyboard-moving.jpg)

So while the removal is unprecedented, where previous versions have just added emojis, perhaps the fact that family emojis consist of 3 or more parts made it unsustainable.


## The current state of emojis

I personally loved the emoji standard's initial ambition of providing neutral visualizations of people, things and "concepts", where the yellow skin tone was meant to include everyone.

As skin tone support was later added, however well-intended, it did open up a door to the world of exponential complexity, which would eventually cause the standard to explode.

You see, besides that some emojis were now extended to support 6 different skin tones:

![Skin tone variants]({{page.assets}}skintone-variants.jpg)

some were soon also extended with gender variants, where emojis that supported it came in 3 variations - male, female and unspecified:

![Gender variants - faces]({{page.assets}}gender-variants-1.jpg)

This was however not only limited to faces, but also applied to the tiny full-body emojis as well as the half-body ones, like these mermaids, fairies and people:

![Gender variants - bodies]({{page.assets}}gender-variants-2.jpg)

And since emojis now support skin tones, *all* of these emojis came in all skin tone variants:

![Gender variants - bodies]({{page.assets}}skintone-variants-2.jpg)

This means that for each new emoji (1) that supports skin tone (6) and gender variants (3), we get a total of **18 illustrations**, for *each emoji*.

But that's not all. You remember the introduction of direction variations (2)? Well, you can now double that number, which means that each emojis come in **36 variants**.

![A native emoji keyboard with people]({{page.assets}}keyboard-moving-callout.jpg)

**BUT THAT'S NOT ALL!** Let's take a look at multi-component based emojis, which adds even more complexity to the mix.

You see, multi-component emojis come in many designs, where "people holding hand" has woman-man, woman-woman & man-man combinations, and love/kissing has some more:

![Multi-component emojis]({{page.assets}}keyboard-multi-component.jpg)

What's so special with these emojis, is that you can vary *both* components, which makes the skin tone popover *very* complicated:

![Multi-component emojis with a skin tone popover]({{page.assets}}keyboard-multi-component-callout.jpg)

If you consider that family emojis consist of 3+ components, you can see how this could turn in to a technical and UX nightmare, if you want to honor all variations everywhere.

Welcome to the world of exponential complexity.


## Personal Reflections

While I think the idea of supporting skin tones and gender variations was admirable (the direction support is just strange), I think it was a wrong turn made by the emoji standard.

I loved the clean, one standard for everyone idea that the emoji standard had in its early days. Opening it up to variations means you've got to keep varying.

Because let's face it, there are more skintones than this, more gender variations than this. By including some, you will exclude others.

As a friend of mine said: an approach of "excluding everyone" leads to including everyone. 

This much focus on skin tone and gender is the *exact opposite* of the intial, fully agnostic approach of the emoji standard. I actually fears it causes more harm than it does good.

Because in the early days, it would have been impossible to type 👩🏻‍❤️‍💋‍👩🏿🤮 to indicate that you don't think that people of the same sex and different skin tones should love each other.

Perhaps too much focus on skin tone and gender mostly appeal to people who put too much focus on skin tone and gender?


## Other technical implications

Besides technical complexities and my personal reflections, removing emojis lead to other unprecedented problems.

For instance, my [EmojiKit](https://github.com/danielsaidi/emojikit) SDK contains information about all emojis that the various emoji versions have introduced. It was easy, since new versions only added new emojis.

With the removal of family emojis, this means that EmojiKit renders the old emojis like this:

![A screenshot of a 3rd party emoji keyboard]({{page.assets}}keyboard-3rdparty.jpg)

When a version may now remove emojis, I can no longer keep a single source of thruth, then filter out emojis that weren't part of older versions from that truth.

And since the family emojis no longer *exist* in the OS, I would need to take screenshots of an old OS version and add image assets to the app, since the OS can't render the emojis.

I think I will just put the new family emoji badges in their correct place, then remove the old family emojis and just leave a comment regarding it in the version where they were added.



## Conclusion

I personally think the gender and skin tone focus has been an unfortunate sidetrack in the emoji standard, and understand why companies like Apple would remove complex multi-component skin tone variants, but it indeed raises some questions regarding the future.

While I doubt that the entire standard would backtrack the skin tone and gender support, I hope that the future may instead be to include more unique art to the emoji standard, to truly reflect the beautiful world of humanity.





