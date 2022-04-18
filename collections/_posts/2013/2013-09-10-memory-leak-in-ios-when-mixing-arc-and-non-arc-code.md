---
title: Memory leak in iOS when mixing ARC and non-ARC code
date:  2013-09-10 13:13:00 +0100
tags:  ios objc
---

In an iOS app of mine, I had a situation where the app shut down after taking a
couple of photos. The crash reports suggested a memory leak, but I had a hard time
reproducing it. Turns out the leak was caused by mixing ARC and non-ARC code.

When attempting to reproduce this, I launched Instruments to monitor how the app
allocates memory. I started taking photos, editing them, but didn't encounter a
memory leak...until I did.

The app has a custom camera view that overlays a crop rectangle and custom
buttons over the image preview. No other camera controls are visible and the result
is pretty clean and nice. However, this is not about bragging about what we did well,
but rather to look at what we didn't do as well.

Since the app is all about storing digital copies of plastic cards you normally
have in your wallet, users tend to take photos of their cards placed on a table
or a flat horizontal surface. Taking photos like this means that the camera will
be facing down, which may give the device a hard time to determine what is up and
what is down. This can in turn lead to the image being compressed or distorted.

To account for this, we perform a check, to see if the width is smaller than the
height. If so, we re-scale the image by switching the width and height and voilá!
We now have an image that is always correct.

But what about that memory leak, you may ask?

Well, I noticed that the memory leak only occured when holding the camera horizontally,
and not always. This lead me to consider what I just described above. 

Before we proceed, let's take a look at the camera delegate code:

```objc
- (void)imagePickerController:(UIImagePickerController *)picker didFinishPickingImage:(UIImage *)image editingInfo:(NSDictionary *)editingInfo {
   if (image.size.width < image.size.height) {
      image = [image imageByResizing:CGSizeMake(image.size.height, image.size.width)];
   }
   [picker dismissModalViewControllerAnimated:NO];
   [self openImageEditorWithImage:image];
}
```

My first guess was that the resize operation caused the memory leak. However, as 
I  investigated this further, it turned out that it was in fact the other way 
around. NOT resizing the image caused the leak.

The app was built for iOS 4.3 and didn't use ARC to start with. However, I added
ARC support when the app was being released, which means that we don't have any
release management in our code. The image editor, on the other hand, is based on
`HFImageEditor`, which does NOT use ARC.

So, as we pass in the original image into the image editor, the i,age can get stuck
in memory. This will eventually lead to a memory crash. If we set the image variable
to something else, however, the original image will now be automatically disposed
by ARC, and our temp weak image reference will be released as well, as soon as the
image editor is done with it.