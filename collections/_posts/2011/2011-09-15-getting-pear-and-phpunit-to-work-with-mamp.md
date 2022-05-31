---
title: Getting PEAR and PHPUnit to work with MAMP
date:  2011-09-15 12:00:00 +0100
tags:  php testing web
icon:  php

tutorial: http://akrabat.com/php/setting-up-php-mysql-on-os-x-10-7-lion/
---

When I recently decided to start re-creating a php project of mine from scratch,
I decided to replace `SimpleTest` with `PHPUnit` and `PHPCover`.

To get familiar with PEAR, I found [this great tutorial]({{page.tutorial}}).
However, since I managed to screw up my PEAR configurations while playing around
with it a while back, the tutorial didn't work.

Turns out that PEAR was missing from where it should be installed, and that
multiple PEAR installations were scattered all over the file system. The config
file pointed to one of these locations, which made the installer believe that I
had the latest release installed.

Turns out that:

- I had managed to set a different install path, due to [this](http://www.reddit.com/r/PHP/comments/iyu3f/pearpecl_is_missing_from_osx_lion_heres_how_to/).
- PEAR was always downloaded to my user root.
- I had to delete all PEAR installations.
- I also had to delete the config file.

I could then follow the tutorial at the top of this post. Installing PHPUnit
was a breeze after the invalid PEAR settings were fixed. However, you also need
to get PHPCover, which is described [here](https://github.com/sebastianbergmann/phpunit/).

You also have to set the include paths in `/etc/php.ini`. Mine looks like this:

    include_path=”.:/php/includes:/usr/lib/php:/usr/lib/php/pear

However, since I use MAMP, I also neededed to modify this file in MAMP’s config
area and add the paths there as well.

PHPUnit now works, but yet now convinced. SimpleTest seems easier to setup and
flexible enough to cover all test cases I need, including mocking. It also makes
it possible to ship the testing framework with the dev bundle. 

Time will tell if I decide to go with PHPUnit or stick with SimpleTest.

