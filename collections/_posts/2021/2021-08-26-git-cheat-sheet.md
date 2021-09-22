---
title:  Git tags cheat sheet
date:   2021-08-26 07:00:00 +0100
tags:   git
---


In this post, I'll list a couple of git commands that I found useful when cleaning up a very unstructured git tag history. This post is primarily meant to be a brain dump for my own use, but if you find the information useful, that's great.

In the scripts, `<PARAMNAME>` indicates where you should inject parameters.


## List local tags with a certain name prefix/suffix

```
git tag -l "<PREFIX>*"
git tag -l "*-<SUFFIX>"
```

## List remote tags with a certain name prefix/suffix

```
git ls-remote --tags <REMOTE> | grep "<PREFIX>-.*[^}]$" | cut -f 2
git ls-remote --tags <REMOTE> | grep "\<SUFFIX>.*[^}]$" | cut -f 2
```

## Delete local tags with a certain name prefix/suffix

```
git tag -d $(git tag -l "PREFIX-*") 
git tag -d $(git tag -l "*-<SUFFIX>")
```

## Delete remote tags with a certain name prefix/suffix

```
git push <REMOTE> --delete $(git ls-remote --tags <REMOTE> | grep "<PREFIX>.*[^}]$" | cut -f 2)
git push <REMOTE> --delete $(git ls-remote --tags <REMOTE> | grep "\<SUFFIX>$" | cut -f 2)
```

That's it for now, but I may return to this post and add new commands whenever I find some new ones that I want to remember.

If you have some commands that you think are worth sharing, feel free to share away :)