---
title: Dependency Injection gone too far?
date:  2012-02-27 12:00:00 +0100
tags:  dependency-injection
icon:  avatar-sweat
---

I am currently working with a new version of a hobby console application project
of mine. The app will execute certain actions depending on the input arguments.

I'm now wondering if I am taking dependency injection too far in this project. I
will describe the project and its code structure and would love to hear your
thoughts on this.


## How does the application work?

The console application (Called Cloney) will do different things depending on the 
input arguments it is provided with. For instance, if I type

	cloney --clone --source=c:/MyProject --target=d:/MyNewProject

Cloney will clone the solution according to certain rules.

To keep the design flexible, I have introduced the concept of sub routines, which 
is a  class that implement the  `ISubRoutine` interface, which means that it can be 
executed using input arguments.

For instance, the main `CloneRoutine` only triggers if the correct arguments are 
provided. Another sub routine - `HelpRoutine` - will trigger if I type:

	cloney -–help

When I run the application, Cloney fetches all `ISubRoutine` implementation then
tries to execute each using the provided input arguments. Some may trigger, some
may not. If no sub routine triggers, Cloney displays a help message.


## The problem

Well, there's really no problem, just different ways to do things.

For instance, when I parse input arguments, I use a class called
`CommandLineArgumentParser`, which implements `ICommandLineArgumentParser`. This
class transforms the default argument array to a dictionary and makes it easy to
map an arg key to an arg value.

Each sub routine determines whether or not it wants to use this util. Since the
sub routine interface only defines the following method:

	bool Run(IEnumerable<string> args)

each sub routine is basically a program of its own. As far as the master program
is concerned, it just delegates the raw argument array to each sub routine. Then,
it's up to each sub routine to use it as it sees fit, or ignore any irrelevant
arguments that it doesn't need.


## The old design – DI for all (too much?)

Previously, the `CloneRoutine` class had two constructors:

	public CloneRoutine()
	: this(Default.Console, Default.Translator, Default.CommandLineArgumentParser, Default.SolutionCloner) { ... } 

	public CloneRoutine(IConsole console, ITranslator translator, ICommandLineArgumentParser argumentParser, ISolutionCloner solutionCloner) { ... }

Since sub routines are created using reflection, each routine must provide a
default constructor. Here, the default constructor uses a default implementation
of each interface, while the custom constructor is used by unit tests. Here, the
dependencies are fully exposed and pluggable.


## So, what is the problem?

Well, I just feel that since the input arguments are what defines what a routine
should do, injecting an argument parser makes the class unreliable. If I provide
the class with a parser that returns an invalid set of arguments, the class will
not do what I expect. Isn't that bad?


## The new design – DI where I think it’s needed (enough?)

Instead of the old design, would it not be better to do things like this:

	public CloneRoutine()
	: this(Default.Console, Default.Translator, Default.SolutionCloner) { ... }

	public CloneRoutine(IConsole console, ITranslator translator, ISolutionCloner solutionCloner) {
      ...
      this.argumentParser = Default.CommandLineArgumentParser;
      ...
   }

This way, I depend on an ICommandLineArgumentParser implementation that is wired
up in a `Default` class. If that implementation is incorrect, my unit tests will
break. The other three injections (IMO) are the ones that should be exchangable.

Is this good design, or am I doing something bad by embedding a hard dependency,
especially since all other component dependencies can be injected?


## Update 2017-04-05

I guess you're not supposed to look at what you wrote some years ago. I am sorry
for wasting bytes on the Internet with these lines of code.




