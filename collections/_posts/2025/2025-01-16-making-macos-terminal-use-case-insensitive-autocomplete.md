---
title:  Making macOS Terminal use case-insensitive autocomplete
date:   2025-01-16 07:00:00 +0000
tags:   macos terminal

assets: /assets/blog/25/0116/
image:  /assets/blog/25/0116/image.jpg
image-show: 0

gist: https://gist.github.com/tarun-ssharma/4d8619ac11a6a7c2091a57fa0f36c5aa

tweet:  https://x.com/danielsaidi/status/1879989784191631403
toot:   https://mastodon.social/@danielsaidi/113839930204827473
bsky:   https://bsky.app/profile/danielsaidi.bsky.social/post/3lfv4qlhv7s2b
---

In this blog post, let's see how to make the macOS Terminal use case-insensitive autocomplete for file and folders.

Although I moved from Windows to Mac almost 20 years ago, I every so often find myself accepting certain everyday frictions out of habit, when the solution is always one quick search away.

Todays friction was how I've always been annoyed about not being able to type `cd pro<tab>` and autocomplete it to the `Projects` folder.

And sure enough, the solution was once more just a quick search away. A discussion thread led me to [this gist](https://gist.github.com/tarun-ssharma/4d8619ac11a6a7c2091a57fa0f36c5aa) which contains two lines that you should add to `~/.zshrc`:

```swift
zstyle ':completion:*' matcher-list '' 'm:{a-zA-Z}={A-Za-z}' 'r:|=*' 'l:|=* r:|=*'
autoload -Uz compinit && compinit
```

Just add it to `~/.zshrc`, save and relaunch Terminal, and you'll have case-insensitive autocomplete!