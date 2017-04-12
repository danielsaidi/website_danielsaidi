---
title:	"Load all assets in a folder in XNA"
date:	2010-01-26 12:00:00 +0100
categories: games
tags: 	xna assets
---


After playing around with XNA for a while, I quickly realized how tedious it is
to load assets manually, especially if the project uses a lot of images, sounds,
textures etc.

The function below is a `ContentManager` extension that can load all asset files
in a folder and parse them into any asset types. The extension requires that the
specified folder is relative to the `Content.RootDirectory` folder.


	public static Dictionary<String, T> LoadContent<T>(this ContentManager contentManager, string contentFolder)
	{
	   //Load directory info, abort if none
	   DirectoryInfo dir = new DirectoryInfo(contentManager.RootDirectory + "\\" + contentFolder);
	   if (!dir.Exists)
	      throw new DirectoryNotFoundException();
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

The functioncan for instance be used by the main Game class like this:

	var textures = Content.LoadContent<Texture2D>("Textures");
	var models = Content.LoadContent<Model>("Models");
	var songs = Content.LoadContent<Song>("Songs");

The method returns a dictionary, so if you want to access the “warrior” model in
the models dictionary, you just have to access it as such:

	var warriorModel = models["warrior"];

Hope it helps!