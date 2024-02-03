---
title: HTML5 localstorage
date:  2010-12-09 12:00:00 +0100
tags:  javascript
icon:  javascript
---

I have spent some time experimenting with the new HTML 5 `localStorage` feature, 
which is a new feature that simplifies storing data in the browser using JavaScript.

For instance, say that you have a web application that needs to uniquely identify
the device. The device itself doesn't send information like the MAC address, phone
number etc. so, you’re at a loss.

You can then easily create a unique ID and store it in local storage. The browser
can then use the persisted to identify itself consistently until the user clears
the local storage.

This is all you need to store a small string in local storage:

```javascript
localStorage.setItem("name", "Daniel");
var name = localStorage.getItem("name");
localStorage.removeItem("name");
```

Local storage does NOT replace a real database, since it's unique for each browser
and can't hold large amounts of data. It's however great for smaller pieces of data.