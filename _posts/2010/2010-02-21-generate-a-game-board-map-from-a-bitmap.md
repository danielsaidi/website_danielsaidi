---
title:	"Generate a game board map from a bitmap"
date:	2010-02-21 12:00:00 +0100
categories: dotnet
tags: 	xna a*
---


I am currently developing an adventure board game in XNA, where players can play
missions that take place on a board that is made up of horizontal tiles.

The post will describe how a bitmap file can be used to quickly create a mission
map, but first some relevant side information.


## Board movement

The players and computer controlled enemies can move horizontally and vertically:

![Board movement example 1](/assets/img/blog/2010-02-21-1.png)

Factors that limit whether or not a game piece can move from one tile to another
(tile A to tile B) are (so far):

- Tile B does not exist (the piece would move outside of the board boundaries)
- Tile B is marked as a None or a Nonwalkable tile (see below)
- Tile B belongs to another room and is separated from tile A by a wall (covered in the previous post)
- Tile B is occupied by another piece or furniture (another piece can not stop here)
- Tile B is occupied by a player or enemy (players and enemies can not walk past eachother)

All these rules are then handled by a bunch of tile-related functions, that take
a player or enemy and decide whether or not the character can move to a tile.


## Tile types

I have chosen to limit myself to three different tile types:

- `None` (tile has no properties and is ignored)
- `NonWalkable`
- `Walkable`

`None` is really not needed, but I decided to keep it in order to separate tiles
that are not part of the game board from tiles that just can not be entered.


## Mission data

In the game, players can play one of many missions, which all takes place on the
same board, but with different maps. To make it easy to create a large number of
missions, I have chosen to define them as such:

- The mission map is represented in a bitmap file
- The mission data is specified in an XML file

When a mission is loaded, the XML file is parsed into a mission object. The file
points out the bitmap file, which is then parsed into a mission board map.

The board map specifies the board tiles, which determines the look of the board,
while the XML file specifies everything else, like the mission name, the mission
targets, board items etc.

The XML file content is the topic of a future post.


## Bitmap parsing overview

Consider the following bitmap, which is a really small example of a mission map:

![Bitmap Example](/assets/img/blog/2010-02-21-2.png)

When the mission is initialized, it goes through the following steps:

- Parse the XML file (not covered in this post)
- Parse the bitmap into a mission board
- Initialize each tile using the corresponding color in the bitmap
- Divide the board tiles into rooms

I have chosen to handle bitmap colors as such:

- Black corresponds to the `None` tile type
- The light grey at position (0,2) and (0,3) – #c3c3c3 – corresponds to a `Nonwalkable` tile
- All other colors are (for now) regarded to be `Walkable` tiles

The colors will later be used to determine which image to use for each tile:

- Black tiles are not handled at all (they are None, remember?) and have no image
- All other colors are converted into hex code (#ffffff instead of White)
- If one or several content textures have the hex code in their names (e.g. ffffff_1.png, ffffff_2.png etc.) a random one is selected
- If no corresponding image exists, the color is used to tint a random ffffff_x.png image

Finally, when all tiles have been initialized with a type, an image and, perhaps,
a tint, they are divided into rooms. Adjacent tiles that have the same color are
considered to belong to the same room. When the board is drawn, I the game draws
walls between all rooms.

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

The function above consists of two sub-operations. First, all tiles on the board
are fully initialized, then the board is divided into rooms.

Naturally, the bitmap has already been imported into the game content collection,
which is why it is available here 🙂


## Step 2: Initialize each tile

The `InitializeTiles` function below converts the texture into a color array and
applies each color the corresponding tile, as such:

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

In this example, I have reduced the number of parameters in the Tile constructor,
to make the code easier to read.

In the game, the Tile constructor uses the provided color to set the Image, Type
and Tint of the tile. However, since this is specific to my game, I have decided
to leave the constructor code out of this post. Just leave a comment if you want
to take a look at it.


## Step 3: Divide the board tiles into rooms

Once the board has a grid of tiles, where each tile has a color, my game divides
the tiles into rooms, using three functions.

`InitializeRooms` function makes sure that all tiles are handled, at least once:

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

A second `InitializeRooms` function takes a tile as a parameter. It ensures that
the tile fetches the room index from any already initialized adjacent tile, that
belongs to the same room (if any), sets a new room index if needed, then finally
spreads the room index to non-initialized adjacent tiles that belong to the same
room (if any).

For now, my function uses two sub-functions that cleans up the code a bit:

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


## Example

The steps above are all you really need to parse a bitmap into an playable map.

In the game that generated the final result below, I have 12 “ffffff” images and
4 “c3c3c3” images. I have no image for the red color or the dark grey rooms. The
result of this is that they use the same images as the hallway, and apply a tint
color on top of that.

In the image below, I display the room index as well. As you can see, the `None`
and `NonWalkable` tiles are given a room index of -1, while all other tiles are
separated into room 1-4.

![Mission map](/assets/img/blog/2010-02-21-3.png)

If I would like to create a totally different map for another mission, or simply
edit the map above, this approach makes it reaaaaally easy to do so.

I will return to the XML file in another blog post, as well as how to draw walls
between the various rooms, how to use different colors/images for the same rooms
etc., but that will have to wait for another day 🙂