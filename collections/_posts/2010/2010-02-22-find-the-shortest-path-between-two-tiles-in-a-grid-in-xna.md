---
title: Find the shortest path between two tiles in a grid in XNA
date:  2010-02-22 12:00:00 +0100
tags:  games c# .net
image: /assets/blog/2010/02-22-1.png
---

I am currently playing around with developing an adventure board game in XNA, in
which players can play various missions that take place on a tile-based board. I
am a full-blown XNA newbie who takes this approach to learn the platform, but it
is a lot of fun.


## About the game

In the game, players have a number of steps to move in each round, and should be
able to select which tile they want to move to. The game should then suggest the
shortest possible path to that tile, and mark tiles that are too far away in red.

The path finding algorithm will also be used by the game, to move enemies on the
board. These enemies are controlled by the computer and will thus have some form
of primitive AI, to help them decide how and where to move.

To improve the illusion of enemy intelligence, the algorithm should also be able
to select a random path if multiple options exist, which will give the enemies a
random, unpredictable (well, sort of) behavior.

Before I describe the method, let’s recap a bit.


## Board movement

The players and computer controlled enemies can move horizontally and vertically:

![Board movement example 1](/assets/blog/2010/02-22-1.png)

Factors that limit whether or not a game piece can move from one tile to another
(tile A to tile B) are (so far):

* Tile B does not exist (the piece would move outside of the board boundaries)
* Tile B is marked as a None or a Nonwalkable tile (see below)
* Tile B belongs to another room and is separated from tile A by a wall (covered in the previous post)
* Tile B is occupied by another piece or furniture (another piece can not stop here)
* Tile B is occupied by a player or enemy (players and enemies can not walk past eachother)

All these rules are then handled by a bunch of tile-related functions, that take
a player or enemy and decide whether or not the character can move to a tile.


## Tile types

I have chosen to limit myself to three different tile types:

* `None` (tile has no properties and is ignored)
* `NonWalkable`
* `Walkable`

`None` is really not needed, but I decided to keep it in order to separate tiles
that are not part of the game board from tiles that just can not be entered.


## Path finding overview

Consider the following map, where a player stands on the green tile and wants to
move to the red tile:

![Board movement example 2](/assets/blog/2010/02-22-2.png)

In the example above, multiple “shortest paths” exist. As we will see later, the
method I use will find a random path every time. It has the following main steps:

* Beginning at the start tile, set its “path length” to zero.
* Recursively handle each sibling, according to the following:
* If the sibling has not been handled yet, set its path length
* If the sibling has already been handled, do it again if the new path length is shorter
* When no tile can be improved, start at the end tile and find the shortest path to the start tile

I call these two main processes `spreading` (a tile spreads its path's length to
its siblings) and `tracing` (trace the shortest path found while spreading).


## Step 1: Spreading

In my game, the path-finding operation is started with this `Board` function :

```csharp
//Placeholder for the calculated path (not thread safe :)
int[,] pathLengths;

public List<Tile> FindPath(Tile startTile, Tile endTile)
{
	//Abort if start or end tile is null
	if (startTile == null || endTile == null)
	{
		return new List<Tile>();
	}

	//Abort if the end tile is non-stoppable
	if (!endTile.IsStoppable)
	{
		return new List<Tile>();
	}

	//Initialize the path length array
	pathLengths = new int[Tiles.GetLength(0), Tiles.GetLength(1)];
	for (int y = 0; y < pathLengths.GetLength(1); y++)
	{
		for (int x = 0; x < pathLengths.GetLength(0); x++)
	    {
	    	pathLengths[x, y] = int.MaxValue;
	    }
	}
	 

	//Begin at the start tile
	pathLengths[startTile.BoardPosition.X, startTile.BoardPosition.Y] = 0;
	FindPath_Spread(startTile);

	//Once done, backtrack from the end tile
	List<Tile> result = FindPath_Trace(endTile);

	//Only return the path if it contains the start tile
	if (result.Contains(startTile)) {
	 	return result;
	}

	return new List<Tile>();
}
```

This function corresponds to the first part of the numbered list above. We don't
proceed if any tile is null or if the end tile can not be stopped at.

We then initialize a placeholder array with max length values, then run a spread
operation from the start tile. Once the spread is done, we trace the path.

As you can see, this function only returns a path if ot contains the start tile.
If the trace operation can not reach the start tile, no path should be returned.

The spread operation is a recursive one that eventually will handle each tile at
least once. It consists of two functions:

```csharp
private void FindPath_Spread(Tile tile)
{
	FindPath_Spread(tile, tile.TopSibling);
	FindPath_Spread(tile, tile.LeftSibling);
	FindPath_Spread(tile, tile.RightSibling);
	FindPath_Spread(tile, tile.BottomSibling);
}

private void FindPath_Spread(Tile tile, Tile target)
{
	//Abort if any tile is null
	if (tile == null || target == null) {
		return;
	}

	//Abort if no movement is allowed
	if (!tile.CanMoveTo(target)) {	
		return;
	}

	//Get current path lengths
	int tileLength = FindPath_GetPathLength(tile);
	int targetLength = FindPath_GetPathLength(target);

	//Use length if it improves target
	if (tileLength + 1 < targetLength)
	{
		pathLengths[target.BoardPosition.X, target.BoardPosition.Y] = tileLength + 1;
		FindPath_Spread(target);
	}
}
```

We initialize the spread operation at the start tile, which has a path length of
zero. The path then spreads out to the tile's siblings, but is only handled when
the sibling’s path length would be improved. Thus, this recursive operation will
stop once all tiles are “as good as they can be”.


## Step 2: Tracing

Once the spread operation is done, the recursive trace operation will be started.
The trace operation will start at the end tile and find the shortest way back to
the start tile. If the start tile can not be reached, no path exists between the
two tiles. If so, the operation will return an empty list.

The trace operation consists of a single function:

```csharp
private List<Tile> FindPath_Trace(Tile tile)
{
	//Find the sibling paths
	int tileLength = FindPath_GetPathLength(tile);
	int topLength = FindPath_GetPathLength(tile.TopSibling);
	int leftLength = FindPath_GetPathLength(tile.LeftSibling);
	int rightLength = FindPath_GetPathLength(tile.RightSibling);
	int bottomLength = FindPath_GetPathLength(tile.BottomSibling);

	//Calculate the lowest path length
	int lowestLength =
		Math.Min(tileLength,
		Math.Min(topLength,
		Math.Min(leftLength,
		Math.Min(rightLength, bottomLength))));

	//Add each possible path
	List<Tile> possiblePaths = new List<Tile>();
	if (topLength == lowestLength){
		possiblePaths.Add(tile.TopSibling);
	}
	if (leftLength == lowestLength){
		possiblePaths.Add(tile.LeftSibling);
	}
	if (rightLength == lowestLength) {
		possiblePaths.Add(tile.RightSibling);
	}
	if (bottomLength == lowestLength) {
		possiblePaths.Add(tile.BottomSibling);
	}

	//Continue through a random possible path
	List<Tile> result = new List<Tile>();
	if (possiblePaths.Count() > 0) {
		result = FindPath_Trace(possiblePaths[RandomHelper.GetInt32(0, possiblePaths.Count())]);
	}

	//Add the tile itself, then return
	result.Add(tile);
	return result;
}
```

`FindPath_GetPathLength` is a small function that I added to avoid duplicate code:

```csharp
private int FindPath_GetPathLength(Tile tile)
{
	if (tile == null){
		return int.MaxValue;
	}
	return pathLengths[tile.BoardPosition.X, tile.BoardPosition.Y];
}
```


## Example

Using the map at the beginning of this post, my game engine will first parse the
map into a game board, as is described in the previous blog post.

This is how my game (for now) displays the tiles. For now, the walls are missing:

![Rendered output](/assets/blog/2010/02-22-3.png)

In the image above, all tiles are walkable, to increase the number of “shortest” 
paths. However, it is not possible to walk to a dark tile from a light one. This
means that the path must be concentrated to light grey tiles only. 

In this image, the green and red tile are just highlighted display the start and
end tile. In the game, they are also light grey!

When I run my game (well, game-to-be), I auto-generate a path between this green
and red tile. Below are displayed three example of resulting path suggestions:


![Three different paths](/assets/blog/2010/02-22-4.png)

The path finding operation is fast and can handle large board games. However, it
would not be suitable for more complex games, where the world is not tile-based.




