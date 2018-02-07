---
title:	"Load all assets in a folder in XNA"
date:	2010-01-26 12:00:00 +0100
tags: 	.net game-development c#
---


I have started looking at XNA, which I've been longing to do for quite some time
now. My friend Jens came over to guide me some of the basics, and after a little
configuration, we happily loaded random textures by pressing space.


## Gettings started with XNA

Although XNA has much to offer, I'm glad if I could even get so far as to create
a crappy game that no one will ever download. Jens, however, has created quite a
few games for XNA as *kobingo*. Make sure to check them out, especially the nice
*Painting Party* app!

Jens has also created a nice XNA framework that helps you get started quickly. I
think it looks absolutely amazing, but still think that I’d benefit from reading
a tutorial or two before just using a framework on top of XNA. Game projects are
quite different from the software I usually create, so I’d better read something
before I get my hands dirty.

I chose to start my XNA journey with a Microsoft tutorial that shows you how you
get a small 2D game up and running, with a bouncing sprite. You can check it out
[here](http://msdn.microsoft.com/en-us/library/bb203893.aspx). I will then check
out some more tutorials with 3D models and a more advanced functionality, before
I continue my “which-I-thought-would-be-trivial" project.


## My first util: Batch load assets from a folder

After playing around with XNA for a while, I quickly realized how tedious it is
to load assets manually, especially if the project uses a lot of images, sounds,
textures etc. I therefore decided to create a util that loads all assets from a
folder with one single call.

The function below is a `ContentManager` extension that can load all asset files
in a folder and parse them into any asset types. The extension requires that the
specified folder is relative to the `Content.RootDirectory` folder.


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


The function can, for instance, be used by the main Game class like this:

	var textures = Content.LoadContent<Texture2D>("Textures");
	var models = Content.LoadContent<Model>("Models");
	var songs = Content.LoadContent<Song>("Songs");

The method returns a dictionary, so if you want to access the “warrior” model in
the models dictionary, you just have to access it as such:

	var warriorModel = models["warrior"];

Hope it helps!


