---
title:	"Bundle JavaScript and CSS files with full file paths"
date:	2010-09-19 12:00:00 +0100
categories: web
tags: 	javascript css
---


I have looked at the great PHP-based CSS and JavaScript bundling approach that is
presented at [rakaz.nl](http://rakaz.nl/2006/12/make-your-pages-load-faster-by-combining-and-compressing-javascript-and-css-files.html).
The suggested approach in that blog post works great, but has a small drawback.

The downloaded combine.php must be adjusted and all combined files must exist in
the same folder.

This is not a big problem, I know, but since I already have a structure in which
CSS and JavaScript files may exist in a sub folder, I want to be able to provide
the bundle URL with full paths to the files I want to bundle.

Since the original solution was so slick, this was quite easy to achieve. I will
not upload my solution, since I want you to grab the original code from rakaz.nl
so that it receives the visitors it deserves 🙂

Let’s get started:

- In your project root, create a folder called `bundle` (or whatever you want)
- In the bundle folder, create an `.htaccess` file and add the following code:

	RewriteEngine On
    RewriteBase /
    RewriteRule ^css/(.*\.css) /combine.php?type=css&files=$1
    RewriteRule ^javascript/(.*\.js) /combine.php?type=javascript&files=$1

- In the bundle folder, create a `combine.php` file and add this content to it

Now you’re practically at where the original approach told you to adjust the PHP
code a bit. Instead, do this:

In `combine.php`, replace

	$path = realpath($base . '/' . $element);

with:

	$path = "../" . $element

The code has to be replaced at two places in the PHP file, but all in all that's
it! The only thing you have to consider now, is to use application root relative
paths when calling the bundle URL.

For instance, I have my main js folder in the application root folder content/js.
Let’s say that I have the files a.js and b.js. The bundle URL would then be:

	bundle/javascript/content/js/a.js,content/js/b.js

To bundle CSS files, the corresponding code would look like:

	bundle/css/content/css/a.css,content/css/b.css

The newly added code will add a ../ to each link, before attempting to parse the
file, which will still work great since the combine.php file is placed one level
down from the application root, to which the links have to be relative, remember?

The great thing now is that I can bundle any JavaScript and CSS files (they still
have to be bundled separately) regardless of their physical location, as long as
they exist anywhere within the application root folder.

Another nice thing is that the .htaccess file is placed inside the bundle folder.
This means that you can just copy the bundle folder, if you would like to use it
in another project.

Hope this helps!