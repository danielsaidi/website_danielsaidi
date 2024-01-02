---
title: Auto-increment Build Number in Xcode
date:  2013-08-23 11:48:00 +0100
tags:  xcode
icon:  swift

assets: /assets/blog/2013/130823/
image: /assets/blog/2013/130823/header.jpg

script: http://stackoverflow.com/questions/9258344/xcode-better-way-of-incrementing-build-number
---

When releasing new version of my iOS apps, I used to manually update the build number. However, a better approach is to have Xcode do it automatically.
Let's take a look at how to do it.

![Counter]({{page.image}})

When looking for ways to automate bumping the build number in Xcode, I found [this 
great script]({{page.script}}) that will automatically increment the build number 
each time you build the app.

However, the original script will increment the build number by one each time the app 
is build. I prefer to have a date stamp, so that I can immediately see when a build
was performed. I therefore use a build number with the date format `yyyymmddHHMM`.

To make this work, I replaced the default `buildnum` value with this one:

```sh
buildnum=`date +%Y%m%d%H%M`
```

This will set the build number to a timestamp instead of incrementing it by one.

Initially, Xcode may have problems executing your script. For this to work, you
will need to enable run access. Do so by running the following script from the Terminal:

```sh
sudo chmod 755 'filename'
```

Execute it in the same folder as the shell script, and you should be good to go.