---
title:  "git autocompletion in OS X Terminal"
date:   2017-01-26 10:56:02 +0100
tags:	git
---


After procrastinating for too long, I finally spent a minute of my life to setup
git autocomplete in the macOS terminal. The original discussion on this topic is
found [here](http://apple.stackexchange.com/questions/55875/git-auto-complete-for-branches-at-the-command-line).

First, run the following command in Terminal to download the autocomplete script:

```
curl https://raw.githubusercontent.com/git/git/master/contrib/completion/git-completion.bash -o ~/.git-completion.bash
```

This will download the autocomplete script and place it as a hidden file in your
home folder. After this, open (or create if you have none) `~/.bash_profile` and
add the following to it:

```
test -f ~/.git-completion.bash && . $_
```

This will make the Terminal run the `.git-completion.bash` script if it exists.

After this, restart Terminal or open a new tab to apply this change. You should
now be able to autocomplete branch names, git commands etc.
