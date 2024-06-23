---
title:  EmojiKit goes open-source
date:   2024-02-02 06:00:00 +0000
tags:   swift open-source

image:  /assets/blog/2024/240202/title.jpg

tweet:  https://x.com/danielsaidi/status/1753360511419175143?s=20
toot:   https://mastodon.social/@danielsaidi/111861348152490917
---

{% include kankoda/data/open-source.html name="EmojiKit" %}
I've decided to open-source my [EmojiKit](https://github.com/danielsaidi/emojikit) library. In this post, I'll discuss why and how it will be done.

![Header image]({{project.header}})

I've decided to open-source the library foundation, with its models and many features, like categories, skin tone support, version information, search capabilities, localization, etc. 

I'll leave out some parts that have IP value to my products, but the rest will be opened up.


## Background

EmojiKit evolved as part of [KeyboardKit](https://keyboardkit.com). As it become quite capable, I decided to make it into a separate product and inline it into KeyboardKit. 

However, extracting it into a commercial SDK put restrictions into how KeyboardKit could use it, since the commercial parts could no longer be free in KeyboardKit. 

This worked well, but I think the library design suffered a bit, where many features became throwing to accomodate to the requirement of first registering a valid license key.

I've also struggled a bit with motivating a 3 tier license model. The cuts feel artificial, so the decision to open-source everything feels like the right move.


## How it will be done

The new open-source SDK will still be hosted under [Kankoda]({{site.kankoda}})'s GitHub account. I will move the code to a new repository in a series of live video streams and blog posts. 

I'm excited about this change and hope that it will make EmojiKit even better for all of us.