---
title: Getting familiar with PHP 5.3.2
date:  2010-08-12 12:00:00 +0100
tags:  archive
---

Since I'm just pretending to be a PHP developer, I only just now started to use
PHP 5.3.2. The reason for this is that I just installed Aptana Studio 2, which
comes with PHP 5.3.2 installed.

After installing Aptana Studio 2, I tried to execute a `build/packagetest` for a
PHP project of mine, but it didn't work due to changes in PHP 5.3.2.

First of all, `get_class` no longer accepts string parameters, so I will have to
change all places where I use it. Thank GOD for unit tests!  I used `get_class`
in functions where a parameter could either be a string or an object. Instead, I
now use `is_string`, which is a lot cleaner.

I've also noticed that `parse_ini_file` is a bit shaky now. It's no longer able
to parse ini files that end with a row at which a parameter is specified (as in
all my previous ini files). The files cannot be parsed, probably since the line
feed is considered to be a part of the parameter value.

I solved this by adding an extra, empty line at the end of each ini file. I don't
know why this has changed, since it's annoying and poses a risk of error in case
an ini file doesn't end with an empty line. I hope that this will change in future
versions of PHP.