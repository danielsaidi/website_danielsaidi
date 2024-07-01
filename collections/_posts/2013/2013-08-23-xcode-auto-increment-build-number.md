---
title: Auto-increment Build Number in Xcode
date:  2013-08-23 11:48:00 +0100
tags:  xcode
icon:  swift

assets: /assets/blog/13/0823/
image:  /assets/blog/13/0823.jpg

script: http://stackoverflow.com/questions/9258344/xcode-better-way-of-incrementing-build-number
---

When I released new version of iOS apps, I used to manually update the build number. It is however better to let Xcode do it automatically. Let's take a look at how to do it.

![Counter]({{page.image}})

When looking for ways to automate bumping the build number in Xcode, I found [this great script]({{page.script}}) that automatically increments the build number each time you build the app.

The original script will increment the build number by one each time. I prefer to have a date stamp, so I can see when a build was performed. I therefore use the `yyyymmddHHMM` format when bumping the build number of my apps.

To make this work, I replaced the default `buildnum` value with this one:

```sh
buildnum=`date +%Y%m%d%H%M`
```

This will set the build number to a timestamp instead of incrementing it by one.

Initially, Xcode may have problems executing your script. For this to work, you will need to enable run access. Do so by running the following script from the Terminal:

```sh
sudo chmod 755 'filename'
```

Execute it in the same folder as the shell script, and you should be good to go.