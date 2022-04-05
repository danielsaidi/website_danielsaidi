---
title: / in URL query makes CSS @import and file paths fail
date:  2010-06-09 12:00:00 +0100
tags:  css php
---

I am currently implementing css file bundling with virtual paths in php and am
facing a problem, where slashes in the url query make file imports fail.

The css file bundling writes a virtual path to a php file, as such:

```
~/bundle/bundle.css&cssFolders=... => ~/bundle/bundle.php&type=css&cssFolders=...
```

Since I provide paths to my css folders, a bundle url may look like this:

```
../bundle/bundle.css&cssFolders=css/,shared/css/
```

The bundle file then builds the resulting css content by importing all files that
it finds, for instance:

```
@import url("../css/x.css");
@import url("../shared/css/y.css");
```

This solution only works in some cases and fails for others. I was confused before
I found the solution.

The / chars in the URL query makes the @import statement fail, since it seems to
count all / in the URL, not just all the / in the path excluding the query. 

This is understandable, since css files are not meant to handle query variables,
but in this case, we have a *virtual* css file that handles query variables, which
is why we need to solve this problem.

The solution, in this case, is to count the number of / in the query variable
`cssFolders` and add an extra ../ for each additional / in the import statements.