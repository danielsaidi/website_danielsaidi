---
title: Swift return does not work with line breaks
date:  2014-09-10 20:00:00 +0100
tags:  ios swift
---

I'm currently porting some iOS games from Objective-C to Swift. While doing this, 
I stumbled upon something interesting in how Swift handles return statements and
new lines.

When I tried to temporarily disable the logic of a function, by adding a return
statement topmost in the function body, I noticed that it didn't work the way I
expected it to.

```swift
func doStuff() {
   return
   print("Doing some stuff")   
   //Some code for animating the hand
}
```

Turns out that unlike Objective-C, Swift doesn't abort after the return statement, 
but rather returns the result of the second line of code.

I thought that the return statement would immediately end all execution, but it
turns out that this isn't the case with Swift. Something to be aware of.