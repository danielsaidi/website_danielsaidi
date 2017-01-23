---
layout: post
title:  "Upgrade .NET Core RC to 1.0"
date:   2016-04-20 08:31:00 +0100
categories: software dotnet
tags: dotnet-core unit-tests xunit visual-studio-code
---


With the release of [Visual Studio Code 1.0](https://code.visualstudio.com/blogs),
I decided to upgrade the .NET Core software I had installed to the latest version.
However, the older versions were not properly replaced when upgrading, which did
cause Visual Studio Code and Omnisharp to behave quite strange.

After installing .NET Core 1.0 from [here](https://www.microsoft.com/net/core) and
upgrading Visual Studio Code from [here](https://code.visualstudio.com/Download),
I created a new .NET Core project with these lines:

{% highlight shell %}
mkdir hwapp
cd hwapp
dotnet new
dotnet restore
dotnet run
{% endhighlight %}

At first, the project seemed to run without any problems. However, when I opened
it in Visual Studio Code, I immediately received warnings that the project could
not load, that the project missed an .sln file, that Omnisharp could not find the
"default" runtime etc. etc.

After investigating this strange behavior, I found that older versions of dnx and
dnvm were not properly installed and conflicted with the new setup.

I tried to solve this by upgrading dnvm, removing all old versions and reinstalling
the latest versions, but this did not work.

In order to get the new setup to work, I also had to specify an alias:

{% highlight shell %}
dnvm update-self
dnvm list -detailed
dnvm uninstall VERSION -r coreclr
dnvm uninstall VERSION -r mono
dnvm install latest -r coreclr -alias default
dnvm install latest -r mono -alias default
{% endhighlight %}

After this, I could start Visual Studio Code and run Omnisharp without problems.
