---
title: Trying out Plupload
date:	 2011-02-02 12:00:00 +0100
tags:  web
icon:  javascript

redirect_from: 
  - /blog/web/2011/02/02/trying-out-plupload
---

The team behind Tiny MCE has created a great upload component called `Plupload` that supports many runtimes, such as from jQuery-based HTML uploads, Flash, Silverlight, etc.

I decided to test Plupload in a project where I used a handy plugin called `FileUploadForm`, which can upload any number of files with a regular form, using AJAX.

As I yesterday started migrating the old plugin to the latest version, I thought "Three years have passed - there MUST be an easier way today, right?”. Plupload to the rescue!

You can tell Plupload which runtimes you’d prefer, the file types to support, etc. Users can then upload files with a regular “select file(s)” dialog or by dragging files into the form.

To make Plupload work with my project, I added some extra functionality, like starting and stopping the project engine and adjusting the target folder with a query string variable.

All in all, adding Plupload to my project took 10 minutes and works perfect.