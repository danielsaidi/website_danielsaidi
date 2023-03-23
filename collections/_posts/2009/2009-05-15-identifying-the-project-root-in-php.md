---
title: Identifying the project root in PHP
date:  2009-05-15 10:20:00 +0100
tags:  archive
icon:  php
---

I currently have problems with identifying the project root in a PHP project. The
problem applies to PHP, but the discussion is general and applies to the other
languages and environments as well.


## Problem description

I will use my PHP-based application framework, Wigbi, as a contextual example, so
that we have some real-life situations. 

Wigbi has many classes that may be used anywhere in the project folder hierarchy, 
so identifying the project root is a major issue. Wigbi provides two ways of doing
this - the two static `Wigbi::rootPath` and `Wigbi::clientRootPath` properties.

Before I explain why we need two properties for this, let me discuss why absolute
and relative paths are not good alternatives to handle this situation.


## Absolute and relative paths

*Absolute paths* use the server root (e.g. /img/pixel.gif) as a starting point.
They shouldn't be used, since the absolute root may differ between production
and live environments. If you don't want to force a solution to have the same
structure in all environments, absolute paths are a no go.

Relative paths (e.g. ../img/pixel.gif) are also common, but mustn't be used by
files that may be used in various parts of the project folder hierarchy. For
instance, Wigbi may be included by both *index.php* and *music/index.php*, which
makes relative paths unusable. The same applies to ASP.NET User Controls, which
may be added to pages anywhere in the folder hierarchy.

You may think that relative paths are OK for web pages, since pages have a fixed
position in the project folder (e.g. news/index.php). This is also *not true*,
since URL rewriting may throw pages around so that */news/latest.php* become
*/latestnews*. This will cause relative paths to fail.

In short, absolute and relative paths are problematic. Only use them when you
fully understand how they are to be used, and the drawbacks of each approach.


## Identifying the project root

ASP.NET has a great way of identifying the project root: the ~ variable. ~ which
can be added as a prefix to any path that is to be used by the client. ASP.NET
will then convert it to the application root path when the page is parsed by the
server. This means that you can always use ~ and be sure that it will resolve to
the project root, which should be the only starting point you have to consider.

However, this only applies to client related paths, not for server files. Wigbi
therefore has two properties that can be used to identify the project root: 
`Wigbi::rootPath` and `Wigbi::clientRootPath`. The first is to be used on the
server and the second in the browser.


## Wigbi::rootPath

`Wigbi::rootPath` identifies the project root for the executing server file. For
instance, when Wigbi is started and loads the current language file, it uses this
property to locate the file, starting from the path to the project root from where
the code executes. As we'll see, this isn't always the executing page.

If you write a class that is to work with files, `Wigbi::rootPath` will work, no
matter where in the folder hierarchy you are.


## Wigbi::clientRootPath

The fact that `Wigbi::rootpath` applies to the executing page causes problems for
the AJAX pipeline, since all pages call `wigbi/postBack.php`. This means that any
file paths that are sent to the client (image paths, links etc.) will be relative
to this page, if you use `Wigbi::rootpath`.

Due to this, there is a `Wigbi::clientRootPath` property that is set whenever an
AJAX operation is executed. It takes the root path used by the page and sends it
so that any path that is returned to the client will be relative to the page. If
there is no set client root path, the property is equal to the root path.

This works, but I would prefer to have a single variable instead of these two. I
also I didn't predict some problems that I discovered later.


## URL rewriting = PROBLEM!!!

So, we have these properties:

* `Wigbi::rootPath` - used by PHP files to work with files on the server.
* `Wigbi::clientRootPath` - used by any path that it to be used by the client.

This should be sufficient, right? Wrong! As I added URL rewrite rules, I noticed
that themes and JavaScript code stopped working.

The problem turned out to be:

* The *root path* is the path of the *executing* page (index.php), which is an empty string.
* In PHP, the root path is *correct*, since the physical file is executed.
* In the browser, the root path is *incorrect*, since the page thinks it has an address that it hasn't.
* PHP doesn't know about the resulting url, only the expected. 
* As such, the root path should be `../`, but becomes blank.

Any ideas how to handle this?


