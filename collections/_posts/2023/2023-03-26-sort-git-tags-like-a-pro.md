---
title:  Sort git tags like a pro
date:   2023-03-26 06:00:00 +0000
tags:   git

icon:   git
---

If you like me use semver (semantic versioning) and have projects with a gazillion version tags, it's nice to be able sort the tags in various ways. Let's see how to sort Git tags like a pro.

If you type `git tag` to list all tags, you will notice that the default sort order is alphabetic, which messes up higher version segments:

```
1.0.0
1.1.0
1.10.0
1.2.0
1.3.0
1.4.0
1.5.0
1.6.0
1.7.0
1.8.0
1.9.0
```

To sort the tags by ascending semver number, you can instead use `git tag --sort v:refname` to get the following result:

```
1.0.0
1.1.0
1.2.0
1.3.0
1.4.0
1.5.0
1.6.0
1.7.0
1.8.0
1.9.0
1.10.0
```

If you like me have repositories with many version tags, you may also want to sort the tags in descending orders. Not to worry, just type `git tag --sort -v:refname` instead:

```
1.10.0
1.9.0
1.8.0
1.7.0
1.6.0
1.5.0
1.4.0
1.3.0
1.2.0
1.1.0
1.0.0
```

If you always want to use a certain sort order, you can just set the `tag.sort` setting to the sort order of your choice. For instance, this Terminal command makes Git use descending order for all repos:

```
git config --global tag.sort -v:refname
```

That's it, you're now a Git tag sorting pro! 🎉