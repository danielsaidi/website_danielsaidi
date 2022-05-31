---
title: Getting started with NDepend 3
date:  2010-10-07 12:00:00 +0100
tags:  .net code-quality
icon:  dotnet
---

After some time, I have finally got my thumb out and added an NDepend project to
one of my hobby project solution, to get some analyzing done. Let's have a look!

The first thing that hit me was how easy it was to attach NDepend to my solution.
I just had to:

- Install Visual NDepend
- Open my .NET Extensions solution
- Under the new "NDepend" main menu item, select "Attach new NDepend project to solution"
- Select all projects of interested (I chose all of them)
- Press OK

Once NDepend is added to your solution, the menu will change to look like this:

![NDepend system menu](/assets/blog/2010/10-07-1.png "NDepend system menu")

Before that, NDepend will perform a first-time analysis of all projects that it's
asked to handle. This is done automatically and will perform the analysis, after
which your default browser will come to life and display an analysis summary that
is saved in a folder called *NDependOut*:

![NDependOut folder](/assets/blog/2010/10-07-2.png "The generated NDependOut folder")

The report is divided into sections:

![Report sections](/assets/blog/2010/10-07-3.png "The various sections of the NDepend report")

Let's go through some of these sections.


### Application metrics

The Application Metrics section contains a complete textual summary of all application metrics:

![Application Metrics](/assets/blog/2010/10-07-4.png "Application Metrics summary")

This summary contains a couple of interesting metrics.

For instance, note the *comment ratio (51%)*. I have always taken great pride in
commenting my code, but have lately focused on writing readable code instead.

Since the analyzed solution mainly contains extension classes, I think that the
summary is quite what I expected. I should have some more interfaces, though.

Note that not much is going on under the hood. Almost everything is public, in
many cases for test purposes (which should be fixed). Also, since I you should
NEVER work directly towards an object's fields, I'm happy to have no public fields.

The last row displays the method/function with the worst *cyclomatic complexity*:

![Cyclomatic complexity](/assets/blog/2010/10-07-5.png "The worst *cyclomatic complexity")

However, the Visual Studio analyzer says that the cyclomatic complexity is 13! Turns
out that NDepend displays the *ILCC*, which is the *Intermediate Language Code Complexity*.


### Assembly metrics + abstraction/stability summary

After the application metrics come some assembly metrics (also quite interesting)
as well as information about for the stability of the different assemblies, presented
in a nice grid:

![Assembly metrics table](/assets/blog/2010/10-07-6.png "The NDepend Assembly metrics table")

This info is then displayed in various graphical components, such as the *Visual
NDepend* view (in Visual Studio, you can use NDepend to navigate this view):

![Visual NDepend](/assets/blog/2010/10-07-7.png "The Visual NDepend View")

...as well as the Abstractness vs. Instability view...

![Abstractness vs. Instability](/assets/blog/2010/10-07-8.png "The Abstractness vs. Instability view")

Now let's stop for a moment and discuss this graph. The word "instability" first
made me feel like I had written the worst piece of junk ever made, but the word
is quite misleading.

The solution mostly consists of extensions and helper classes, which are almost
never independent, since they by definition depend on other classes. If I have
understood the term "instability" correctly, it means that the solution highly
depends on other components.

However, for this kind of solution, it is hard to have it any other way. After a
bit of reflecting over the graph, it still gave me some insights to what view is
meant to display.


### Dependencies, build order etc.

The part of the report is probably a lot more interesting if you intend to delve
into a solution of which development you have not been a part of earlier on.

However, for this solution, this part of the report didn't give me anything that
I did not already know.


### Constraints

Finally, NDepend has an amazing section, where code is evaluated according to all
constraints, e.g.:

![Constraints](/assets/blog/2010/10-07-9.png "One of the vast number of constraint summaries")

This image displays a constraint that selects all functions that:

- Has more than 30 lines of code OR
- Has more than 200 IL instructions OR
- Has a cyclomatic complexity over 20 OR
- Has an IL cyclomatic complexity over 50 OR
- Has an IL nesting depth that is larger than 4 OR
- Has more than 5 parameters OR
- Has more than 8 variables OR
- Has more than 6 overloads

For instance, the first item in the list (*Split()*) is there because it has more
than 8 variables.

Many default constraints are perhaps a little strict, but most are really useful.
Having a look at them and how the code applies to them, gives you a deeper
understanding of the code you and your team writes.


### Type metrics

Finally comes an exhausting, thorough grid, with ALL the information you can ask
for about every single type in the solution. Get ready for Types Metrics.

![Type metrics](/assets/blog/2010/10-07-10.png "Type metrics")

The "worst" cells in each category are highlighted, which makes it easy to get a
quick overview of the entire framework (although the information is massive).


## Conclusion

I have barely scratched the surface of what NDepend can offer, but to be able to
extract all this data by just pressing a button, is quite impressive.

I first didn't understand some parts of the NDepend report and believed that a
hobby project like mine may not be the best in which to use NDepend, that it
comes to better use when you work in a lead role, where you manage a team. In
such a context, NDepend is absolutely amazing.

However, after some time with NDepend, I have started to see the benefits even
for a solution like mine. I can use the information I get from NDepend to detect
the worst parts of my framework and makes it easy to adjust them and re-analyze
them and watch my implementation grow better.

It's a bit like comparing my iPhone with my iPad. ReSharper was like my iPhone:
as soon as I started using it, I couldn't imagine being without it. NDepend, on
the other hand, is much like the iPad. At first, I couldn't see a clear use-case,
but after some time, it finds it way into your day-to-day life.