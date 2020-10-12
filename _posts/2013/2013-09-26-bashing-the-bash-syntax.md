---
title: Bashing the Bash Syntax
date:  2013-09-26 08:13:00 +0100
tags:  ios bash xcode
---

![Bash icon](/assets/blog/2013-09-26-bash.png)

This post is dedicated to complaining about the Bash syntax from a n00b's perspective.

In my apps, I use a .sh script that automatically increments the build number to
a compact date stamp of when the app was built, prior to each release. I used to
do this manually, but decided to automate this a while back, which I wrote about
in a previous blog post.

As I wrote in that post, I found a great blog and .sh script that increments the
build number with 1 for each build. However, I wanted my script to set the build
number to a date formatted string, e.g. 201306130824, so I needed to modify this
original script a little.


## ` not " or '

To achieve this, I added an extension that allows me to switch between different
build number formats. I first believed this to be correct:

```sh
buildnum="date +%Y%m%d%H%M"
```

Since you can write e.g. "$CONFIGURATION", wrapping the date syntax in quotation
marks should work, right. Turns out it didn't. Single quotes did not work either.
Eventually, I found out that the correct syntax is to use `, like this:

```sh
buildnum=`date +%Y%m%d%H%M`
```

WTF!?

However, I accepted this awful syntax and calmed myself with the fact that I now
had a working build number bumping script.


## To little whitespace!

This nice script worked great until I started working with a second developer in
another project. Now the script was not as nice, since it bumped the build number
for both of us as soon we made the smallest change to the project.

The best thing would be to only bump the build number when performing a release
build. I read some articles about how to do this, and eventually decided to ditch
the additional target option we had and instead use the `$CONFIGURATION` variable,
which is set by Xcode and is accessible from within the script.

When performing a debug build, Xcode sets this value to "Debug", so we should be
able to check if the variable has this value, and abort the script if so is the
case. It should look something like this:


```sh
if ["$CONFIGURATION"="Debug"]; then
    exit 1
fi
```

Sadly, this did not work, so I read on and found the following warning:

*OBS! Note the whitespaces around the =*

Ah, so you obviously have to add whitespaces around the equal sign! Let's try it:

```sh
if ["$CONFIGURATION" = "Debug"]; then
    exit 1
fi
```

Hmmmm, still no luck. I wonder...no, that should be bad....that should be really
bad! They cannot require that you...that you...what, like this?

```sh
if [ "$CONFIGURATION" = "Debug" ]; then
   exit 1
fi
```

Sure thing, you also need spaces after [ and before ]

Obviously! I may be a n00b, but the Bash Gods are just plain evil.