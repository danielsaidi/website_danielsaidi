---
title:  Git tags cheat sheet
date:   2021-08-26 07:00:00 +0100
tags:   git
---


In this post, I'll list a couple of git commands that I found useful when cleaning up an unstructured git tag history. The post is primarily meant for future reference, but if you find it useful, that's great.

In the scripts, `<PARAM>` indicates where you should inject parameters.


## List local tags with a certain name prefix/suffix

To list local tags with a certain name prefix or suffix, run the following Terminal commands:

```
git tag -l "<PREFIX>*"
git tag -l "*-<SUFFIX>"
```

## List remote tags with a certain name prefix/suffix

To list remote tags with a certain name prefix or suffix, run the following Terminal commands:

```
git ls-remote --tags <REMOTE> | grep "<PREFIX>-.*[^}]$" | cut -f 2
git ls-remote --tags <REMOTE> | grep "\<SUFFIX>.*[^}]$" | cut -f 2
```

## Delete local tags with a certain name prefix/suffix

To delete local tags with a certain name prefix or suffix, run the following Terminal commands:

```
git tag -d $(git tag -l "PREFIX-*") 
git tag -d $(git tag -l "*-<SUFFIX>")
```

## Delete remote tags with a certain name prefix/suffix

To delete remote tags with a certain name prefix or suffix, run the following Terminal commands:

```
git push <REMOTE> --delete $(git ls-remote --tags <REMOTE> | grep "<PREFIX>.*[^}]$" | cut -f 2)
git push <REMOTE> --delete $(git ls-remote --tags <REMOTE> | grep "\<SUFFIX>$" | cut -f 2)
```


## Conclusion

There's really nothing to conclude, but I hope you find these commands useful. I may return to this post and add new commands later, if I find new ones worth remembering. 

If you have some commands that you think are worth sharing, feel free to share!