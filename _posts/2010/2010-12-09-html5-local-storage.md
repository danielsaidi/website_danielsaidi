---
title:	"Make Combres work with ASP.NET"
date:	2010-12-09 12:00:00 +0100
categories: web
tags: 	html5 local-storage
---


I have spend some time experimenting with the new HTML 5 `localStorage` feature.
It is a new feature that simplifies storing data in a browser, using JavaScript.

Local storage does NOT replace a real database, since it is unique for a browser
and can not hold that large amounts of data, but it is great for smaller tasks.

For instance, say that you have an iPhone web application that needs to uniquely
identify the mobile device. The device itself does not send any such information
(like the MAC address, phone number etc.) so, you’re at a loss.

With localStorage, however, you can easily create a unique ID (e.g. a GUID) then
store it in local storage. The browser can then use the ID to identify itself.

For instance, this is all you need to store a small string in localStorage:

	localStorage.setItem("name", "Daniel");
	var name = localStorage.getItem("name");
	localStorage.removeItem("name");

As you can see, it’s really easy and quite powerful.