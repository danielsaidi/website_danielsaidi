---
title:  Introducing TagKit
date:   2022-08-19 00:00:00 +0000
tags:   article swift swiftui tagging 

image:  https://github.com/danielsaidi/TagKit/raw/main/Resources/Logo.png

demo:   https://github.com/danielsaidi/TagKit/raw/main/Resources/Demo.gif

swiftkit:   https://github.com/danielsaidi/TagKit
swiftuikit: https://github.com/danielsaidi/TagKit
tagkit:     https://github.com/danielsaidi/TagKit
wally:      https://wally.app
---

As I'm currently rewriting my old [Wally]({{page.wally}}) app from scratch for iOS 16 and SwiftUI 2, I'm adding a bunch of user requested features. One such feature is tagging, which will let users categorize their content. For reusability and sharing purposes, I've put all this tag-specific logic in a brand new, tiny package called [TagKit]({{page.tagkit}}). Let's take a look!

Tagging is used in many platforms and applications, and I'm happy to finally add it to Wally. Since I will probably want to add it to other apps as well, I decided to extract tag-related functionality from Wally and libraries like [SwiftKit]({{page.swiftkit}}) and [SwiftUIKit]({{page.swiftuikit}}), which is part of my long-term strategy to make these pretty huge libraries smaller, in favor for smaller and more focused libraries. If this can also help others out, that's a great bonus!

You probably don't need an introduction to tagging, but let's at least start with looking at slugifying strings, which is the process of converting string by removing unsupported characters and replacing whitespace with a separator.


## Slugifying strings

You can see slugified strings in action in many web urls (for instance this one), where the page date and title is often slugified to create a unique valid url that also describes the content.

In TagKit, the ``Slugifiable`` protocol describes a slugifyable type:

```swift
public protocol Slugifiable {

    var slugifiableValue: String { get }
}
```

`String` implements this protocol by default, by returning itself as the slugifiable value.

Once a type implements ``Slugifiable``, it can be slugified with the `slugified()` function:

```swift
let string = "Hello, world!"
let slug = string.slugified() // Returns "hello-world"
```

You can also provide a custom ``SlugConfiguration`` to customize the slugified result:

```swift
let string = "Hello, world!"
let config = SlugConfiguration(
    separator: "+",
    allowedCharacters: NSCharacterSet(charactersIn: "hewo")
)
let slug = string.slugified() // Returns "he+wo"
```

You probably won't need to use these functions directly, nor customize the configuration, but if you have to, you can.


### Taggable types

With slugified strings in place, we can start looking at tagging, which is the process of adding tags (or labels) to items, which can be used to group, filter etc.

In TagKit, the ``Taggable`` protocol describes a taggable type:

```swift
public protocol Taggable {

    var tags: [String] { get set }
}
```

Once a type implements ``Taggable``, it can make use of all the functionality that the protocol provides, such as `hasTags`, `slugifiedTags`, `hasTag(...)`, `addTag(...)`, `removeTag(...)`, `toggleTag(...)` etc. 

Collections that contain ``Taggable`` types also get some additional functionality as well.

This means that you can now let your domain model types implement ``Taggable`` and automatically get access to a bunch of logic. This is also true for your observable SwiftUI view models, which means that you can easily create a UI where you can add, remove and toggle tags on an observable type, then write the changes back to the item that you are adding.


## Views

TagKit also has a few views that aim at making it easier to work with tags. For instance, ``TagList`` and ``TagEditList`` let you list and edit tags with a customizable tag view, ``TagCapsule`` renders tags with a customizable style and ``TagTextField`` automatically slugifies text as you type.

You can have a look at the SwiftUI previews in these files to see them in action, but a very basic example looks like this.

![An animated gif of an app that uses TagKit]({{page.demo}}){:width="650px"}

The demo above shows very plain tag capsules, but you can style these capsules in any way you like, or even replace them with your own custom views.


## Conclusion

TagKit is currently a tiny library, but I really like how creating these small, super-focused libraries make it easy to reuse functionality and share what you create with the world. 

Feel free to checkout the [GitHub repository]({{page.tagkit}}) and try this out. I'd be very interested in hearing what you think.