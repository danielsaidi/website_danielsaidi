---
layout: post
title:  "IIS 7 does not allow ports on Windows Vista"
date:   2009-01-29 08:28:00 +0100
categories: software dotnet
tags: windows iis visual-studio
---


After clean installing Windows Vista on my work computer, then installing Visual
Studio 2005 and Visual Studio 2008, I suddenly faced problems with using dynamic
ports when developing web applications in ASP.NET.

When web applications are executed in VS 2005/2008, they may run on dynamic ports
on the development server, e.g. *localhost:12345*. This worked before my computer
was clean installed. After installing, however, I found myself being redirected
to *www.localhost.com:12345*.

After struggling with this for a little bit, I eventually found the solution on
[this page](http://bvencel.blogspot.com/2008/05/aspnet-development-server-problems.html).

If you are facing the same problems as I did, I hope that this helps you out.