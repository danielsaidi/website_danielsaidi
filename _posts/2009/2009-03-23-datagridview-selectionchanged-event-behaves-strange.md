---
title:  "DataGridView SelectionChanged event behaves strange"
date:   2009-03-23 08:56:00 +0100
categories: dotnet
tags:	win-forms
---


In a project where I use the handy DataGridView control I, bind a data source to
a grid view, then listen for the `SelectionChanged` event. When the event is fired,
I enable or disable other controls according to the data that is contained in the
grid. For instance, I disable a move down button if I have less than two items in
the list, or if the selected row is the last one.

However, the `SelectionChanged` event behaves strange. It fires several times when
data is being bound and when the control is displayed. The last time it is fired,
the `RowCount` property of the grid is **1**, no matter how many rows it contains.
This makes my enable/disable functionality fail.

The bug(?) can be recreated as such:

* Add a DataGridView to a form
* Bind its `SelectionChanged` event and set a local int variable to its RowCount
* Add a breakpoint to the SelectionChanged event, so it can be inspected
* In the form constructor, create a `List<String>` instance with several strings, then bind it to the grid view.
* Run the application

As you probably will see, the `SelectionChanged` event fires several times. The
last time it fires, the grid is said to contain 1 row, although it has several.

Furthermore, the `SelectionChanged` event also does not fire when the list goes
from containing none to containing one item, nor from containing one item to none.

I found the DataSourceChanged event to behave much more stable. It is fired every
time I modify the data source in any way. Also...it fires correctly. For the move
up/down functionality, I also need to listen for the `CurrentCellChanged` event.

After some further issues with the `SelectionChanged` event, I found that it also
applies to the CurrentCell property. If the control has not yet been displayed, the
property will be null.

Therefore, when you need to update the GUI according to a selection event in the
DataGridView control, just follow this pattern:

1. Create a function that enables and disabled your GUI controls correctly
2. Create an event handler that executes this function
3. Bind the event handler to the following events of the data grid view:

* `CurrentCellChanged`
* `DataSourceChanged`
* `VisibleChanged`

This will hopefully make your GUI behave the way you want it to.