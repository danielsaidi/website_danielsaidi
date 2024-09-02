---
title: A* implementation for XNA
date:  2010-02-16 12:00:00 +0100
tags:  archive
---

I have recently been playing around with the XNA game framework that can
be used to develop games for the Xbox 360. It's fun, but different from
the code I usually write. For instance, I'm currently trying to implement
the A* pathfinding algorithm in C# for a board game that I'm working on.

So far, I have an engine foundation for a board game that mimics the old
Hero Quest board game. It lets me generate missions from images and text
files, where the image defines the board and the text file describes the
mission, emenies, goals etc.

This makes it easy to develop a large amount of missions, that can be
grouped into campaigns. By making the base model solid, new missions will
involve more content management than programming.

When my game engine imports a mission map, it generates a grid of walkable
and unwalkable tiles that make up the board. I will write more about these
tiles and how to handle them in a future post.

The next step is to be able to find the shortest path between two tiles. I
have found a great tutorial for this and will use it to implement the A* 
pathfinding algorithm in C#.