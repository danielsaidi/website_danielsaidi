---
title:  "Perform web.config transforms on build"
date: 	2013-04-04 09:29:00 +0100
tags: 	.net web
---


In a current project, we are auto-creating deploy packages of an ASP.NET MVC web
site, using Team City. When we do, we need to perform web.config transformations
so that a properly transformed configuration file ends up in the package.

When we create these deploy packages, we do not perform a web deploy, but rather
just builds the project with one of several available configurations. This could
cause problems, since the default behavior is that regular builds do not trigger
configuration transformations.

Luckily, you can trigger config transformations as part of a build process. What
we have done to achieve this, is to add the configuration transformation step as
a post build event. To get this to work, just follow these steps:

* First, open the web project project file in a text editor.
* Look for the following line:

```
<Import Project="$(MSBuildExtensionsPath)\$(MSBuildToolsVersion)\Microsoft.Common.props" Condition="Exists('$(MSBuildExtensionsPath)\$(MSBuildToolsVersion)\Microsoft.Common.props')" />
```

* Below that line, add this line: 

```
<UsingTask TaskName="TransformXml" AssemblyFile="$(MSBuildExtensionsPath)\Microsoft\VisualStudio\v10.0\Web\Microsoft.Web.Publishing.Tasks.dll" />
```

* Look for this commented out section:

```
<Target Name="AfterBuild"></Target>
```

* Uncomment that section and add the following inside it:

```
<MakeDir Directories="obj\$(Configuration)" Condition="!Exists('obj\$(Configuration)')" />
```

```
<TransformXml Source="web.config" Transform="web.$(Configuration).config" Destination="obj\$(Configuration)\Web.config" />
```

These last two lines make sure that a proper target folder is created in the obj
folder, then perform a config transformation and place the resulting file inside
this target folder. Now, whenever you perform a build, a transformed version of
the web.config will end up in the proper obj folder. Use it when you create your
deploy package and you are good to go.