---
title: Identifying the project root in PHP
date:  2009-05-15 10:20:00 +0100
tags:  archive
---

I currently have problems with identifying the project root in a PHP project. The
problem applies to PHP, but is general and applies to other
languages and environments as well.


## Problem description

I will use my PHP-based web application framework, Wigbi, as a real-world example.

Wigbi has many classes that can be used anywhere in the project folder hierarchy, which is why it's important to identify the project root.

Wigbi has two ways to do this - the two static `Wigbi::rootPath` and `Wigbi::clientRootPath` properties, which identify the root in two different ways.

Before I explain why we need two properties, let's discuss why absolute and relative paths are not good alternatives to handle this situation.


## Absolute and relative paths

*Absolute paths* use the server root (e.g. /img/pixel.gif) as the starting point. Absolute paths shouldn't be used, since the absolute root may differ between various environments.

Relative paths (e.g. ../img/pixel.gif) are also common, but mustn't be used by files that may be used in various parts of the project folder hierarchy. For instance, Wigbi can be included by both *index.php* and *music/index.php*, which makes relative paths unusable.

The same applies to ASP.NET User Controls, which may be added to pages anywhere in the folder hierarchy.

You may think that relative paths are OK for web pages, since pages have a fixed position in the project folder (e.g. news/index.php). This is also *not true*, since URL rewriting may move pages around, which will cause relative paths to fail. You may also want to be able to move a file without having to change its content.

In short, absolute and relative paths are problematic. Only use them if you fully understand how they are to be used, and the drawbacks of each approach.


## Identifying the project root

ASP.NET has a great way of identifying the project root: the ~ variable. `~` which can be added as a prefix to any path that is to be used by the client. 

ASP.NET will convert `~` to the application root path when the page is parsed by the server. This means that you can always use `~` and be sure that it will resolve to the project root.

However, this only applies to client related paths, not server files. Wigbi therefore has two properties for identifying the project root:  `Wigbi::rootPath` and `Wigbi::clientRootPath`. 

Let's see how these paths can be used.


## Wigbi::rootPath

`Wigbi::rootPath` identifies the project root for the executing server file. For instance, when Wigbi is started and loads the current language file, it uses this property to locate the file, starting from the path to the project root from where the code executes.

If you write a class that is to work with files, `Wigbi::rootPath` will work, no
matter where in the folder hierarchy you are.


## Wigbi::clientRootPath

There is also a `Wigbi::clientRootPath` property that is set whenever an AJAX operation is executed. It makes path that is returned to the client to be relative to the page.

This works, but I would prefer to have a single variable instead of these two. I also I didn't predict some problems that I discovered later.


## URL rewriting = PROBLEM!!!

So, we have these properties:

* `Wigbi::rootPath` - used by PHP files to work with files on the server.
* `Wigbi::clientRootPath` - used by any path that it to be used by the client.

This should be sufficient, right? Wrong! As I added URL rewrite rules, I noticed
that themes and JavaScript code stopped working.

The problem turned out to be:

* The *root path* is the path of the *executing* page (index.php), which is an empty string.
* In PHP, the root path is *correct*, since the physical file is executed.
* In the browser, the root path is *incorrect*, since it has an address that it hasn't.
* PHP doesn't know about the resulting url, only the expected. 
* As such, the root path should be `../`, but becomes blank.

Any ideas how to handle this?


