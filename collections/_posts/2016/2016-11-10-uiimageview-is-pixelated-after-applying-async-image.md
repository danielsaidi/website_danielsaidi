---
title: UIImageView is pixelated after applying an async image
date:  2016-11-10 09:39:02 +0100
tags:  ios swift
---

In an iOS project that I'm currently working on, I load images asynchronously into an
`UIImageView`. Although the images are properly fetched, they are pixelated once they 
are added to the image view.

The setup that I use, is that I first add a placeholder image from the bundle to the
image view as the image fetch starts, to indicate that the image hasn's been downloaded 
yet. The placeholder is a PDF asset, which is loaded as @2x or @3x depending on the
device. It looks very sharp on all devices.

Turns out that when the asynchronously loaded image is loaded into the image view, it
will have a scale that differs from the placeholder image, since the async image has a
@1x scale. As a result, the image becomes pixelated.

If I skip applying the placeholder image before beginning to download the image,
the downloaded image looks perfect. The solution is to generate a new image from the
loaded one, using the proper scale. This is how you do it (broken down in small steps):

```swift
guard let image = loadedImage else { return }
guard let cgImage = image.cgImage else { return }
let scale = UIScreen.main.scale
let orientation = image.imageOrientation
let image = UIImage(cgImage: cgImage, scale: scale, orientation: orientation)
self.imageView.image = image
```

The image is now super-sharp and the world a bit happier than it was ten minutes ago.