---
title:  "Swift return does not work with line breaks"
date:   2014-09-10 20:00:00 +0100
categories: mobile
tags: 	ios swift
---


I am currently porting some iOS games from Objective-C to Swift, which involves
rewriting a lot of code. While doing this, I stumbled upon something interesting.

When I tried to temporarily disable the logic of a function, by adding a return
statement topmost in the function body, I noticed that it didn't work the way I
expected it to.

{% highlight swift %}
func doStuff() {
   return
   print("Doing some stuff")   
   //Some code for animating the hand
}
{% endhighlight %}

Care to guess what happens? Well, it turns out that Swift does not abort after
the return statement, but rather returns the result of the second like of code.

I thought that the return statement would immediately end all execution, but it
turns out that so is not the case with Swift.