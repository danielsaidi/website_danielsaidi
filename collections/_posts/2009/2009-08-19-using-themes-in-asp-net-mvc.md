---
title: Using themes in ASP.NET MVC
date:  2009-08-19 05:38:00 +0100
tags:  archive
icon:  dotnet
---

I have finally started creating my first web site with ASP.NET MVC. After looking
through the nice start examples, I noticed that .css files were manually included
in the master page. Let's have a look at how to use ASP.NET themes instead.


## Create a themes

To create a custom theme, simply do the following:

* Right-click on your web project and choose `Add / Add ASP.NET Folder / Theme`.
* This will create a folder called `App_Themes` in the project root.
* Create a theme-specific folder within App_Themes, e.g. "Default".
* Add `.skin` or `.css` files to the folder - they will be included automatically.

The theme can then be applied in two ways:

* As the default theme - add `Theme="Default"` to the `pages` tag in `web.config`.
* As a page-specific theme - add `Theme="Default"` to the `Page` tag in the themed `.aspx` files.

The theme will now be automatically applied, which means that skin and style files
will be loaded and applied to the page based on the applied theme.

I prefer the default theme approach, and apply themes only to the pages that are
to use another theme. This makes it easier to switch the design of an entire app
by changing a single config parameter.


## Create a theme of the template css files

To convert the template application's css files to a theme, I created a Default
theme and moved `Content/Site.css` to the Default theme folder. 

I then modified the web.config file to use the theme and removed the manually
applied .css file from the master page.

When I then tried to run the page, it crashed with this info:

`Using themed css files requires a header control on the page. (e.g. <head runat="server" />).`

It turned out that the `Default.aspx` file in the project root was blank and only
used to redirect the user. To make the application use the theme, I simply added
this dummy code to the page:

```html
<html>
 <head runat="server"></head>
</html>
```

This will never be displayed and doesn't disturb the app in any way, but makes
the default theme work.