---
title:  Using iPad Pro with Working Copy
date:   2017-11-05 20:20:00 +0100
tags:	git ios
image:	http://danielsaidi.com/assets/blog/2017-11-05.jpg
---


In this post, I will write about my experience using Working Copy on my iPad Pro,
adding a blog post to a Jekyll-powered blog, then pushing the result to GitHub.

![iPad Pro with Working Copy]({{ "assets/blog/2017-11-05.jpg" | absolute_url }})

When I bought my iPad Pro, I had some naive idea about using it as a lightweight
coding environment, at least for blogging, coding JavaScript etc. However, Apple
saying that the iPad Pro is more computer than a computer really didn’t help, as
I found setting it up for coding was more or less a no-can-do.

However, today I decided to give [Working Copy](https://workingcopyapp.com) a go.
Downloading it is straightforward, but as I tried to login to GitHub to clone my
repository, I was asked to give Working Copy full access to my SSH keys and more.
This seemed strange. I was rather expecting WC to simplify creating new keys and
add them to GitHub. I decided to skip this step for now and clone with HTTP.

After cloning the repo, I started giving blogging in Working Copy a try. In fact,
I am writing this very text as a test, to see if it works. Adding a new post was
super easy, so I copied the content from another MarkDown file and got started.

Working Copy has a nice, clean text editor. I immediately thought this was going
to be all I would need, but I quickly ran into some obvious deal breakers:

* The editor starts each new line with a capital letter, which forces me to type
two letters, then delete the first capital one. I haven’t found a setting that I
can use to disable this. It’s annoying and makes typing in MarkDown a hassle.

* The editor takes a long time to convert text to MarkDown. This causes new text
to have a larger font than the converted text, until the editor converts it.

However, bear in mind that Working Copy is a git client, not a text editor. That
I am at all able to write MarkDown texts as well as I can in Working Copy, is in
fact a bonus, rather than a let-down.

I will download a better text editor for the iPad and connect it to Working Copy.
Perhaps this will make typing more pleasant. I will write a blog post about this
once I get around to it.

When I finished typing, I could just switch over to status and commit my changes,
then push it to GitHub. This revealed the first Working Copy pay wall - you have
to pay to be able to push from the app. A *liittle* sneaky, but I won't complain,
since I too would love to create an app that people would love to pay for. Still,
I would have liked it if this info was presented sooner.

I would love for Apple to enable git push in iOS in some way, but I have NO idea
how they would do it, considering how the operating system is setup. The iPad is,
sadly, still a non-work tool, due to all the restrictions iOS has, compared to a
"real" computer.

However, I enabled Working Copy's trial mode and pushed this post to GitHub (why
yes, I am writing this in retrospect) to see if I could complete this task using
these tools. Pushing was super-simple, even with HTTPS, and after a little while,
the post popped up on my web site (yes, this very post indeed). It was now I did
notice the final problem, the one that killed off all my dear efforts.

It seems that Working Copy suffers from the iOS three-dash bug, which causes all
three-dashes to be reduced to one single dash. Actually, the bug can be as nasty
as to delete all text that comes after three dashes, but in this case it removes
two of three dashes. Since my site is created with [Jekyll](https://jekyllrb.com),
this means that pushing with Working Copy completely ruins the topmost necessary
[Jekyll Front Matter](https://jekyllrb.com/docs/frontmatter/).

I hope that this bug will be resolved in future versions of iOS, but until then,
I guess that I will be blogging from my computer.