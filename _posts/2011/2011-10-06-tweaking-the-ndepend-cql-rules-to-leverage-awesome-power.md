---
title:  "Tweaking the NDepend CQL rules to leverage awesome power"
date:   2011-10-06 12:00:00 +0100
tags: 	.net ndepend cql
---


I have previously written about how to automate and schedule NDepend for several
.NET solutions at once. After getting into the habit of using it more regurarly,
the power of CQL has grown on me.

For instance, one big problem that I have wrestled with at work is that the code
contains static fields for non-static-should-be fields. In a web context. Enough
said.

Prior to CQL, I used to search for "static" in the entire .NET solution, then go
through the search result (which of course also did include valid static methods
and properties) and...well, it really did not work.

As I yesterday dug into the standard CQL rules, to get a better understanding of
the NDepend analysis tools, I noticed the following standard CQL:

	// <Name>Static fields should be prefixed with a 's_'</Name>
	WARN IF Count > 0 IN SELECT FIELDS WHERE 
	 !NameLike "^s_" AND 
	 IsStatic AND 
	 !IsLiteral AND 
	 !IsGeneratedByCompiler AND 
	 !IsSpecialName AND 
	 !IsEventDelegateObject 

	// This naming convention provokes debate.
	// Don't hesitate to customize the regex of 
	// NameLike to your preference.

Although NDepend's naming conventions do not quite fit my conventions, this rule
is just plain awesome. I just had to edit the CQL to

	// <Name>Static fields should not exist...mostly</Name>
	WARN IF Count > 0 IN SELECT FIELDS WHERE 
	 IsStatic AND 
	 !IsLiteral AND 
	 !IsGeneratedByCompiler AND 
	 !IsSpecialName AND 
	 !IsEventDelegateObject 

	// This naming convention provokes debate.
	// Don't hesitate to customize the regex of 
	// NameLike to your preference.

and voilá: NDepend will now automatically find all static fields in the solution
and ignore any naming conventions.

Since this got me going, I also went ahead to modify the following rule

	// <Name>Instance fields should be prefixed with a 'm_'</Name>
	WARN IF Count > 0 IN SELECT FIELDS WHERE 
	 !NameLike "^m_" AND 
	 !IsStatic AND 
	 !IsLiteral AND 
	 !IsGeneratedByCompiler AND 
	 !IsSpecialName AND 
	 !IsEventDelegateObject 

	// This naming convention provokes debate.
	// Don't hesitate to customize the regex of 
	// NameLike to your preference.

to instead require that fields are camel cased (ignoring the static condition as
well):

	// <Name>Instance fields should be camelCase</Name>
	WARN IF Count > 0 IN SELECT FIELDS WHERE 
	 !NameLike "^[a-z]" AND 
	 !IsLiteral AND 
	 !IsGeneratedByCompiler AND 
	 !IsSpecialName AND 
	 !IsEventDelegateObject

Two small changes to the original setup that are insanely helpful. Another great
thing is that when you edit the queries in `VisualNDepend`, you get an immediate,
visual feedback to how the rule applies to the entire solution.

So, now I can start tweaking the standard CQL rules to conform to my conventions.



