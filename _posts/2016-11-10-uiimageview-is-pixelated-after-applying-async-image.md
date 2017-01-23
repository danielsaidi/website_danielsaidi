---
title:  "UIImageView is pixelated after applying an async image"
date:   2016-11-10 09:39:02 +0100
categories: apps
tags:	ios swift uiimageview uiimage cgimage
---


In a project that I'm currently working on, I load images asynchronously into an
UIImageView. As the download starts, I apply a placeholder image from the bundle
to the image view, to indicate that no image has yet been downloaded.

The placeholder is a PDF vector asset, which is loaded as @2x or @3x, depending
on the device. It looks very sharp on all devices.

However, when the asynchronously loaded image is loaded into the image view, the
image will have a scale that differs from the placeholder, since the async image
is loaded as a @1x image. As a result, the image becomes pixelated.

If I skip applying the placeholder image before beginning to download the image,
the downloaded image looks perfect. Seems like the image view is keeping a state
when receiving a new image with a different scale. 

The solution turned out to be, to generate a new image from the loaded one. This
is how you do it (broken down in small steps):

{% highlight swift %}
guard let image = loadedImage else { return }
guard let cgImage = image.cgImage else { return }
let scale = UIScreen.main.scale
let orientation = image.imageOrientation
let image = UIImage(cgImage: cgImage, scale: scale, orientation: orientation)
self.imageView.image = image
{% endhighlight %}

The image is now super-sharp and the world a bit happier than it was ten minutes ago.