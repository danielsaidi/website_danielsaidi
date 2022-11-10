---
title: IIS 7 does not allow ports on Windows Vista
date:  2009-01-29 08:28:00 +0100
tags:  .net
icon:  dotnet

link: http://bvencel.blogspot.com/2008/05/aspnet-development-server-problems.html
---

After clean installing Windows Vista, Visual Studio 2005 and Visual Studio 2008 on my
work computer, I started having problems with using dynamic ports with ASP.NET.

When ASP.NET web applications are executed in VS 2005/2008, they may run on dynamic
ports on the local server, e.g. `localhost:12345`. This worked before the clean install,
but now I found myself being incorrectly redirected to `www.localhost.com:12345`.

After struggling with this, I eventually found a solution [here]({{page.link}}). If
you face the same problems as I did, I hope that it helps you out.