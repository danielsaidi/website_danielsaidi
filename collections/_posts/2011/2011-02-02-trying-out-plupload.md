---
title: Trying out Plupload"
date:	 2011-02-02 12:00:00 +0100
tags:  web
icon:  javascript

redirect_from: 
  - /blog/web/2011/02/02/trying-out-plupload
---

The team behind Tiny MCE has created a great file upload component called
`Plupload`. It supports several runtimes – from jQuery-based uploads in HTML 4/5
to Flash, Silverlight, Gears etc.

I decided to test Plupload in a hobby project, where I used a handy plugin called
`FileUploadForm`, which could upload any number of files using AJAX. All you had
to do was to add a form to a page and it would handle the entire upload process
automatically.

However, as I yesterday sat down to migrate the old plugin so that it would work
with the new project version, I thought "Three years have passed - there MUST be
an even easier way to upload files today, right?”. Plupload to the rescue!

You can tell Plupload which runtimes you’d prefer, the file types to support etc.
Users can then upload files with a regular “select file(s)” dialog or by dragging
files into the upload form.

To make Plupload work with my project, I added it to my project and added some
extra functionality, like starting/stopping the project engine and adjusting the
target folder with a query string variable.

All in all, adding Plupload to my project took 10 minutes and works perfect.