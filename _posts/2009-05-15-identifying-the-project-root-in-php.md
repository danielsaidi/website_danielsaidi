---
title:  "Identifying the project root in PHP"
date:   2009-05-15 10:20:00 +0100
categories: web
tags: 	php asp-net
---


I am currently having problems with identifying the project root in one of my PHP
projects. The separate ways to do so are easy enough, but have problems combining
them.

The problem applies to PHP, but the discussion is general and applies to the same
situation in other languages and environments as well.

I will use my PHP-based application framework, Wigbi, as a contextual example, so
that we have some real-life situations to consider.


## Problem description

Since Wigbi includes many classes and may be used anywhere in the project folder
hierarchy, identifying the project root is a major issue. Wigbi provides two ways
of doing this - the `Wigbi::rootPath` and `Wigbi::clientRootPath` properties.

I will soon explain why we need two properties for this, but before I do, I just
want to discuss absolute and relative paths, which may seem like the way to go at
first, but are not good alternatives.


## Absolute and relative paths

*Absolute paths* use the server root (e.g. /img/pixel.gif) as a starting point.
They should not be used, since the absolute root may differ between production
and live environments. If you do not want to force a solution to have the same
structure in all environments (god knows I don't), absolute paths are a no go.

Relative paths (e.g. ../img/pixel.gif) are also seen every once in a while, but
must not be used by files that may be used in various parts of the project folder
hierarchy. For instance, Wigbi may be included by *index.php* and *music/index.php*,
which makes relative paths unusable. The same applies to ASP.NET User Controls,
which may be added to pages anywhere in the folder hierarchy.

Some may at first think that relative paths are OK for pages in a web site, since
pages have a fixed position in the project folder (e.g. news/index.php). This is
*not true*, since URL rewriting may throw pages around so that */news/latest.php*
may become */latestnews*. This will cause relative paths to fail.

In short, absolute and relative paths are rarely ok to use, if not working with
fairly simple sites.


## The solution: Identify the project root

ASP.NET has a great way of identifying the project root: the ~ variable. ~ can be
added to the beginning of any path that is to be used by the client. ASP.NET will
then convert it to the application root path when the page is sent to the server.
This means that you can always use ~ and be sure that it will begin at the project
root, which should be the only starting point you should have to consider. However,
this only applies to client related paths, not for files on the file system.

Wigbi features two properties for identifying the project root - `Wigbi::rootPath`
and `Wigbi::clientRootPath*` The first property is to be used on the server, and
the second to be used in the browser.


## Wigbi::rootPath

`Wigbi::rootPath` identifies the project root for the executing page. For instance,
when Wigbi is started and loads the current language file, it uses this property to
load a language file, starting from the path to the project root from where the code
executes. As we will see, this does not have to be the executing page.

If you write a class that is to work with files, `Wigbi::rootPath` will work, no
matter where in the folder hierarchy you are.


## Wigbi::clientRootPath

The fact that `Wigbi::rootpath` applies to the executing page will cause problems
when working with the AJAX pipeline, since all pages call the *wigbi/postBack.php*
page, which *is *the executing page. This means that any file paths that are sent
to the client (such as image paths, links etc.) will be relative to the *postback*
page, if you use `Wigbi::rootpath*`

Due to this, I created the `Wigbi::clientRootPath` property, which is set whenever
an AJAX operation is executed. It takes the root path used by the page and sends
it so that any path that is returned to the client will be relative to the page. If
there is no set client root path, the property is identical to the root path.

This works well, but I would prefer to have a single variable instead of the two
I have now. Furthermore, I did not foresee the problems that I discovered later.


## URL rewriting = PROBLEM!!!

OK, so I have these properties:

`Wigbi::rootPath` - used by PHP files to work with files on the server

`Wigbi::clientRootPath` - used by any path that it to be used by the client

This should be sufficient, right? Wrong! As I added URL rewrite rules, I noticed
that themes and JavaScript stopped working. The problem turned out to be:

* The *root path* is the path of the *executing *page (index.php), which is an empty string.
* When in PHP, the root path is *correct*, since the physical file is executed.
* When in the browser, the root path is *incorrect*, since the page thinks it has an address that it in fact has not. PHP does not know about the resulting url, only the expected. The root path should be ../ but is instead blank.

Any ideas? How can I handle this?


