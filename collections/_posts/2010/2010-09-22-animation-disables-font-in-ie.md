---
title: Animation disables font in Internet Explorer
date:  2010-09-22 12:00:00 +0100
tags:  jquery web
icon:  javascript
---

Yesterday, I built a simple demo page to demonstrate how easy it is to get
[fonts.com Web Fonts](https://www.fonts.com/web-fonts) up and running. However, 
as I did, I noticed that Internet Explorer disables custom fonts during animations.

As the animation starts, Internet Explorer started disabling the fonts, falling
back to the original fonts.

However, it turns out that it wasn't a limitation in Internet Explorer, but rather
a bug in the animation code.

Turns out that this button code's trailing comma made Internet Explorer fail:

```html
<button onclick="$('.comic').animate({fontSize: '3em', });">Click me!</button>
```

Removing the comma fixes the font disabling bug:

```html
<button onclick="$('.comic').animate({fontSize: '3em' });">Click me!</button>
```

Thanks for not any exceptions at all, IE!