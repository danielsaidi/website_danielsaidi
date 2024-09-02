---
title: Load all assets in a folder in XNA
date:  2010-01-26 12:00:00 +0100
tags:  gamedev
---

I have started looking at XNA and working on a small game. This post show how to load all assets in a folder, which is convenient for small games with not that many resources.


## Getting started with XNA

Although XNA has much to offer, I still struggle with the game engine aspects and would be insanely happy to even release a crappy game that no one will ever download.

My friend Jens, however, has created quite a few games for XNA under the name *kobingo*. Make sure to check them out, especially the nice *Painting Party*.

Jens has also created a framework to help you get a new game up and running quickly. It looks amazing, but I’d probably benefit from reading some XNA tutorials first.

Game development is different from the code I usually write, so I’d better read up before I get my hands bypass a lot of the fundamentals by using a framework.

So I chose to start with a Microsoft tutorial that shows you how to get a small 2D game up and running, with a bouncing sprite, before I continue my own project.


## My first utility: Batch load assets from a folder

After some time with XNA, I realized how tedious it is to load assets manually, especially if the project uses a lot of images, sounds, textures etc. 

I understand that proper asset management is important, but if a game uses all resources in every level, things could be more convenient. 

I therefore decided to create a way to load all assets from a folder with a single call, with a nice convenient API that reduces the amount of code at the call site. 

The extension below loads all asset files in a folder and parse them into any asset types. It requires that the specified folder is relative to the `Content.RootDirectory` folder.

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

This function can then be used by the main `Game` class like this:

```csharp
var textures = Content.LoadContent<Texture2D>("Textures");
var models = Content.LoadContent<Model>("Models");
var songs = Content.LoadContent<Song>("Songs");
```

The method returns a dictionary, so if you want to access the “warrior” model in the models dictionary, you just have to access it as such:

```csharp
var warriorModel = models["warrior"];
```

You could then build on top of this, add named keys for the most important asset keys, add structs that refer to the models etc. but that is beyond the scope here.