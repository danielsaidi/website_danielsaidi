---
title: Auto-increment Build Number in Xcode
date:  2013-08-23 11:48:00 +0100
tags:  ios xcode
---

![Counter](/assets/blog/2013-08-23-counter.jpg)

When building and releasing iOS apps, I used to manually update the build number
of the app. However, I found [this great script](http://stackoverflow.com/questions/9258344/xcode-better-way-of-incrementing-build-number)
that will automatically increment the build number each time you build the app.


## Date as build number

However, that script will increment the build number by one each time the app is
build. I prefer to have a date stamp, so that I can immediately see when a build
was performed. I therefore use a build number with a date format: “yyyymmddHHMM”.

To make this work, I replaced the default builnum value with this one:

```sh
buildnum=`date +%Y%m%d%H%M`
```

This will set the build number to a timestamp instead of incrementing it by one.


## Run script permissions

Initially, XCode may have problems executing your script. For this to work, you
will need to enable run access. Do so by running the following terminal script:

```sh
sudo chmod 755 'filename'
```

Execute it in the same folder as the shell script, and you should be good to go.