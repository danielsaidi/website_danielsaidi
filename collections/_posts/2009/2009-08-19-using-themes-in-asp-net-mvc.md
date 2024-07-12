---
title: Using themes in ASP.NET MVC
date:  2009-08-19 05:38:00 +0100
tags:  archive
icon:  dotnet
---

I'm building a web site with ASP.NET MVC. After looking at the nice examples, I noticed that CSS files were manually included in the master page. Let's use themes instead.


## Create a themes

To create a custom theme, simply do the following:

* Right-click on your web project and choose `Add / Add ASP.NET Folder / Theme`.
* This will create a folder called `App_Themes` in the project root.
* Create a theme-specific folder within App_Themes, e.g. "Default".
* Add `.skin` or `.css` files to the folder - they will be included automatically.

The theme can then be applied in two ways:

* As the default theme - add `Theme="Default"` to the `pages` tag in `web.config`.
* As a page-specific theme - add `Theme="Default"` to the `Page` tag in the `.aspx` files.

The theme will now be automatically applied, which means that skin and style files will be loaded and applied to the page based on the applied theme.

I prefer the default approach, and only apply explicit themes to pages that use a different theme. This makes it easier to switch the entire design with a single config parameter.


## Create a theme of the template css files

To convert the demo app's css files to a theme, I created a Default theme and then moved the `Content/Site.css` file to the Default theme folder. 

I then modified `web.config` to use the theme and removed the manually applied `.css` file from the master page.

When I then tried to run the page, it crashed with this error:

`Using themed css files requires a header control on the page. (e.g. <head runat="server" />).`

Turns out that the `Default.aspx` file in the project root was blank and only used to redirect the user.

To make the application use the theme, I simply added this dummy code to the page:

```html
<html>
 <head runat="server"></head>
</html>
```

This is not displayed and doesn't affect the app in any way, but applies the default theme.