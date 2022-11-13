---
title: Bashing the Bash Syntax
date:  2013-09-26 08:13:00 +0100
tags:  xcode
image: /assets/blog/2013/2013-09-26-bash.png

post:  /blog/2013/08/23/xcode-auto-increment-build-number
---

This post is dedicated to complaining about the Bash syntax from a n00b perspective. It's just a rant. Don't take it too seriously.

![Bash icon]({{page.image}})

In my apps, I use a shell script that automatically increments the build number to a compact date stamp of when the app was built, prior to each release. I used to do this manually, but decided to automate this a while back, which I wrote about in a [previous blog post]({{page.post}}).

As I wrote in that post, I found a great script that increments the build number with 1 for each build, then modified the script to use dates instead of increments.


## ` not " or '

To achieve this, I added an extension that allows me to switch between different build number formats. I first believed this to be correct:

```sh
buildnum="date +%Y%m%d%H%M"
```

Since you can write e.g. `"$CONFIGURATION"`, wrapping the date syntax in quotation marks should work, right. Turns out it didn't. Single quotes did not work either.

I eventually found out that the correct syntax is to use `, like this:

```sh
buildnum=`date +%Y%m%d%H%M`
```

I accepted this awful syntax and calmed myself with that I now had a working build number script.


## To little whitespace!

The script worked great until I started working with another developer in another project. Now the script wasn't as nice, as it bumped the build number for both of us as soon we made changes to the project.

The best would be to only bump the build number when performing a release build. I read some articles about this, and eventually decided to ditch the additional target option that we had and instead use the `$CONFIGURATION` variable, which is set by  Xcode and is accessible from within the script.

When performing a debug build, Xcode sets this value to "Debug". We should be able to check if the variable has this value, and abort the script if so is the case. 

It should look something like this:

```sh
if ["$CONFIGURATION"="Debug"]; then
    exit 1
fi
```

Sadly, this didn't work, so I read on and found the following warning:

`OBS! Note the whitespaces around the =`

Ah, so you obviously have to add whitespaces around the equal sign! Let's try it:

```sh
if ["$CONFIGURATION" = "Debug"]; then
    exit 1
fi
```

Hmmmm, still no luck. I wonder...no, that should be bad....that should be really bad! They can't require that you...that you...what, like this?

```sh
if [ "$CONFIGURATION" = "Debug" ]; then
   exit 1
fi
```

Sure thing, you also need spaces after `[` and before `]`. 

I may be a shell script n00b, but this syntax is just plain evil.