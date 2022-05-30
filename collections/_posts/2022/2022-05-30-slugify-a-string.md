---
title:  Slugify a string
date:   2022-05-30 01:00:00 +0000
tags:   swift

icon:   swift

swiftkit: https://github.com/danielsaidi/SwiftKit
---

In this post, we'll take a quick look at how to slugify a string in Swift, which may be nice if you want to generate tags, urls etc.

One such use-case that I've had myself, was when I created an app where users could apply custom tags to items. The app allowed free typing, then converted the entered string to a valid tag once the user tapped return. For instance, entering `Vacation Plans`, generated a `vacation-plans` tag. We then used this tag to group items, make it easier to index and search for tags etc.

Another situation where it's common to slugify strings is when you want to create valid urls for custom strings. You can see this in action by inspecting the url of this post.

To slugify a string, we can define a customizable set of allowed characters, use these to split the string into components, then join the components using a customizable component separator.

In Swift, an easy way to achieve this is to create a `slugified` extension function for `String`:

```swift
public extension String {
    
    func slugified(
        separator: String = "-",
        allowedCharacters: NSCharacterSet = NSCharacterSet(charactersIn: "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789-")
    ) -> String {
        self.lowercased()
            .components(separatedBy: allowedCharacters.inverted)
            .filter { $0 != "" }
            .joined(separator: separator)
    }
}
```

This extension lower-cases the entire string, then separates the string into characters using the provided character set. It then filters out any empty components, then finally joins the remaining ones.


## Conclusion

This was a short post, but I hope you found it helpful. You can find the source code in my [SwiftKit]({{page.swiftkit}}) library. Feel free to try it out and let me know what you think.