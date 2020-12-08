---
title: Make div container adjust to floating children elements
date:  2010-08-06 12:00:00 +0100
tags:  css web
---

Today, my collegue Johan Driessen showed me a CSS fix for a problem that happens
when a div container has nested, floating divs. This causes the div container to
not resize according to the size of its nested elements, as such:

![Default div behavior](/assets/blog/2010-08-06-1.png "Default div behavior")

As you see, the div doesn't adjust its size according to the nested elements. It
can be fixed, though, by this nice little CSS class:

```css
.fc:after{content:".";clear:both;display:block;visibility:hidden;height:0;}
* html .fc{height:1px;}
```

Just add the `fc` css class to the div container element, and the result will be
as such:

![Default div behavior](/assets/blog/2010-08-06-2.png "Default div behavior")