---
title: Flush the Visual Studio Template Cache
date:  2011-01-14 12:00:00 +0100
tags:  archive
icon:  dotnet
---

I recently created a project template that uses the Spark view engine
instead of the default Web Forms view engine. However, it didn't show
up in the project template list. Time for a template cache flush.

If your Visual Studio project templates don't show up in Visual Studio,
even though you've added them correctly, Visual Studio may be presenting
a cached collection.

If this happens, you can flush the `Visual Studio Template Cache` to
make Visual Studio update the list of available templates.

This is easy to do. In the command prompt, just run the following command:

```
devenv /installvstemplates
```

If your command prompt doesn't find `devenv`, the .exe file should be
in a folder like:

```
C:\Program Files (x86)\Microsoft Visual Studio 10.0\Common7\IDE>
```

After flushing the cache, open Visual Studio and choose to create a new
project. Your new templates should now appear in the list.