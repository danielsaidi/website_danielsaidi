---
title: Adding photos to the iPhone simulator
date:  2012-03-13 12:00:00 +0100
tags:  ios

image: /assets/blog/12/0313-4.png
assets: /assets/blog/12/0313-
---

I'm building an iOS app that makes use of the device camera. It works well, but since I'm also using the simulator, I want to be able to select pictures from the photo library as well.

However, once I open up a `UIImagePickerControllerSourceTypePhotoLibrary` dialog in the iPhone simulator, I'm presented with the following screen:

![No Photos iPhone screen]({{page.assets}}1.png "No Photos – You can sync photos and videos onto your iPhone using iTunes.") 

Can I? I haven't found a way, but there's a workaround that can add photos to a simulator.

Just open up Finder and drag any image you want to add into the simulator. When you see the green plus icon, just release the image. The image will then open up in Safari, like this:

![Safari browser screenshot]({{page.assets}}2.png "The Safari browser shows the image that was dragged to the simulator.")

Click the image and keep the button pressed. You now get an option to save the image:

![Save option]({{page.assets}}3.png "Press and hold the left mouse button to open the save and copy action sheet")

That’s it! If you open up the photo library, you'll see the image in your list of saved images:

![Photo library]({{page.assets}}4.png "The photo is added to the photo library")

You can then use it as you see fit in the simulator.

Hope it helps!