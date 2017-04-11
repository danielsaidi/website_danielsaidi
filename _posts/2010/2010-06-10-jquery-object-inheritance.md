---
title:	"jQuery, MooTools, object inheritance and JSON"
date:	2010-06-10 12:00:00 +0100
categories: web
tags: 	javascript jquery json mootools
---


I have previously used both MooTools and jQuery as embedded toolkits in projects
of mine. I have used MooTools a lot longer than jQuery, but as I have moved more
and more towards jQuery, I now only use MooTools for its nice type capabilities.


## Class inheritance

However, I have now decided to drop MooTools altogether and focus on exclusively
using jQuery. Since this means that I will no longer have access to the MooTools
`Object` class, I instead use the jQuery `extend` function to create "inheriting"
classes.

In jQuery, I handle this as such:

	function InheritingClass() {
		$.extend(this, new BaseClass("optional parameters"));
	};

while the MooTools Object class required a syntax like this:

	var InheritingClass = Class({
		Extends: BaseClass,
		initialize: function() { this.parent("optional parameters"); }
	});

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