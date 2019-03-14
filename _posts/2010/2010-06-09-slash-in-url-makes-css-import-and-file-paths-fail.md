---
title:	"/ in URL query makes CSS @import and file paths fail"
date:	2010-06-09 12:00:00 +0100
tags: 	css php web
---


I am currently implementing CSS file bundling in a way that uses a virtual path
to a PHP file, as such:

	~/bundle/bundle.css&cssFolders=... => ~/bundle/bundle.php&type=css&cssFolders=...

Since I provide paths to my CSS folders, a bundle URL may look like this:

	../bundle/bundle.css&cssFolders=css/,shared/css/

My bundle file then builds the resulting CSS content by importing all files that
it finds, for instance:

   @import url("../css/x.css");
   @import url("../shared/css/y.css");

The solution, however, did only work in some cases, and failed for others. I was
confused before I found the solution.

The / chars in the URL query makes the @import statement fail, since it seems to
count all / in the URL, not just all the / in the path excluding the query. This
is quite understandable, since css files are not meant to handle query variables.

In this case, however, we have a *virtual* css file that handles query variables,
which is why we need to solve this problem.

The solution, in this case, is to count the number of / in the query variable
`cssFolders` and add an extra ../ for each additional / in the import statements.