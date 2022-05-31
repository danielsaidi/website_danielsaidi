---
title: Bundle JavaScript and CSS files with full file paths
date:  2010-08-19 12:00:00 +0100
tags:  php javascript css web
icon:  php

post: http://rakaz.nl/2006/12/make-your-pages-load-faster-by-combining-and-compressing-javascript-and-css-files.html
---

I have looked at the great php-based css and JavaScript bundling approach that is
presented at [rakaz.nl]({{page.post}}). The approach in that post works great, but
has a small drawback.

The drawback is that the downloaded `combine.php` must be adjusted and that all 
combined files must exist in the same folder. It's not a big problem, but since 
I already have a structure in which css and JavaScript files may exist in a sub 
folder, I want to be able to provide the bundle URL with full paths to the files 
I want to bundle.

Since the original solution was so slick, this was quite easy to achieve. This
is basically how I did it:

- In your project root, create a folder called `bundle` (or whatever you want).
- In the bundle folder, create an `.htaccess` file and add the following code:
    * RewriteEngine On
    * RewriteBase /
    * RewriteRule ^css/(.*\.css) /combine.php?type=css&files=$1
    * RewriteRule ^javascript/(.*\.js) /combine.php?type=javascript&files=$1
- In the bundle folder, create a `combine.php` file and add this content to it.

Now you’re practically at where the original approach told you to adjust the PHP
code. Instead, do this:

In `combine.php`, replace

```php
$path = realpath($base . '/' . $element);
```

with this code:

```php
$path = "../" . $element
```

The code has to be replaced at two places in the php file, but that's it! The 
only thing to consider now, is to use application root relative paths when calling
the bundle URL.

For instance, I have my main js folder in the root folder `content/js`. Let’s say
that I have the files `a.js` and `b.js` in this folder. The bundle URL would then be:

```
bundle/javascript/content/js/a.js,content/js/b.js
```

To bundle css files, the corresponding code would look like:

```
bundle/css/content/css/a.css,content/css/b.css
```

The adjusted code will add a ../ to each link before parsing the file. This will
still work great, since `combine.php` is placed one level down from the application
root, to which the links have to be relative.

Another great thing is that we can bundle any JavaScript and css files (they still
have to be bundled separately) regardless of their location, as long as they exist
anywhere within the application root folder.

Another nice thing is that the `.htaccess` file is placed inside the bundle folder,
which means that you can just copy the bundle folder to use it in another project.