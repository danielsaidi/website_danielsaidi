---
title:  "Adding photos to the iPhone simulator"
date:    2012-03-13 12:00:00 +0100
categories: mobile
tags: 	ios ios-simulator
---


I am currently developing an iOS app that will make use of the device camera. It
works really well, but since I am also running this app on the simulator, I want
to be able to select pictures from the photo library as well.

However, once I open up the UIImagePickerControllerSourceTypePhotoLibrary dialog
in the iPhone simulator, I am presented with the following screen:

![No Photos iPhone screen](/assets/img/blog/2012-03-13-1.png "No Photos – You can sync photos and videos onto your iPhone using iTunes.") 

Uhm, can I? I have not found a way to do so, but there is an easy workaround for
this so that you easily can add photos to your simulator.

Just open up Finder and drag any image you want to add into your simulator. When
you see the green plus icon, just release the image. The image will then open up
in Safari, like this:

![Safari browser screenshot](/assets/img/blog/2012-03-13-2.png "The Safari browser shows the image that was dragged to the simulator.")

Click the image and keep the mouse button pressed. You will now get an option to
save the iamge to the simulator:

![Save option](/assets/img/blog/2012-03-13-3.png "Press and hold the left mouse button to open the save and copy action sheet")

That’s it! If you open up the photo library, you will see the image in your list
of saved images:

![Photo library](/assets/img/blog/2012-03-13-4.png "The photo is added to the photo library")

Hope it helps!