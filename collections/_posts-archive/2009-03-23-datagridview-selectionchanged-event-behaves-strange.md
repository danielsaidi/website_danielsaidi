---
title: DataGridView SelectionChanged event behaves strange
date:  2009-03-23 08:56:00 +0100
tags:  archive
---


In this post, let's look at how to fix the `DataGridView` `SelectionChanged` event if it starts to behave strange.

I use a `DataGridView` to bind data to a grid view, then listen for its `SelectionChanged` event and configure other controls according to the data in the grid. 

For instance, I may disable a move down button if the grid has less than two items or if the selected row is the last one.

However, the `SelectionChanged` event behaves strange. It fires several times when data is bound and when the control is displayed. The last time it fires, the `RowCount` property is **1**, no matter how many rows it contains. This makes my enable/disable functionality fail.

This behavior can be recreated like:

* Add a DataGridView to a form.
* Bind its `SelectionChanged` event and set a local integer to its `RowCount`.
* Add a breakpoint to the `SelectionChanged` event, so it can be inspected.
* In the form constructor, create a `List<String>` with several strings.
* Bind the list instance to the grid.
* Run the application.

The `SelectionChanged` event will fire several times. The last time it triggers, it's claims that it contains 1 row, although it contains several.

Furthermore, `SelectionChanged` doesn't fire when the list goes from containing no items to one item, nor from containing one item to no items.

For data binding, I `DataSourceChanged` to behave better. It fires every time I modify the data source and fires correctly. For move up/down, I listen for `CurrentCellChanged`.

After some more issues with `SelectionChanged`, I found that it also behaves incorrectly for the `CurrentCell` property as well. If the control has not yet been displayed, it will be null.

You should therefore do this if you must update the GUI according to `DataGridView` events:

1. Create a function that enables and disabled your GUI controls correctly.
2. Create an event handler that executes this function.
3. Bind the event handler to the following events of the grid view:

* `CurrentCellChanged`
* `DataSourceChanged`
* `VisibleChanged`

This will hopefully make your GUI behave the way you want it to.