---
title:	"A* implementation for XNA"
date:	2010-02-16 12:00:00 +0100
tags: 	xna a* game-dev
---


I have recently been playing around with the XNA framework to get a grasp at how
to develop games for the XBOX 360. It is great fun, but quite different from the
kind of development that I usually do.

I now have a working game engine stub, with an object model that grows and grows
and feels quite nice. I can generate missions from image/text file tuples, where
the image determines the mission's board and the text file describes the mission,
items on the board, goals etc.

This setup makes it easy to quickly develop a large amount of missions, that can
be divided into multiple campaigns. By making the base model solid, new missions
will be content management than programming.

When my game engine imports a mission map image, it generates a grid of walkable
and unwalkable tiles, of which the board consists. I will write more about these
tiles and how to handle them in a future blog post.

The next step is now to be able to find the shortest patj from a tile to another.
I found this great tutorial and will use it to implement the A* algorithm, which
hopefully will take care of this for me:

[http://dotnetperls.com/pathfinding](http://dotnetperls.com/pathfinding)