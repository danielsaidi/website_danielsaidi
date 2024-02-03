---
title: Øredev 2011 in the rear-view mirror – Part 1
date:  2011-11-23 12:00:00 +0100
categories: conferences
tags:  conferences
icon:  avatar
---

Two weeks ago, I attended to the Øredev Developer Conference in Malmo, Sweden. I
was there from Wednesday to Friday and it was truely inspiring. In this summary,
I will write about sessions I attended, sessions I missed and a few sessions I
heard a lot of good stuff about.

I have deliberately waited two weeks with this summary, since I want it to be an
objective summary and not one caught up in any conference hype. I will sum up the
sessions over a couple of days, so my summary will consist of a number of posts.


## KEYNOTE: Alexis Ohanian - Only your mom wants to use your website

After an early morning flight, we arrived a bit too late for the morning keynote
with Reddit co-founder Alexis Ohanian. The doors to the keynote were closed, but
it was playing outside, so we managed to catch a glimpse of it.

Alexis talked about how to convince people that your product is worth their time
and how companies tend to ignore users experience, thus their customers. He had
some online examples that we have gotten used to as Internet users, such as how
airline ticket search engines return a huge amount of tickets, where most are not
what we want.

I will watch the recording once it is up to see the presentation in full. People
who attended it told me it was good, although Jeff Atwood threw Reddit some shade
during his Friday keynote. More on that later.


## Jon Skeet - C#5 Async 101

This talk was one of many, where a distinct British, Scottish or American accent, 
with an almost over-the-top engagement, seemed to be two of the key-criteria to be 
invited as a speaker. Jon was very inspiring, though, and did demonstrate the new
async keyword, which will be shipped in C# 5.

Jon talked about how async operations have been around since back in .NET 1, but
how they have been jumpy and resulted in spaghetti code. The new `async` and
`await` keywords seems like a clean way to write async code. The resulting code
reads just like non-async code, with new keywords.

Another cool thing is that all `await`s are potential pause points. If .NET has
all the data it needs to provide the caller with a result, it will do so without
going async. Jon then showed us these new features in action and finished off by
pointing out that `async != parallell`.


## Gary Short - .NET 4.0 Collection Classes Deep Dive

I found this talk to be really interesting as it contains a lot of performance 
related information that's worth knowing in the daily life of a system developer.

Gary talked a lot about list operations. For instance, when you add your first
item to a list, its capacity grows to 4. When that's no  longer enough, it grows
to 8, then 16, then 32. Gary suggested to instead initialize the list with an int
parameter, e.g. new List<string>(10), which will cause it to grow with fixed steps.

Gary also told us to not use `Add` in a loop, since `AddRange` is better and how 
`XAt` is better than X (`RemoveAt` instead of `Remove` etc.). A function that
specifies the index executes at fixed time, no matter how many items you have in
your list. With graphs of how much slower certain operations work when the size
of the list grows, Gary really exhausted the subject.

Gary also went through sorting and how the various sorting algorithms perform and
ended the session by going through various collection types. 

I already knew most of the stuff and feel like I maybe should have attended another
session.  For people not knowing about the various collections, though, I'm sure it
was a great talk.