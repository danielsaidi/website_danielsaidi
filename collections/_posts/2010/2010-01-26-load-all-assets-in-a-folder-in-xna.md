---
title: Load all assets in a folder in XNA
date:  2010-01-26 12:00:00 +0100
tags:  .net c# gaming
icon:  dotnet
---

I have started looking at XNA and am currently working with assets in a small game.
This post show how to load all assets in a folder, which is convenient for smaller
games where resources isn't as critical.


## Getting started with XNA

Although XNA has much to offer, I'm glad if I could even get so far as to create
a crappy game that no one will ever download. Jens, however, has created quite a
few games for XNA under the username *kobingo*. Make sure to check them out, and
especially have a look at the nice *Painting Party* game!

Jens has also created a nice framework to help you get started quickly. I think
it looks amazing, but I’d probably benefit from reading a tutorial or two on XNA
before using a framework on top of it. Game development is different from the code
I usually write, so I’d better read up before I get my hands dirty.

I chose to start my journey with a Microsoft tutorial that shows you how to get a
small 2D game up and running, with a bouncing sprite, and I will then go through
some more tutorials with 3D models and more advanced features, before I continue
my own project.


## My first utility: Batch load assets from a folder

After playing around with XNA for a while, I realized how tedious it is to load
assets manually, especially if the project uses a lot of images, sounds, textures
etc. I understand that asset management is important, but if your game will use
all resources in every level, things could be more convenient. 

I therefore decided to create a util that loads all assets from a folder with one
single call. The function below is a `ContentManager` extension that can load all
asset files in a folder and parse them into any asset types. It requires that the
specified folder is relative to the `Content.RootDirectory` folder.

```csharp
public static Dictionary<String, T> LoadContent<T>(this ContentManager contentManager, string contentFolder)
{
   //Load directory info, abort if none
   DirectoryInfo dir = new DirectoryInfo(contentManager.RootDirectory + "\\" + contentFolder);
   if (!dir.Exists) 
   {
      throw new DirectoryNotFoundException();
   }

   //Init the resulting list
   Dictionary<String, T> result = new Dictionary<String, T>();

   //Load all files that matches the file filter
   FileInfo[] files = dir.GetFiles("*.*");
   foreach (FileInfo file in files)
   {
      string key = Path.GetFileNameWithoutExtension(file.Name);
      result[key] = contentManager.Load<T>(contentManager.RootDirectory + "/" + contentFolder + "/" + key);
   }
   
   //Return the result
   return result;
}
```

This function can then, for instance, be used by the main `Game` class like this:

```csharp
var textures = Content.LoadContent<Texture2D>("Textures");
var models = Content.LoadContent<Model>("Models");
var songs = Content.LoadContent<Song>("Songs");
```

The method returns a dictionary, so if you want to access the “warrior” model in
the models dictionary, you just have to access it as such:

```csharp
var warriorModel = models["warrior"];
```

You could then build on top of this, add named keys for the most important asset
keys, add structs that refer to the models etc. but that is beyond the scope here.

Hope it helps!


