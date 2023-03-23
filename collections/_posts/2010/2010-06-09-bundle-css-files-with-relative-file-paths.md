---
title: Bundle CSS files with relative file paths
date:  2010-06-09 11:00:00 +0100
tags:  archive
icon:  php
---

I'm currently working on a css bundler, where aim to bundle files from different
folders into a single file. This post describes how I had to replace content
bundling with import bundling.

Previously, all css files within the selected folder (if any), as well as in a
mandatory system folder, were added to the virtual bundle file, where all the
css file content were added to the virtual file output as a single mega string:

```
.validation-error{border: solid 1px #bb4444;background: #ffdddd;}.RatingBar .images a{display: ....
```

The output is nice, clean and compact and can be compressed even further.

However, this approach, which can be found in bundlers all over the web, suffers
from a major flaw - file paths. Since virtual files don't exist in the same folder
as the source files, file paths may become invalid.

The approach above requires you to either use absolute paths or to have all your
css files at the same folder level. This is not always possible, though.

It is however possible, if you provide the bundler with file system information,
so that it can account for folder path differences, and modify the file paths so
that they work within the bundle file. However, this involves too much work for
me to be interested in implementing such a solution for now.

So, I took a step back and reimplemented the bundle implementation. The bundled
file still remains, but instead of adding file content to the output as above, I
instead import all files of interest into it. The virtual file output will thus
look something like this instead:

```
@import url("../css/default.css");@import url("../../wigbi/css2/x.css");@import url("../../wigbi/css2/y.css");
```

This is not as optimal as the topmost output, especially since I cannot compress
the code. However, by importing the files, I don't have to handle the file paths
within each file. It's a fair tradeoff, for now.