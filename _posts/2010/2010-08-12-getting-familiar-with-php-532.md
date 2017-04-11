---
title:	"Getting familiar with PHP 5.3.2"
date:	2010-08-15 12:00:00 +0100
categories: web
tags: 	php
---


Since I'm no real PHP developer (just pretending) I have only now started to use
PHP 5.3.2, although it was released quite long ago. The reason to this is that I
have just installed Aptana Studio 2, which comes with PHP 5.3.2, so I guess it's
time to learn it.

After installing Aptana Studio 2, I tried to execute a `build/packagetest` for a
PHP project of mine, but it did not work due to small modifications in PHP 5.3.2.

First of all, `get_class` no longer accepts string parameters anymore, so I will
just have to change all the places where I use it. Thank GOD for all unit tests!

I have used get_class in functions where a parameter could either be a string or
an object. Instead, I now use `is_string`, which is a lot cleaner.

I've also noticed that `parse_ini_file` is a bit shaky now. It is no longer able
to parse ini files that end with a row at which a parameter is specified (all my
previous ini files). The files cannot be parsed, probably since the line feed is
considered to be a part of the parameter value.

I solve this by adding an extra, empty line at the end of each ini file. I don't
know why this has changed, since it's both annoying and poses a risk of error in
case an ini file doesn't end with an empty line. I hope it will change in future
versions of PHP.