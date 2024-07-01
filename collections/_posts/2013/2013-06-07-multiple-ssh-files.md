---
title: Multiple SSH Files
date:  2013-06-07 07:08:00 +0100
tags:  git
icon:  git
---

When working with git, I use a single SSH key pair for GitHub, GitLab, etc. Today, though, I had to add a second key pair to be able to clone another remote repository. 

Since I haven't used multiple SSH keys before, I was not sure how to define which key to use where. Luckily, I found an article that suggested using an SSH config file.

With an SSH config file, you can specify which ssh key to use where, and if you want to use
a specific `IdentityFile`.

The article has since been removed, but I hope that the information in this post
is enough for you to find the information elsewhere.