---
title:  EmojiKit goes open-source
date:   2024-02-02 06:00:00 +0000
tags:   swift sdks

assets: /assets/blog/24/0202/
image:  /assets/blog/24/0202.jpg
image-show: 0

kankoda: https://kankoda.com
keyboardkit: https://keyboardkit.com

tweet:  https://x.com/danielsaidi/status/1753360511419175143?s=20
toot:   https://mastodon.social/@danielsaidi/111861348152490917
---

{% include kankoda/data/open-source name="EmojiKit" %}
I've decided to open-source the [EmojiKit]({{project.url}}) SDK and move it from my company [Kankoda]({{page.kankoda}}) to my own GitHub account. In this post, I'll discuss why and how it will be done.

![Header image]({{project.header}})


## Background

[EmojiKit]({{project.url}}) evolved as part of [KeyboardKit]({{page.keyboardkit}}). As it become quite capable, I decided to extract it to a separate product and inline it into KeyboardKit. 

However, converting it to a commercial SDK put restrictions on how KeyboardKit could use it, since the commercial parts could no longer be free in KeyboardKit. 

This worked well, but I think the library design suffered a bit, where many features became throwing to accomodate to the requirement of first registering a valid license key.

I've also struggled a bit with motivating a 3 tier license model. The cuts felt artificial, so the decision to open-source everything feels like the right move.


## How it will be done

The SDK will be moved from [Kankoda]({{site.urls.kankoda}})'s GitHub account to my personal GitHub account. I will move the entire repository, then remove the product pages and Gumroad product.

I'm excited about this change and hope that it will make EmojiKit even better for all of us.