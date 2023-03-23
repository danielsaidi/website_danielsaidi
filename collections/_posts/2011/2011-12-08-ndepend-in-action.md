---
title: Various NDepend learnings
date:  2011-12-08 12:00:00 +0100
tags:  archive
icon:  dotnet
---


I've been using NDepend to analyze the latest version of my NExtra library.
The code is overall good, but the code analysis highlights some interesting
design flaws that I should fix in the next version.

First, almost all cases where NDepend warns that method or type names are
too long, are where I have facades or abstractions for native .NET entities
that can't be subclassed, like Membership. In this case, I add exceptions to
tell NDepend to not raise any warnings.

Second, NDepend warns for naming conventions that I disagree with, e.g. that
method names should start with m_. Here, I simply don't agree with NDepend
and just remove the rules. However, I later realized that I could instead have
kept tweaked rules to make them verify my own conventions instead.

Third, I strangely have circular namespace dependencies, despite putting effort
into avoiding it. The cause of these dependencies was certain namespaces that I
use to separate abstractions from implementations, where the interfaces use and
return types that are defined in the base namespace, while implementations in
the base namespace implement the interfaces in the `Abstractions` namespace. And
there you go - circular dependencies.

In this case, I don't think it's too serious, but it does highlights a thing that
has annoyed me while working with this library. In the unit test projects, the test
classes only knows about the abstract interfaces, but the setup method either uses
a concrete implementation or a mock for the interfaces. This forces me to refer both
the base as well as the abstractions namespace. I should perhaps redesign this, but
at the same time, it's a folder-local dependency loop that isn't too serious.