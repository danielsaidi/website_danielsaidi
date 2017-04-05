---
title:  "Trying out NDepend"
date:    2011-12-08 12:00:00 +0100
categories: dotnet
tags: 	ndepend coupling
---


I have been using NDepend to analyze the latest version of my NExtra library. The
code is spotless (this is where you should detect the irony), but the analysis is
highlighting some interesting design flaws that I should fix in the next version.

First of all, almost all cases where method or type names are too long are where
I have created facades or abstractions for native .NET entities, like Membership.
Here, I simply add exceptions where NDepend should not raise any warnings, since
the whole point with the facades are that they should mirror the native classes,
to simplify switching them out with your own implementations, mocks etc.

Second, NDepend warns for naming conventions that I do not agree with, e.g. that
method names should start with m_. Here, I simply remove the rules, since I don't
find them to be valid. However, after removing the rules, I realized that I could
have kept them and renamed them and made them check that my own conventions are
followed. I will probably do so later on.

Third, and the thing I learned most of today, was that I have circular namespace
dependencies, despite putting effort into avoiding circular namespace and assembly
dependencies. 

The cause of these circular dependencies was the X and X/Abstractions namespaces
I used to separate abstractions from implementations. What happens is that these
abstract interfaces define methods that use and return types and entities in the
base namespace. At the same time, implementations in the base namespace implement
the interfaces in the Abstractions namespace. There you go, circular dependencies.

In this particular case, I do not think that it is too serious, but it highlights
something that has annoyed me a bit while working with this library. In the unit
test projects, the test classes only knows about the abstract interfaces, but the
setup method either uses a concrete implementation or a mock for the interfaces.
This forces me to refer both the base as well as the abstractions namespace. Once
again, this is not that bad, but it raises a question about whether or not to do
a namespace merge in future versions.


