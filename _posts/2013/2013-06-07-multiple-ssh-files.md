---
title: Multiple SSH Files
date:  2013-06-07 07:08:00 +0100
tags:  git
---

When working with git, I have mainly used a single SSH key pair, which I use for
e.g. GitHub.

Today, though, I had to add a second key pair to be able to clone another remote
repository. Since I have not used multiple SSH keys simultaneously before, I was
not sure how to define which key to use where.

Luckily, [this great article](http://nerderati.com/2011/03/simplify-your-life-with-an-ssh-config-file/)
suggests using an SSH config file, in which you can specify which ssh key to use
where, and if you want to use a specific IdentityFile.