---
title: jQuery, MooTools, object inheritance and JSON
date:  2010-06-10 12:00:00 +0100
tags:  javascript jquery
---

I use both MooTools and jQuery in various projects. As I now have started moving
more towards jQuery, I only use MooTools for its nice type and json capabilities.
Let's look at how to implement this in jQuery.


## Class inheritance

As I have decided to drop MooTools, I will no longer have access to the MooTools
`Object` class, where inheritance was implemented like this:

```
var InheritingClass = Class({
	Extends: BaseClass,
	initialize: function() { this.parent("optional parameters"); }
});
```

I will instead use the jQuery `extend` function to create a similar inheritance model:

```
function InheritingClass() {
	$.extend(this, new BaseClass("optional parameters"));
};
```

The MooTools example requires knowledge about the object model, while the jQuery
example only requires developers to know about how to use the extend method. The
jQuery example feels better.


## JSON encoding/decoding

MooTools features a nice JSON parser and serializer, which I will not be able to
use anymore. jQuery contains (at least) two functions that can be used to handle
JSON - `parseJSON` and `serializeArray`. However, the latter only works with DOM
elements, so it is not really what I was after.

After some reading, it seems like the official JSON JavaScript implementation is
the best around, while a jQuery plugin that I found had quite an extensive issue
list. So, although it makes me depend on yet another 3rd part component, I chose
this nice little class, which I have used before. 

MooTools is hereby completely replaced with jQuery (and the small JSON class).