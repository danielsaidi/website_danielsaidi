---
title:  Building a SwiftUI text view that can customize the rendering of certain words
date:   2025-04-08 07:00:00 +0000
tags:   swiftui

image:  /assets/sdks/textreplacementview-header.jpg
assets:  /assets/blog/25/0408/

bsky:   https://bsky.app/profile/danielsaidi.bsky.social/post/3lmcsb27je22b
toot:   https://mastodon.social/@danielsaidi/114303063345595654
---

{% include kankoda/data/open-source name="TextReplacementView" %}
In this post, we'll take a look at how to create a `Text` view replacement that lets us customize how certain parts of a text is rendered.

![iOSKonf25 logo]({{page.image}})

SwiftUI's `Text` view has become amazing over the years, and now supports rendering Markdown:

```swift
Text("This is [Markdown](https://www.markdownguide.org) with *some* **formatting**")
    .foregroundStyle(.blue)
    .tint(.yellow)
    .font(.largeTitle)
```

You can apply a `foregroundStyle` to color the text, a `tint` to color links, and use other text modifiers like underline, italic, and bold, but that's about it:

![Markdown Preview]({{page.assets}}markdown.jpg)

To get better control over how a text is rendered, let's use SwiftUI's ability to combine multiple `Text` views into a single view, to implement a way to provide one or multiple `Text` builders for a string:

```swift
public struct TextReplacementView: View {
    
    /// Create a replacement view with a single replacement.
    public init(
        _ text: String,
        replace: String,
        with replacement: @escaping (String) -> Text
    ) {
        self.init(text, replacements: [replace: replacement])
    }
    
    /// Create a replacement view with multiple replacements.
    public init(
        _ text: String,
        replacements: [String: (String) -> Text]
    ) {
        self.content = Self.processReplacements(
            in: text,
            with: replacements
        )
    }
    
    private let content: Text
    
    public var body: some View {
        content
    }
}
```

This view will use a static `processReplacements` function to find and replace one or multiple strings in the provided `text` with custom `Text` views:

```swift
private extension TextReplacementView {
    
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

The `processReplacements` function iterates over all the replacements that we provide, to find all the matching ranges for each replacement, then either render a regular `Text` or a `Text` replacement.

The resulting `TextReplacementView` takes a `text` string and a `[String: (String) -> Text]` dictionary, and has a convenience initializer for when we just want to customize a single word.

We can now use the `TextReplacementView` as easy as this:

```swift
TextReplacementView(
    "Some text",
    replace: "text",
    with: { Text($0).foregroundStyle(.red) }
)
```

...or as complicated as this:

```swift
TextReplacementView(
    "TextReplacementView can be used to customize any part of a text and render the text as a collection of concatenated Text views that flow nicely over multiple lines.",
    replacements: [
        "TextReplacementView": {
            Text($0)
                .font(.title.bold())
                .fontDesign(.rounded)
                .foregroundColor(.green)
        },
        "customize": {
            Text($0)
                .font(.body.bold())
                .foregroundColor(.purple)
        },
        "part": {
            Text($0)
                .font(.headline)
                .foregroundColor(.red)
        },
        "text": {
            Text($0)
                .underline()
                .strikethrough()
        },
        "Text": {
            Text($0)
                .bold()
                .foregroundColor(.black.opacity(0.6))
        },
        "flow nicely over multiple lines": {
            Text($0)
                .foregroundColor(.orange)
        }
    ]
)
```

to customize multiple parts of the provided string. The result is a multi-`Text` view that flows nicely over multiple lines, and that can customize a single word or large chunks of text:

![TextReplacementView Preview]({{page.assets}}preview.jpg)

Note that a limitation is that it can't apply a background style, since the `.backgroundStyle` modifier returns `some View` and not `Text`. If you know a way around this, please let me know.


## Conclusion

The `TextReplacementView` that we built in this post lets us customize the rendering of any part of a rendered text. I have created an [open-source project]({{project.url}}) that you can use to give it a try.

The `TextReplacementView` component works on all platforms, all the way back to iOS 13 and aligned OS versions, which means that you can use it on iOS, macOS, tvOS, watchOS, and visionOS.

Happy text styling!