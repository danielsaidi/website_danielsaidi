---
title: Animation disables font in Internet Explorer
date:  2010-09-22 12:00:00 +0100
tags:  jquery web
---

Yesterday, I built a simple demo page where I demonstrate how easily you can get
[fonts.com Web Fonts](https://www.fonts.com/web-fonts) to work.

However, as I animated some of the elements on the page, I noticed that Internet
Explorer started disabling the fonts, falling back to the original fonts.

Turns out that this button code:

```html
<button onclick="$('.comic').animate({fontSize: '3em', });">Click me!</button>
```

was too much for Internet Explorer to handle. It should be:

```html
<button onclick="$('.comic').animate({fontSize: '3em' });">Click me!</button>
```

This fixes the font disabling bug. Thanks for not handling any exceptions, IE!