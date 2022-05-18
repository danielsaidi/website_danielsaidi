---
title:  How to suppress 'linking against a dylib which is not safe for use in application extensions' warning
date:   2022-05-18 10:00:00 +0000
tags:   xcode swift

icon:   swift
assets: /assets/blog/2022/2022-05-18/
---

Swift packages is an easy and powerful tool for separating your code into modules. However, Xcode currently shows a `linking against a dylib which is not safe for use in application extensions` warning when linking targets and packages in certain ways, even though a package is extension-safe. Let's look at how we can suppress this warning.

Let's consider a test app project that's called `MyApp`, that has a main app target, a `MyAppWidget` widget extension target and a `MyAppFramework` framework target:

![Xcode project with an app, a widget and a framework target]({{page.assets}}project.png){:class="plain"}

Let's then create a new package called `MyPackage` in a sub folder and add it as a local package. The package has no logic and doesn't depend on any extension unsafe api:s.

![Xcode project with a local package]({{page.assets}}package.png){:class="plain"}

Since the package doesn't use any extension unsafe api:s, we can link our widget extension to it and build the project without getting any errors or warnings:

![Xcode widget with a linked local package]({{page.assets}}widget.png){:class="plain"}

We can even check "Allow app extension API only" for our framework, link it to the package and build the project without getting any errors or warnings:

![Xcode framework with a linked local package]({{page.assets}}framework.png){:class="plain"}

We can also link the main app target or the widget to either `MyAppFramework` or `MyPackage` without getting any errors or warnings:

![Xcode app with linked framework]({{page.assets}}app-with-framework.png){:class="plain"}

However, if we now link the main app target or the widget to both `MyAppFramework` and `MyPackage`, we get a `linking against dylib not safe for use in application extensions` warning:

![Xcode app with linked framework and package]({{page.assets}}app-with-framework-and-package.png){:class="plain"}

We can't specify "Allow app extension API only" for Swift packages, but we also shouldn't have to, since the packages don't contain any unsafe code. I have tried finding a solution to this without finding one.

Since this warning is incorrect, at least from a package perspective, and I prefer to have as few warnings as possible to distract from things that really need attention, I'd like to find a way to remove this warning. Since there doesn't seem to be a way to fix it, perhaps we can just suppress it?

Just a word of warning before we continue. Only consider suppressing this warning if you are absolutely sure about the code you pull in. You don't want to suppress warnings for things that could actually break your app due to problems in the code.

To suppress this warning, you can add `$(inherited) -Xlinker -no_application_extension` to "other linker flags" for any target that causes this warning. In our case, we can add it to the framework and widget to make the warning go away:

![Framework with other linker flags]({{page.assets}}linker-flags.png){:class="plain"}


## Conclusion

Swift packages is an easy and powerful way to separate your code into modules, but Xcode currently seem to throw invalid warnings when linking packages and targets in an Xcode project. 

Until Apple fixes this problem, you can use linker flags to suppress these warnings. Just make sure that you don't suppress warnings that are actually accurate and need to be fixed.