---
title:	"Bundle CSS files with relative file paths"
date:	2010-06-09 11:00:00 +0100
tags: 	css php web
---


I am currently working on a css bundler, where I have to be able to bundle files
from many different folders.

Previously, all css files within the selected folder (if any), as well as within
a mandatory system folder, were added to the virtual bundle file. The engine did
parse all files and add them to the virtual file output as one mega string:

	.validation-error{border: solid 1px #bb4444;background: #ffdddd;}.RatingBar .images a{display: ....

The output is nice, clean and compact and could be compressed even further.

However, this approach, which can be found in bundlers all over the web, suffers
from a major flaw - file paths. Since the virtual files do not exist at the same
folder level in the file system as the parsed files, all paths in the bundle file
will be invalid. This is especially true when handling files from many different
folders.

The approach above requires you to either use absolute paths or to have all your
css files at the same folder level. This is not always possible, though, since I
want to allow developers to point out any folder as css folder.

It is possible, however, if you provide the bundler with file system information,
so that it can account for folder path differences, and modify the file paths so
that they work within the bundle file. This, however, involves way too much work
for me to be interested in implementing such a solution...at least now.

So, I took a step back. The bundle file still remains, but instead of adding all
file content to the output as above, I just import all files of interest into it
instead. The virtual file output will thus look something like this:

	@import url("../css/default.css");@import url("../../wigbi/css2/x.css");@import url("../../wigbi/css2/y.css");

This is not as optimal as the topmost output, especially since I cannot compress
the code. However, by importing the files, I don't have to handle the file paths
within each file. A farily decent tradeoff...for now.