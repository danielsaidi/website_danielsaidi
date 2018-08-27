---
title:	"Trying out Plupload"
date:	2011-02-02 12:00:00 +0100
tags: 	web
redirect_from: 
  - /blog/web/2011/02/02/trying-out-plupload
---


In a hobby project of mine, I had a really handy UI plugin called FileUploadForm,
that could upload any number of files with AJAX. All you needed to do was to add
such a form to the page to have it handle the entire upload process automatically.

However, as I yesterday sat down to migrate the old plugin so that it would work
with the new project version, I thought "Three years have passed - there MUST be
an even easier way to upload files today, right?”. Believe it or not, there was.

The team behind Tiny MCE have created a really nice file upload component called
`Plupload`. It supports several runtimes – from jQuery-based uploads in HTML 4/5
to Flash, Silverlight, Gears etc. It is insanely easy to configure.

You can tell Plupload which runtimes you’d prefer, the file types to support etc.
The users can then upload files either with a regular “select file(s)” dialog or
by dragging files from an Explorer/Finder window.

To make Plupload work with my project, I moved the upload file to ~/wigbi/pages/
and added some extra functionality, like starting/stopping the project engine and
adjusting the target folder with a query string variable.

All in all, adding Plupload to my project took 10 minutes and works perfect.