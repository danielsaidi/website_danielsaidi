---
title: Git tags cheat sheet
date:  2021-08-26 07:00:00 +0100
tags:  git
icon:  git
---


In this post, let's look at some git commands that I find useful. The post is primarily meant for future reference for myself, but can hopefully be useful to others as well.

Throughout the article, `<PARAM>` indicates where you should inject parameters.


## List local tags with a certain name prefix/suffix

This command lists local tags with a certain name prefix or suffix:

```
git tag -l "<PREFIX>*"
git tag -l "*-<SUFFIX>"
```

## List remote tags with a certain name prefix/suffix

This command lists remote tags with a certain name prefix or suffix:

```
git ls-remote --tags <REMOTE> | grep "<PREFIX>-.*[^}]$" | cut -f 2
git ls-remote --tags <REMOTE> | grep "\<SUFFIX>.*[^}]$" | cut -f 2
```

## Delete local tags with a certain name prefix/suffix

This command deletes local tags with a certain name prefix or suffix:

```
git tag -d $(git tag -l "PREFIX-*") 
git tag -d $(git tag -l "*-<SUFFIX>")
```

## Delete remote tags with a certain name prefix/suffix

This command deletes remote tags with a certain name prefix or suffix:

```
git push <REMOTE> --delete $(git ls-remote --tags <REMOTE> | grep "<PREFIX>.*[^}]$" | cut -f 2)
git push <REMOTE> --delete $(git ls-remote --tags <REMOTE> | grep "\<SUFFIX>$" | cut -f 2)
```