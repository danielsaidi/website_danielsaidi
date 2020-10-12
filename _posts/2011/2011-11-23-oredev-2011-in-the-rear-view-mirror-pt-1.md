---
title: Øredev 2011 in the rear-view mirror – Part 1
date:  2011-11-23 12:00:00 +0100
tags:  conference
categories: conferences
---

This is the first part of my Øredev 2011 summary. I will label each session with
day:order to satisfy all structure freaks (myself included) that read this.


## About Øredev

Two weeks ago, I attended to the Øredev Developer Conference in Malmo, Sweden. I
was there from Wednesday to Friday and it was truely inspiring.

In this summary, I will write about sessions I attended, sessions I missed and a
few sessions I heard a lot of good stuff about, but either missed or had to skip.

I have deliberately waited two weeks with this summary, since I want it to be an
objective summary and not one caught up in the conference hype. I will sum up the
sessions over a couple of days, so my summary of Øredev will consist of a number
of posts. I will label every talk with day:order to satisfy all structure freaks
(myself included) that read this.


## 1:1 - KEYNOTE: Alexis Ohanian - Only your mom wants to use your website

After an early morning flight, we arrived a bit too late for the morning keynote
with Reddit co-founder Alexis Ohanian. The doors to the keynote were closed, but
the keynote was displayed on screen just outside, so we managed to catch a short
glimpse of it.

Alexis talked about how to convince people that your product is worth their time,
and that companies tend to ignore users experience, and thus their customers. He
mentioned some bad scenarios that we have gotten used to as Internet users, such
as airline ticket search engines that return a huge amount of tickets in a list.

I attempted to make out the most of the real-time illustration that was drawn of
the keynote (and of all other keynotes), but will watch the recording once it is
released instead. People who attended it told me it was quite good, although Jeff
Atwood threw Reddit a pinch during his Friday keynote - more on that later.


## 1:2 - Jon Skeet - C#5 Async 101

The first talk I attended was one of many, where a distinct British, Scottish or
American accent, with an almost over-the-top engagement, seemed to be two of the
key-criteria to be invited as a speaker. Jon was very inspiring, though, and did
demonstrate the new async keyword, which will be shipped together with C#5.

Jon talked about how async operations have been around since back in .NET 1, but
that they always have been jumpy and resulted in spaghetti code. The new `async`
keyword, though, together with `await` seems like a clean and promising tool for
writing async operations.

The resulting code looks just like "ordinary" non-async code, with new keywords.
Another really cool thing about it all is awaits are potential pause points. If
.NET has all the data it needs to provide the caller with a result, it will do so
without going async.

Jon then showed us these new features in action and finished off by pointing out
that `async != parallell`

I am looking forward to try this out!


## 1:3 - Gary Short - .NET 4.0 Collection Classes Deep Dive

I found this talk to be really interesting while listening to it, but afterwards
cannot think of much that I can use  in my daily life as a system developer.

He talked a lot about list operations and how NOT to populate lists. For instance,
did you know that when you add your first item to the list, its capacity grows to 
4...and that when that is no longer enough, it grows to 8, then 16, then 32...?
Well, neither did I. Gary suggested to initialize the list with an int parameter,
e.g. new List<string>(10), which will cause it to grow with fixed interval steps.

He also told us to not use Add in a loop, that AddRange is better than sequential
Add operations and how XAt is better than X (RemoveAt instead of Remove etc.). A
function that specifies the index executes at fixed time, no matter how many items
you have in your list. Combined with graphs of how much slower certain operations
work when the size of the list grows, Gary really exhausted the subject. He also
went through sorting and how the various sorting algorithms perform and ended the
session by going through various collection types.

So, Gary knows a lot about collections, but I already knew about most of the stuff
and feel like I maybe should have attended another session. For people not knowing
about the various collections, though, I am sure it was a great run.