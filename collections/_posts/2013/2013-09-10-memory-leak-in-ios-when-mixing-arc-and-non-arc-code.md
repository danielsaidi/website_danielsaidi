---
title: Memory leak in iOS when mixing ARC and non-ARC code
date:  2013-09-10 13:13:00 +0100
tags:  ios
icon:  swift
---

I had an iOS app crash after taking a few photos. The stack trace showed a memory leak, but I had a hard time finding it. Turns out that it was caused by mixing ARC and non-ARC.
 
When I tried reproducing it, I launched Instruments to see how the app allocates memory. I started taking photos, editing them, but didn't encounter a memory leak...until I did.

The app has a custom camera view that overlays a crop rectangle and custom views over the image preview. No other camera controls are visible and the result is pretty clean.

The app is all about storing copies of cards in your wallet, so users tend to take photos of their cards placed on a table or a flat horizontal surface. This means that the camera will be facing down, which may give the device a hard time to determine what is up and down.

This can in turn lead to the image being compressed or distorted, so we perform a check to see if the width is smaller than the height. If so, we re-scale the image and voilá, we get an image that is always correct.

But what about that memory leak?

Well, I noticed that the memory leak only occured when you held the camera horizontally, and not always. This lead me to consider what I just described above. 

Let's take a look at the camera delegate code:

```objc
- (void)imagePickerController:(UIImagePickerController *)picker didFinishPickingImage:(UIImage *)image editingInfo:(NSDictionary *)editingInfo {
   if (image.size.width < image.size.height) {
      image = [image imageByResizing:CGSizeMake(image.size.height, image.size.width)];
   }
   [picker dismissModalViewControllerAnimated:NO];
   [self openImageEditorWithImage:image];
}
```

My first guess was that the resize operation caused the memory leak. However, as I looked into this, it turned out that it was the other way around. NOT resizing it caused the leak.

The app was built for iOS 4.3 and didn't use ARC at first. I added ARC support when it was being released. However, the image editor uses `HFImageEditor` which does NOT use ARC.

So, as we pass in the original image into the image editor, it can get stuck in memory. This will eventually lead to a crash. If we however set the image variable to something else, the image will be automatically disposed by ARC, and our temp weak image reference will be released as well, as soon as the image editor is done with it.