---
title:	"Getting started with NDepend 3"
date:	2010-10-07 12:00:00 +0100
categories: dotnet
tags: 	ndepend
---


After some time, I have finally got my thumb out and added an NDepend project to
one of my hobby project solution, to get some analyzing done before releasing it.

The first thing that hit me was how easy it was, to attach a new NDepend project
to my solution. I just had to:

- Install Visual NDepend
- Open my .NET Extensions solution
- Under the new "NDepend" main menu item, select "Attach new NDepend project to solution"
- Select all projects of interested (I chose all of them)
- Press OK

Once NDepend is added to your solution, the menu will change and look like this:

![NDepend system menu](/assets/img/blog/2010-10-07-1.png "NDepend system menu")

But before that, NDepend will perform a first-time analysis of all projects that
it asked to handle. This is done automatically as you bind NDepend to a solution.
It will perform the analysis, after which your default browser will come to life
and display an analysis summary, which is saved in a folder called *NDependOut*:

![NDependOut folder](/assets/img/blog/2010-10-07-2.png "The generated NDependOut folder")

Let's look at what the report has to say, shall we?



## Report sections

The NDepend report is divided into some sections, of which more (to me) are more
interesing than others:

![Report sections](/assets/img/blog/2010-10-07-3.png "The various sections of the NDepend report")



### Application metrics

First of all, a complete textual summary of all application metrics is presented:

![Application Metrics](/assets/img/blog/2010-10-07-4.png "Application Metrics summary")

This summary contains a couple of interesting metrics.

For instance, note the *comment ratio (51%)*. I have always taken great pride in
commenting my code, but lately I have focused on writing readable code instead :)

Since the analyzed solution mainly contains extension classes, I think that this
summary is quite what I expected, even if I maybe should have some more interfaces.

Note that not much is going on "under the hood". Almost everything is public (in
some cases for unit test purposes, which of course should be fixed).

Also, since I think one should NEVER work directly towards an object's fields, I
am happy that I have no public fields at all.

The last row displays the method/function with the worst *cyclomatic complexity*:

![Cyclomatic complexity](/assets/img/blog/2010-10-07-5.png "The worst *cyclomatic complexity")

However, when I analyze the method with the Visual Studio analyzer, it says that
it has a cyclomatic complexity of 13! Turns out that NDepend displays the *ILCC*,
which is the *Intermediate Language Code Complexity* - the resulting CC. However,
I will try to write a new blog post later on, in which I'll use this information
to improve the GetHtml() method, that has this high cc.



### Assembly metrics + abstraction/stability summary

After the application metrics come some assembly metrics (also quite interesting)
as well as information about for the stability of the different assemblies.

First of all, everything is presented a textual grid:

![Assembly metrics table](/assets/img/blog/2010-10-07-6.png "The NDepend Assembly metrics table")

This info is then displayed in various graphical components, such as the *Visual
NDepend* view (in Visual Studio, you can use NDepend to navigate this view):

![Visual NDepend](/assets/img/blog/2010-10-07-7.png "The Visual NDepend View")

...as well as the Abstractness vs. Instability view...

![Abstractness vs. Instability](/assets/img/blog/2010-10-07-8.png "The Abstractness vs. Instability view")

Now let's stop for a moment and discuss this graph. The word "instability" first
made me feel like I had written the worst piece of junk ever made in the history
of mankind, but I think that the word is quite misleading.

As I've mentioned, the analyzed solution mostly consists of extension and helper
classes, which are almost never independent. They mostly depend on other classes,
since that *is* their purpose. If I understand the term "instability" correctly,
this is what it means. The solution is unstable since it highly depends on other
components.

However, for this kind of solution, it is hard to have it any other way. After a
bit of reflecting over the graph and enjoying the green color (except for the so
far empty build project), I understood what view intends to display.



### Dependencies, build order etc.

The part of the report is probably a lot more interesting if you intend to delve
into a solution of which development you have not been a part of earlier on.

However, for this solution, this part of the report didn't give me anything that
I did not already know.



### Constraints

Finally, NDepend displays an amazing part, where the code is evaluated according
to all existing constraints, e.g.:

![Constraints](/assets/img/blog/2010-10-07-9.png "One of the vast number of constraint summaries")

This part displays a constraint that selects all functions that:

- Has more than 30 lines of code OR
- Has more than 200 IL instructions OR
- Has a cyclomatic complexity over 20 OR
- Has an IL cyclomatic complexity over 50 OR
- Has an IL nesting depth that is larger than 4 OR
- Has more than 5 parameters OR
- Has more than 8 variables OR
- Has more than 6 overloads

For instance, the first item in the list (*Split()*) is there, since it has more
than 8 variables.

Many default constraints are perhaps a little strict, but most are really useful.
Just having a look at these constraints and how your code applies to them, gives
you a deeper understanding of how you (or your team) writes code.



### Type metrics

Finally comes an exhausting, thorough grid, with ALL the information you can ask
for about every single type in the solution.

![Type metrics](/assets/img/blog/2010-10-07-10.png "Type metrics")

The "worst" cells in each category are highlighted, which makes it easy to get a
quick overview of the entire framework (although the information is massive).



## Conclusion

I have barely scratched the surface of what NDepend can offer, but to be able to
extract all this data by just pressing a button, is quite impressive.

I wrote this blog post yesterday, and have rewritten large parts of the solution
today. During that time span, my stance towards it has shifted a bit.

Yesterday, when I did not understand some parts of the report, I believed that a
hobby project was not the best context in which to use NDepend and that it comes
to better use when you work in a role (e.g. lead developer) that requires you be
able to quickly extract data about a system. In such a context, NDepend is great.

However, after taking some time to "feel" how NDepend feels for me as a developer,
I have started to see the benefits even for a solution like this. As I will show
in future blog posts, I can use the information I get from NDepend to detect the
worst parts of my framework and makes it easy to adjust them and re-analyze them
and watch my implementation grow better.

It is a bit like comparing my iPhone with my iPad. ReSharper was like my phone -
as soon as I started using it, I could not imagine being without it. NDepend, on
the other hand, is much like the iPad. At first, I really could not see the use,
but after some time, it finds it way into your day-to-day life.



