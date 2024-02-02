---
title:  EmojiKit goes open-source
date:   2024-02-02 06:00:00 +0000
tags:   swift open-source

image:  /assets/headers/emojikit.png

tweet:  https://x.com/danielsaidi/status/1747726878868209739?s=20
toot:   https://mastodon.social/@danielsaidi/111773321977694317
---

I've decided to open-source my [EmojiKit](https://kankoda.com/emojikit) library, which is currently a closed-source product at my company [Kankoda]({{site.kankoda}}). In this post, I'll discuss why and how it will be done.

![Header image]({{page.image}})

I've decided to open-source the EmojiKit foundation, with all its models, categories, localization, skin tones, version information, search capabilities etc. I'll leave out custom assets, pickers and keyboards, since they have IP value to my company products, but the rest will be open.

EmojiKit evolved as part of the [KeyboardKit](https://keyboardkit.com) project. After a few years, as it had become quite capable, I decided to make it into it's own product, and inline it into KeyboardKit. 

However, extracting the emoji features into a commercial SDK enforced some considerations into how KeyboardKit could use them, since the parts that were put under a commercial plan could not be free in KeyboardKit. This worked out well, but I think the library design suffered a bit, where many features became throwing in order fail if they were accessed without first registering a valid license key.

After having this in effect for a couple of months, I still struggle with motivating having the foundation as a 3 tier commercial product. The cuts feel quite artificial, and since I have no customers yet, the decision to open-source everything feels like the right move.

The new open-source SDK will be hosted under [my personal GitHub account]({{site.github_url}}) instead of under [Kankoda]({{site.kankoda}})'s. I will move the code to the new repository piece by piece, in a series of live video streams and blog posts. If you're excited about emojis and want to join this project, just reply here or to the tweet or toot, and I'll let you know when I get started working on it. 

I'm excited about this change, since I love working on open-source projects with other developers. I hope that this in time will make EmojiKit an even better SDK for all of us.