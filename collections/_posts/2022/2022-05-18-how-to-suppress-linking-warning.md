---
title:  How to fix 'linking against a dylib which is not safe for use in application extensions' warning
date:   2022-05-18 10:00:00 +0000
tags:   xcode spm

icon:   swift
assets: /assets/blog/22/0518/

tweet:  https://twitter.com/danielsaidi/status/1526890018819788805?s=20&t=b2pADt7urhtlz3IJuo66LA
---

Swift packages is a powerful tool for separating your code into modules. However, Xcode currently shows a `linking against a dylib which is not safe for use in application extensions` warning when linking packages in certain ways, even when a package is safe.

Let's consider a test app that's called `MyApp`, that has a main app target, a `MyAppWidget` widget extension target and a `MyAppFramework` framework target:

![Xcode project with an app, a widget and a framework target]({{page.assets}}project.png){:class="plain"}

Let's create a new package called `MyPackage` in a sub folder and add it as a local package. The new package has no logic and doesn't depend on any extension unsafe api:s.

![Xcode project with a local package]({{page.assets}}package.png){:class="plain"}

Since the package doesn't use any unsafe api:s, we can link our widget extension to it and build the project without getting any errors or warnings:

![Xcode widget with a linked local package]({{page.assets}}widget.png){:class="plain"}

We can even check "Allow app extension API only" in our framework, link it to the package and build the project without getting any errors or warnings:

![Xcode framework with a linked local package]({{page.assets}}framework.png){:class="plain"}

We can also link the main app target or the widget to either `MyAppFramework` or `MyPackage` without getting any errors or warnings:

![Xcode app with linked framework]({{page.assets}}app-with-framework.png){:class="plain"}

However, if we link the app or widget to both `MyAppFramework` and `MyPackage`, we get a `linking against a dylib which is not safe for use in application extensions` warning:

![Xcode app with linked framework and package]({{page.assets}}app-with-framework-and-package.png){:class="plain"}

Since this warning is incorrect for our package, I'd like to remove it to avoid bloating the project with incorrect or irrelevant warnings. We should only have warnings that can be acted on, and aim to fix all warnings.

To suppress the warning, you can add `$(inherited) -Xlinker -no_application_extension` to "other linker flags" for any target that causes this warning.

In this case, we can add it to the framework and widget to make this warning go away:

![Framework with other linker flags]({{page.assets}}linker-flags.png){:class="plain"}

However, this is not recommended, since the warning *may* be valid. We must not suppress warnings for things that can actually break your app.

Another alternative is to add this information to the package itself, instead of suppressing the warning in the project. You can do this by specifying `linkerSettings` for the package:

```
linkerSettings: [.unsafeFlags(["-Xlinker", "-no_application_extension"])])
```

This will make the package file look something like this:

![Package with linker settings]({{page.assets}}package-linker-settings.png){:class="plain"}

This will make the warnings go away, which means that you don't have to suppress them in the project. Just make sure your package is actually safe for extensions before you do this.