---
title: Øredev 2011 in the rear-view mirror – Part 1
date:  2011-11-23 12:00:00 +0100
categories: conferences
tags:  conferencess
icon:  avatar
---

Two weeks ago, I attended to the Øredev Developer Conference in Malmo, Sweden. It was truely inspiring. In this series of sum-ups, I will try to summarize the talks I went to.


## KEYNOTE: Alexis Ohanian - Only your mom wants to use your website

After an early morning flight, we arrived too late for the morning keynote with Reddit co-founder Alexis Ohanian. The doors to the keynote were closed, but it was playing outside.

Alexis talked about how to convince people that your product is worth their time and how companies tend to ignore the user experience, such as how airline ticket search engines return a huge amount of tickets, where most are not what we want.

I will watch the talk once the video is up. People who saw it told me it was good, although Jeff Atwood threw Reddit some shade during his Friday keynote.


## Jon Skeet - C#5 Async 101

Jon talked about the new .NET `async` concurrency feature that will be available in C# 5.

Jon talked about how async operations have been around since .NET 1, but how they have resulted in spaghetti code. The new `async` and `await` keywords seems like a clean way to write async code. The resulting code reads like non-async code, which is very nice.

A cool thing is that all `await`s are potential pause points. If .NET has all it needs to return a result, it will do so without going async. Jon showed this in action. It looks great!


## Gary Short - .NET 4.0 Collection Classes Deep Dive

Gary talked about list fundamentals in .NET. For instance, when you add items to an empty list, its capacity grows to 4. When that's no longer enough, it expands to 8, then 16, and so on. If possible, initialize the list with an int parameter, e.g. `new List<string>(10)`, to make it grow in fixed steps, which is more performant.

Gary also told us to not use `Add` in a loop, since `AddRange` is better, and how `XAt` is better than `X` (`RemoveAt` instead of `Remove` etc.). A function that specifies the index executes at fixed time, no matter how many items you have in a list. With graphs of how much slower certain operations work when the size of the list grows, Gary really exhausted the subject.

Gary also went through sorting and how the various sorting algorithms perform and ended the session by going through various collection types. To understand lists and collections, this was a great talk.