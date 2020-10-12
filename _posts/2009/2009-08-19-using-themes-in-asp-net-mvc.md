---
title: "Using themes in ASP.NET MVC"
date:  2009-08-19 05:38:00 +0100
tags:  .net web css
---

I have finally gotten around to create my first web site with ASP.NET MVC. After
looking through the nice start examples, I noticed that .css files were manually
included in the master page. 

Since I prefer to use ASP.NET themes, I decided to adjust the application a bit.


## Create custom themes

To create a custom theme, simply do the following:

* Right-click on your web project and choose "Add / Add ASP.NET Folder / Theme"
* This will create a folder called **App_Themes** in the project root.
* Create a theme-specific folder within App_Themes, e.g. "Default".
* Add .skin or .css files to the folder - these files will be included automatically

The theme can then be applied in two ways:

* As the default theme (used by all project pages) - add Theme="Default" to the
"pages" tag in web.config.
* As a page-specific theme (applied to a page) - add Theme="Default" to the Page
tag in each .aspx file that should use the theme.

The theme will now be automatically applied, which means that all skin and style
files will be loaded automatically and applied to the page. I prefer the default
theme approach, and apply themes only to the pages that are to use another one.
This makes it easier to switch the design of an entire application by changing a
single config parameter.


## Create a theme of the template css files

To convert the template application's css files to a theme, I created a Default
theme and moved Content/Site.css to the Default theme folder. I then modified the
web.config file to use the theme and removed the manually applied .css file from
the master page.

However, when I tried to run the page, it crashed with this info:

`Using themed css files requires a header control on the page. (e.g. <head runat="server" />).`

Turns out that the Default.aspx file in the project root is completely blank and
only used to redirect the user. To make the application use the theme, I simply
added this dummy code to the page:

```html
<html>
 <head runat="server"></head>
</html>
```

This will never be displayed and does not disturb the app in any way, but makes
the default theme work...which at least I think is nice :)