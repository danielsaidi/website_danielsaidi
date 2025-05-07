---
title:  Supercharging SwiftUI Text with Dynamic Content Styling
date:   2025-04-08 07:00:00 +0000
tags:   swiftui

image:  /assets/blog/25/0408/header.jpg
image-show: 0
assets:  /assets/blog/25/0408/

bsky:   https://bsky.app/profile/danielsaidi.bsky.social/post/3lmcsb27je22b
toot:   https://mastodon.social/@danielsaidi/114303063345595654
---

{% include kankoda/data/open-source name="TextReplacements" %}
In this post, we'll take a look at how to extend the SwiftUI `Text` with ways to customize any parts of its text, either individual words or longer segments.


## Native Text Capabilities

SwiftUI's `Text` view has been extended with amazing capabilities over the years, and now lets us do a lot more that was initially not possible, such as rendering Markdown like this:

```swift
Text("This is [Markdown](https://www.markdownguide.org) with *some* **formatting**")
    .foregroundStyle(.blue)
    .tint(.yellow)
    .font(.largeTitle)
```

By simply passing in a Markdown string, `Text` will render texts and links, with some styling options.

![Markdown Preview]({{page.assets}}markdown.jpg)

As you can see in the code, you can apply a `foregroundStyle` to color the text, a `tint` to color links, and use Markdown syntax to make any part of the text underlined, italic, and bold.

Since a few years back, you can also combine multiple `Text` views into a single view, like this:

```swift
Text("I") +
Text(" **love** (❤️) ").foregroundStyle(.red) +
Text("SwiftUI!")
```

Combined with Markdown, this gives us even more power over how our text is rendered, since we can apply various view modifiers to various parts of the resulting text.

But even if these capabilities are great, I wanted to explore a more streamlined way to achieve the same result. And I think I've come up with something pretty nice.


## Text Replacements

Let's use SwiftUI's ability to combine multiple `Text` views to provide a way to pass in one or multiple replacements when creating a `Text` view.

I want to have a simple way to pass in a single replacement, and another to pass in multiple ones. 

This is how I want it to look:

```swift
public struct TextReplacementView: View {
    
    /// Creates a text view with a text and a single replacement.
    init(
        _ text: String,
        replace: String,
        with replacement: @escaping (String) -> Text
    ) {
        self.init(text, replacements: [replace: replacement])
    }
    
    /// Creates a text view with a text and a multiple replacements.
    init(
        _ text: String,
        replacements: [String: (String) -> Text]
    ) {
        // Insert magic here
    }
}
```

I then created a `processReplacements` function that can find one or multiple matches in the provided `text` and replace them with custom `Text` views, using the replacement text builders:

```swift
private extension Text {
    
    /// Process the replacements in a deterministic way
    static func processReplacements(
        in text: String,
        with replacements: [String: (String) -> Text]
    ) -> Text {
        
        // Create a structure to track replacement positions
        struct Replacement {
            let range: Range<String.Index>
            let pattern: String
            let replacementFunc: (String) -> Text
        }
        
        // Find all occurrences of all patterns
        var allReplacements: [Replacement] = []
        
        // Find text ranges for all specified replacements
        for (pattern, replacementFunc) in replacements {
            var searchRange = text.startIndex..<text.endIndex
            
            while let range = text.range(of: pattern, range: searchRange) {
                allReplacements.append(Replacement(
                    range: range,
                    pattern: pattern,
                    replacementFunc: replacementFunc
                ))
                searchRange = range.upperBound..<text.endIndex
            }
        }
        
        // Sort replacements by position, then by length
        // Longer patterns are handled first to handle overlaps
        allReplacements.sort { first, second in
            if first.range.lowerBound != second.range.lowerBound {
                return first.range.lowerBound < second.range.lowerBound
            }
            return first.pattern.count > second.pattern.count
        }
        
        // Process the text with non-overlapping replacements
        var result = Text("")
        var currentIndex = text.startIndex
        
        // Remove overlapping replacements
        var validReplacements: [Replacement] = []
        var lastEnd: String.Index?
        
        for replacement in allReplacements {
            if let lastEnd = lastEnd, replacement.range.lowerBound < lastEnd {
                continue // Skip overlapping replacement
            }
            validReplacements.append(replacement)
            lastEnd = replacement.range.upperBound
        }
        
        // Apply the valid replacements
        for replacement in validReplacements {
            // Add text before the replacement
            if currentIndex < replacement.range.lowerBound {
                let beforeText = text[currentIndex..<replacement.range.lowerBound]
                result = result + Text(String(beforeText))
            }
            
            // Add the replacement
            result = result + replacement.replacementFunc(replacement.pattern)
            currentIndex = replacement.range.upperBound
        }
        
        // Add any remaining text
        if currentIndex < text.endIndex {
            let remainingText = text[currentIndex..<text.endIndex]
            result = result + Text(String(remainingText))
        }
        
        return result
    }
}
```

The function iterates over all the provided replacements, to find all matching ranges within the text, then either renders a regular `Text` or a `Text` replacement.

The resulting `Text` initializers take a `text` and one or multiple `(String) -> Text` replacements, and can now be used as easy as this:

```swift
Text(
    "Some text",
    replace: "text",
    with: { Text($0).foregroundStyle(.red) }
)
```

...or as complicated as this:

```swift
Text(
    "TextReplacements is a SwiftUI library that extends the Text view with ways to customize any parts of its text. The result is a Text with customized segments that can flow nicely over multiple lines.",
    replacements: [
        "TextReplacements": {
            Text($0)
                .font(.title)
                .fontWeight(.black)
                .fontDesign(.rounded)
                .foregroundColor(.green)
        },
        "SwiftUI": {
            Text($0)
                .font(.headline)
                .fontWeight(.black)
                .fontDesign(.rounded)
                .foregroundColor(.blue)
        },
        "Text": {
            Text($0)
                .fontWeight(.black)
                .fontDesign(.rounded)
                .foregroundColor(.black.opacity(0.6))
        },
        "customize": {
            Text($0)
                .italic()
                .underline()
                .font(.body)
                .fontWeight(.heavy)
                .fontDesign(.monospaced)
                .foregroundColor(.purple)
        },
        "par": {
            Text($0)
                .font(.headline)
                .fontWeight(.black)
                .fontDesign(.rounded)
                .foregroundColor(.red)
        },
        "can flow nicely over multiple lines": {
            Text($0)
                .foregroundColor(.orange)
        }
    ]
)
.padding()
#if os(visionOS)
.frame(maxWidth: 350)
.background(.ultraThickMaterial)
.background(.white.opacity(0.5))
.clipShape(.rect(cornerRadius: 10))
.padding()
.scaleEffect(2)
#endif
```

The result can customize a single word or large chunks of text, and flows nicely over multiple lines:

![TextReplacementView Preview]({{page.assets}}preview.jpg)

Since the replacements are `(String) -> Text` builders, this approach only supports view modifiers that return `Text`. This means that modifiers like `.backgroundStyle` don't work. 

If you know a way around this, or would like to explore it, just let me know. Perhaps there are some background modifiers that generate `Text` that I don't know about?


## Conclusion

The `Text` initializers that we built in this post let us customize the rendering of any part of a `Text`. I have created an [open-source project]({{project.url}}) that lets you add these utilities to any app.

The `TextReplacements` library works on all platforms, all the way back to iOS 13, which means that it works on iOS, macOS, tvOS, watchOS, and visionOS.

I hope that you will love using `TextReplacements`. Happy styling!