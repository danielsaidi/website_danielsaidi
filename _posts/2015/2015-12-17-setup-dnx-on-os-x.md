---
title:  "Setup DNX on OS X"
date:   2015-12-17 08:48:00 +0100
tags:	.net .net-core automation
---

After so much waiting, so many "I'll do this first", so much app coding etc. etc.
(yep, I blame my family as well), I finally managed to start playing around with
DNX and ASP.NET 5.

For those of you who do not know the story, ASP.NET 5 will change everything...ish.
You can create your web applications, web api:s and console apps on OS X and Linux,
then run it everywhere. Nice, right?

Before I begin, I'll just mention some terms that I'll use in this post.

* **dnx** stands for *.NET Execution Environment*. It's a console application that
you get access to when installing ASP.NET 5. You use it to create web sites, run
your apps etc.

* **dnvm** stands for *.NET Version Manager*. It is used to install new versions of
.NET Core and the various tools it needs

* **dnu** stands for *DNX Utility*. It's a console application that you can use to
install dependencies for your projects, like *npm*.



## Step 1 - Install Mono

To setup DNX, [first install Mono](http://www.mono-project.com/download). You can
install it from the download page or by using [Homebrew](http://brew.sh/), which
is an amazing package manager for OS X.



## Step 2 - Install Visual Studio Code

Visual Studio Code is an open source IDE (well, not quite yet - for now, it is a
clean but powerful text editor rather than a full IDE), that can be used for all
kinds of projects. It was built with plugins in mind, so expect an explosion in
the upcoming months.

You do not have to use Visual Studio Code to write ASP.NET 5 code. You can any of
your favorite editors, like Sublime or Atom. However, I think you should at least
have a look at Visual Studio Code, since it is a really nice editor.

Visual Studio Code can be downloaded [here](https://go.microsoft.com/fwlink/?LinkID=534106)
or installed using [Homebrew Cask](https://caskroom.github.io/), which is an OS X
package manager for applications. 

If you are used to the full Visual Studio experience, you will notice that Visual
Studio Code is stripped. You can not even create new projects or solutions with it.
To create new projects, you must setup ASP.NET 5.



## Step 3 - Install ASP.NET 5

To install ASP.NET 5, download [this file](https://go.microsoft.com/fwlink/?LinkId=703940).
After installing ASP.NET 5, you must set it up. Now, brace yourself - you will no
longer rest in the comforting arms of install bundles. The rest of the setup will
take place in the terminal...which to some is a most welcome change.

Open the terminal, then type `dnx`. You will notice that the command will not be
recognized yet. To register it, run the following command:

```sh
source /Users/<your user name>/.dnx/dnvm/dnvm.sh
```

This will make it possible to run `dnx` and `dnu` from any folder. If you now run
the `dnx` command again, it will be recognized.



## Step 4 - (Optional) Install Yeoman

If you want to hack away with ASP.NET 5, you have all the tools you need (and an
optional Visual Studio Code as well). However, to simplify things even more, you
can use [Yeoman](http://yeoman.io) to generate projects. With Yeoman, you do not
have to setup each new project from scratch.

To install Yeoman and the ASP.NET project generator plugin, run these commands in
the terminal:

```sh
npm install -g yo
npm install -g generator-aspnet
```

After this, you can create ASP.NET projects with Yeoman, using this command from
any folder:

```sh
yo aspnet
```


This will open a wizard that lets you choose a project template from a selection
of different project types. Once you have selected a template, Yeoman will setup
the project for you in a sub folder with the same name as your project.



## Step 5 - Run your project

This post will not cover the project structure of ASP.NET 5 projects. However, to
sum it up, the following is true for your generated project:

* there are no solution files
* project files are now small
* the json files and project files have no references to the files in the project
* it's a beautiful thing

Once you have a project, `cd` into the project folder, then run `dnu restore` to
restore all dependencies (like npm install). Once all dependencies are restored,
you can run your project.

Depending on what kind of project you have, you run it in different ways:

* Console app - `dnx run`
* Web app - `dnx web`
* Web API - `dnx web`
* Test project - `dnx test`

Although you can run `dnx build` to build your project prior to running it, there
is no need for this, since the run commands builds the project as well.



## That's it!

That's about it. In a future post, I'll cover how to setup a code project with a
test project.