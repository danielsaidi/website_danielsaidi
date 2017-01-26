---
title:  "git autocompletion in OSX Terminal"
date:   2017-01-26 10:56:02 +0100
categories: general
tags:	git
---


After procrastinating for too long, I finally decided to spend one minute of my
life to setup git autocomplete on my work computer.

First, run the following command in the Terminal, to get the autocomplete script:

{% highlight bash %}
curl https://raw.githubusercontent.com/git/git/master/contrib/completion/git-completion.bash -o ~/.git-completion.bash
{% endhighlight %}

This will download the autocomplete script and place it as a hidden file in your
home folder.

Then, open (or create if you have none) `~/.bash_profile` and add the following:

{% highlight bash %}
test -f ~/.git-completion.bash && . $_
{% endhighlight %}

This will make the Terminal run the `.git-completion.bash` script if it exists.

After this, you can press tab while typing a branch name, to get the Terminal to
auto complete the branch name as much as possible, just as when you type `cd`.
