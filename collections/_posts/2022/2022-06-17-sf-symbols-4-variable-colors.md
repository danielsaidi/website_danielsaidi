---
title:  SF Symbols 4 variable colors
date:   2022-06-17 01:00:00 +0000
tags:   swiftui sf-symbols colors

assets: /assets/blog/2022/2022-06-17/
image:  /assets/blog/2022/2022-06-17/image.jpg
---

SF Symbols is an amazing iconography library, that is designed to integrate seamlessly with the various Apple platforms. SF Symbols 4 adds even more features to these symbols, where variable colors will let you communicate values with your symbols. Let's take a look!

Until now, SF Symbols has supported various color modes, like `.monochrome`, `.hierarchical` and `.multicolor`, as well as symbol variants like `.fill`, `.circle` and more. Previous versions of SF Symbols applied `.monochrome` rendering by default, if you didn't specify a rendering mode. New this year is Automatic Rendering, which will define a preferred rendering mode for each symbol, that is made to fit each symbol's unique characteristic.

Another new feature this year is variable colors, which make some symbols adaptive to a numeric value. For instance, setting a `0.5` value for the wi-fi symbol will cause it to colorize half of the wifi beams. This is great for indicating e.g. signal strength. Symbols may use the value in any way that fits the symbol, or ignore it altogether.

You can easily define a variable value when you create your symbol instance, for instance:

```swift
Image(systemName: "wifi", variableValue: 0.5)
```

This result in a wifi icon where half the beams are highlighted. Setting the value to 0 will make all beams become dimmed, while setting it to 1 will highlight all beams.

Let's see this in action for multiple symbols at once:

```swift
struct Preview: View {
        
    @State private var value: CGFloat = 0

    func symbol(_ name: String) -> some View {
        Image(systemName: name, variableValue: value)
            .symbolVariant(.fill)
            .font(.largeTitle)
    }

    var body: some View {
        VStack(spacing: 20) {
            symbol("wifi")
            symbol("speaker.wave.3")
            symbol("waveform")
            symbol("applewatch.radiowaves.left.and.right")
            symbol("dot.radiowaves.right")
            symbol("dot.radiowaves.left.and.right")
            symbol("antenna.radiowaves.left.and.right")
            symbol("shareplay")

            Button("\(value.formatted())") {
                withAnimation {
                    if value == 1.0 {
                        value = 0
                    } else {
                        value += 0.2
                    }
                }
            }
            .font(.headline)
            .buttonStyle(.bordered)
            .padding()
        }
    }
}
```

We start with a variable value of 0, then increment it with 0.2 each time we tap the button. The result looks great, where each symbol handles the value a bit differently based on its unique characteristics:

![A screenshot that show variable colors in SF symbols]({{page.image}}){:width="850px"}

For now, animating the change behaves a big strange, where some symbols handle it gracefully while others apply 1 before applying the new value and doesn't animate the transition. Hopefully this will be fixed before it's released later this year.

However, the feature is doubtlessly an amazing addition to SF Symbols. I can't wait to use this in my apps, and hope that it makes the case for custom iconography even harder to justify.