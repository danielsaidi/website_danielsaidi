---
title: git autocompletion in OS X Terminal
date:  2017-01-26 10:56:02 +0100
tags:  git macos

post:  http://apple.stackexchange.com/questions/55875/git-auto-complete-for-branches-at-the-command-line
---

After procrastinating for too long, I finally spent a minute to setup git autocomplete
in the Terminal. The original discussion on this topic is
found [here]({{page.post}}).

To enable git autocomplete, first run the following command to download the autocomplete
script:

```
curl https://raw.githubusercontent.com/git/git/master/contrib/completion/git-completion.bash -o ~/.git-completion.bash
```

This will download the script and place it as a hidden file in your home folder. After
this, open or create `~/.bash_profile` and add the following to it:

```
test -f ~/.git-completion.bash && . $_
```

This will make the Terminal run the `.git-completion.bash` script if it exists.

After this, restart the Terminal or open a new tab to apply this change. You should
now be able to use autocomplete for branch names, git commands etc.
