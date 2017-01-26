---
title:  "Memory leak in iOS when mixing ARC and non-ARC code"
date: 	2013-09-10 13:13:00 +0100
categories: apps
tags: 	ios xcode
---


In [an iOS app of mine](http://wally.danielsaidi.com/), that I made with friends,
we had a situation where the app shut down after taking a couple of photos. 

Looking at the crash reports, it was obvious that we had a memory leak somewhere,
but we had a hard time reproducing it.

I finally got some time to sit down with this, and launched Instruments to monitor
how the app allocates memory. I started taking photos, editing them, but did not
encounter a memory leak. Then, finally, I did.

In the app, we use a custom camera view that overlays a crop rectangle and custom
buttons over the image preview. No other camera controls are visible. The result
is (or was, at the time of launch) pretty nice.

However, this is not about bragging about what we did good, but rather to strike
down on what we did not do so good.

Since the app is all about storing digital copies of plastic cards you normally
have in your wallet, users tend to take photos of their cards placed on a table
or a flat horizontal surface. Taking photos like this means that the camera will
be facing down, which may give the device a hard time to determine what is up and
what is down. This can in turn lead to the image being compressed or distorted.

To account for this, we perform a check, to see if the width is smaller than the
height. If so, we re-scale the image by switching the width and height and voilá!
We now have an image that is always correct.

But what about that memory leak, you may ask?

I noticed that the memory leak only occured when holding the camera horizontally,
and not always. This lead me to considering what I just described above. Before
proceeding, take a look at the camera delegate code:

{% highlight objc %}
- (void)imagePickerController:(UIImagePickerController *)picker didFinishPickingImage:(UIImage *)image editingInfo:(NSDictionary *)editingInfo {
   if (image.size.width < image.size.height) {
      image = [image imageByResizing:CGSizeMake(image.size.height, image.size.width)];
   }
   [picker dismissModalViewControllerAnimated:NO];
   [self openImageEditorWithImage:image];
}
{% endhighlight %}

My first guess was that the resize operation was what caused the memory leak. As
I investigated this further, however, it turned out that it was in fact the other
way around. NOT resizing the image caused the leak.

Wally was built for iOS 4.3 and did not use ARC to start with. However, we added
ARC support when the app was being released, which means that we do not have any
release management in our code. The image editor, on the other hand, is based on
HFImageEditor, which does NOT use ARC.

So, as we pass in the original image into the image editor, it can gets stuck in
memory. This will eventually lead to the app crashing due to memory error. If we
set the image variable to something else, however, the original image will now be
automatically disposed by ARC, and our temp weak image reference will be released
as well, as soon as the image editor is done with it.