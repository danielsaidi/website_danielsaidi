---
title: UIImageView is pixelated after applying an async image
date:  2016-11-10 09:39:02 +0100
tags:  swift uikit
icon:  swift
---

In an iOS project that I'm working on, I load images asynchronously into an `UIImageView`. Although images are properly fetched, they're pixelated when they're added to the view.

I first add a placeholder image from the bundle to the image view as the image fetch starts, to indicate that the image hasn's been downloaded yet. The placeholder is a PDF, which is loaded as @2x or @3x depending on the device. It looks very sharp on all devices.

Turns out that when the asynchronously loaded image is loaded into the image view, it
will use a scale that differs from the placeholder, since the async image has a
@1x scale. As a result, the image becomes pixelated.

If I skip applying the placeholder image before beginning to download the image, the async image looks great. The solution is to generate a new image from the loaded one, using the proper scale. This is how you do it (broken down in small steps):

```swift
guard let image = loadedImage else { return }
guard let cgImage = image.cgImage else { return }
let scale = UIScreen.main.scale
let orientation = image.imageOrientation
let image = UIImage(cgImage: cgImage, scale: scale, orientation: orientation)
self.imageView.image = image
```

The image is now super-sharp and the world a bit happier than it was ten minutes ago.