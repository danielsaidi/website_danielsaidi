---
title:	"onSelectRow fix for jqGrid"
date:	2010-10-07 12:00:00 +0100
tags: 	javascript jquery web
---


I love the [jqGrid jQuery plugin](http://www.trirand.com/blog/). If you have not
tried it yet, I really think you should.

However, when you look at the online demos that describe how to edit a grid row,
you may discover that the `onSelectRow` method that is used in the demos doesn't
work perfect. After editing a row, you must select a new one before you can edit
the first one again.

To fix this, I replaced the original onSelectRow method:

	onSelectRow: function(id) {
	   if (id && id!==lastsel) {
	      jQuery('#rowed3').jqGrid('restoreRow',lastsel)
	      jQuery('#rowed3').jqGrid('editRow',id,true);
	      lastsel=id;
	   }
	}

...with this one:

	onSelectRow: function(id) {
	   if (id) {
	      if (id !== lastsel) {
	         articleGrid.jqGrid('restoreRow', lastsel);
	         articleGrid.jqGrid('editRow', id, true);
	         lastsel = id;
	      } else {
	         articleGrid.jqGrid('restoreRow', lastsel);
	         lastsel = "";
	      }
	   }
	}

Now, the grid behaves a lot more like I want it to. 

Hope it helps!