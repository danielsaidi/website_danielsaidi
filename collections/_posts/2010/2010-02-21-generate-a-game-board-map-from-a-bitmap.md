---
title: Generate a game board map from a bitmap
date:  2010-02-21 12:00:00 +0100
tags:  archive
image: /assets/blog/2010/02-21-1.png
---

I'm currently developing an adventure board game in XNA, where players can play
missions that take place on a board that is made up of square tiles. It's a lot
like the amazing, old board game *Hero Quest*. In this post, I'll describe how
my custom-made game engine generates a board from a bitmap.

The engine defines a set of item types that can be placed on the board, like
characters, doors, furnitures etc. as well as how each item can interact with the
board. For instance, characters can move around, while static objects can not.
Doors let characters pass and can be seen through, while rubble blocks vision and
passage. Book shelves and chests can contain hidden treasures and blocks passages,
but don't block vision. And so on, and so on.

I will now describe how the engine use bitmap files to let you quickly create a
mission map. But first, some relevant side information.


## Board movement

Players and computer controlled enemies can move sideways, but not diagonally:

![Board movement example 1](/assets/blog/2010/02-21-1.png)

Factors that limit whether or not a game piece can move from one tile to another
(tile A to tile B) are:

* Tile B doesn't exist (the piece would move outside of the board boundaries).
* Tile B is marked as a `None` or a `Nonwalkable` tile (see below).
* Tile B belongs to another room and is separated from tile A by a wall.
* Tile B is occupied by another piece or furniture (another piece can not stop here).
* Tile B is occupied by a player or enemy (players and enemies can not walk past eachother).

All these rules are then handled by a bunch of tile-related functions, that take
a player or enemy and decide whether or not the character can move to a tile.


## Tile types

I have chosen to limit myself to three different tile types:

* `None` - the tile has no properties and is ignored.
* `NonWalkable` - the tile can not be stepped on.
* `Walkable` - the tile can be stepped and stopped on.

`None` is really not needed, but I decided to keep it in order to separate tiles
that are not part of the game board from tiles that just can not be entered.


## Mission data

In the game, players can play several missions that all takes place on the same
base board, but that use different maps. To make it easy to create a large number
of missions, I the engine works like this:

* The mission map is specified in a bitmap file.
* The mission data is specified in an XML file.

When a mission is loaded, the XML file is parsed into a `Mission` object. The file
refers to the bitmap, which is parsed into a mission board map.

The board map specifies the board tiles, which determines the look of the board,
while the XML file specifies everything else, like the mission name, the mission
targets, board items etc.


## Bitmap parsing overview

Consider the following example bitmap:

![Bitmap Example](/assets/blog/2010/02-21-2.png)

When the mission is initialized, it goes through the following steps:

* Parse the XML file.
* Parse the bitmap into a mission board.
* Initialize each tile using the corresponding color in the bitmap.
* Divide the board tiles into rooms.

I have chosen to handle bitmap colors as such:

* Black corresponds to the `None` tile type.
* The light grey at position (0,2) and (0,3) – #c3c3c3 – corresponds to a `Nonwalkable` tile.
* All other colors are (for now) regarded to be `Walkable` tiles.

The colors will later be used to determine which image to use for each tile:

* Black tiles are not handled at all (they are `None`, remember?) and have no image.
* All other colors are converted into hex code (#ffffff instead of White).
* If any textures have the hex code in their name (e.g. ffffff_1.png) a random one is selected.
* If no corresponding image exists, the color is used to tint a random ffffff_x.png image.

When all tiles have been initialized with a type, an image etc. they are then 
divided into rooms. Adjacent tiles with the same color are considered to belong
to the same room. When the board is later drawn, the game draws solid, non-passable
walls between the rooms.

The process is really straightforward. I have chosen not to include code for the
`Board` and `Tile` classes that are mentioned below, since the classes are quite
complex and I just want to describe the brief concept of how I parse the bitmap.


## Step 1: Initialize the mission board

In my model, the `Board` class has an `Initialize` function, that takes an image
as a parameter:

	public void Initialize(Texture2D image)
	{
	    InitializeTiles(image);
	    InitializeRooms();
	}

The function above consists of two operations. First, all tiles on the board are
fully initialized, then the board is divided into rooms.


## Step 2: Initialize each tile

The `InitializeTiles` function below converts the texture into a color array and
applies each color to the corresponding tile:

```csharp
private void InitializeTiles(Texture2D image)
{
    //Convert image to colors
    Color[] colors = new Color[image.Width * image.Height];
    image.GetData<Color>(colors);

    //Initialize the board tile matrix
    Tiles = new Tile[image.Width, image.Height];

    //Initialize each tile in the grid
    for (int y = 0; y < Tiles.GetLength(1); y++) 
    {
        for (int x = 0; x < Tiles.GetLength(0); x++) 
        {
            Tiles[x, y] = new Tile(colors[x + y * image.Width]);
        }
    }
}
```

In this example, I have reduced the number of parameters in the `Tile` constructor,
to make the code easier to read.

In the game, the `Tile` constructor uses the provided color to set the `Image`, 
`Type` and `Tint` of the tile. However, since this is specific to my game, I decided
to leave the constructor out of this post.


## Step 3: Divide the board tiles into rooms

Once the board has been given a grid of tiles, the engine divides tiles into rooms,
using three functions.

`InitializeRooms` makes sure that all tiles are handled at least once:

```csharp
private void InitializeRooms()
{
    for (int y = 0; y < Tiles.GetLength(1); y++)
    {
        for (int x = 0; x < Tiles.GetLength(0); x++)
        {
            InitializeRooms(Tiles[x, y]);
        }
    }
}
```

A second `InitializeRooms` function takes a tile parameter and ensures that the
tile fetches the room index from already initialized adjacent tiles that belong
to the same room (if any), sets a new room index if needed, then spreads the room
index to non-initialized adjacent tiles that belong to the same room.

For now, the function uses two sub-functions that cleans up the code a bit:

```csharp
private void InitializeRooms(Tile tile)
{
    //Abort if no tile or if already checked
    if (tile == null || tile.RoomIndex.HasValue) { return; }

    //Set negative room index if no tile or non walkable
    if (tile.TileType == TileType.None || tile.TileType == TileType.Unwalkable)
    {
        tile.RoomIndex = -1;
    }
    
    //Fetch room number from similar siblings
    InitializeRooms_Fetch(tile, tile.TopSibling);
    InitializeRooms_Fetch(tile, tile.LeftSibling);
    InitializeRooms_Fetch(tile, tile.RightSibling);
    InitializeRooms_Fetch(tile, tile.BottomSibling);

    //Set room number if none has been set
    if (!tile.RoomIndex.HasValue)
    {
        tile.RoomIndex = roomIndex++;
    }

    //Spread room number to similar siblings
    InitializeRooms_Spread(tile, tile.TopSibling);
    InitializeRooms_Spread(tile, tile.LeftSibling);
    InitializeRooms_Spread(tile, tile.RightSibling);
    InitializeRooms_Spread(tile, tile.BottomSibling);
}
```

```csharp
private void InitializeRooms_Fetch(Tile tile, Tile sibling)
{
    //Abort if either tile is null
    if (tile == null || sibling == null) { return; }

    //Fetch index if the tiles have the same color
    if (sibling.RoomIndex.HasValue && tile.Color == sibling.Color)
    {
        tile.RoomIndex = sibling.RoomIndex;
    }
}
```

```csharp
private void InitializeRooms_Spread(Tile tile, Tile sibling)
{
    //Abort if either tile is null
    if (tile == null || sibling == null) { return; }

    //Spread by initializing the sibling
    if (tile.RoomIndex.HasValue && tile.Color == sibling.Color)
    {
        InitializeRooms(sibling);
    }
}
```

## Example

The steps above are all you need to parse a bitmap into an playable map.

In the game that generated the final result below, I have 12 “ffffff” images and
4 “c3c3c3” images. I have no image for the red color or the dark grey rooms. The
result of this is that they use the same images as the hallway, and apply a tint
color on top of that.

In the image below, I display the room index as well. As you can see, the `None`
and `NonWalkable` tiles are given a room index of -1, while all other tiles are
separated into room 1-4.

![Mission map](/assets/blog/2010/02-21-3.png)

This approach makes it really easy to create a different map for another mission
or edit the map above.

I will return to the XML file in another post, as well as how to draw walls
between rooms, how to use different colors/images for the same rooms etc.