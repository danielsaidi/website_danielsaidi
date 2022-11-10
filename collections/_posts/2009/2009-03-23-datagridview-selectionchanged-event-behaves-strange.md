---
title: DataGridView SelectionChanged event behaves strange
date:  2009-03-23 08:56:00 +0100
tags:  .net c#
icon:  dotnet
---


In this post, we'll look at how the `DataGridView` `SelectionChanged` event can
behave strange and how to fix it if it does.

In a project, I use a `DataGridView` to bind data to a grid view. I then listen
for its `SelectionChanged` event and configure other controls according to the
data in the grid. For instance, I may disable a move down button if the grid has less
than two items or if the selected row is the last one.

However, the `SelectionChanged` event behaves strange. It fires several times when
data is being bound and when the control is displayed. The last time it fires, the
`RowCount` property of the grid is **1**, no matter how many rows it contains. This
makes my enable/disable functionality fail.

This behavior can be recreated like:

* Add a DataGridView to a form.
* Bind its `SelectionChanged` event and set a local integer to its `RowCount`.
* Add a breakpoint to the `SelectionChanged` event, so it can be inspected.
* In the form constructor, create a `List<String>` with several strings and bind it to the grid.
* Run the application.

As you probably will see, the `SelectionChanged` event fires several times. The last
time it fires, it's said to contain 1 row, although it has several. Furthermore, the
`SelectionChanged` event also doesn't fire when the list goes from containing no items
to one item, nor from containing one item to no items.

For the data binding, I found the `DataSourceChanged` event to behave much more stable.
It fires every time I modify the data source and fires correctly. For the move up/down
functionality, I listen for the `CurrentCellChanged` event.

After some further issues with `SelectionChanged`, I found that it also applies to the
`CurrentCell` property as well. If the control has not yet been displayed, this property
will be null.

Therefore, when you need to update the GUI according to `DataGridView` events, follow
this pattern:

1. Create a function that enables and disabled your GUI controls correctly.
2. Create an event handler that executes this function.
3. Bind the event handler to the following events of the grid view:

* `CurrentCellChanged`
* `DataSourceChanged`
* `VisibleChanged`

This will hopefully make your GUI behave the way you want it to.