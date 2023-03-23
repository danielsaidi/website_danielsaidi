---
title: Perform web.config transforms on build
date:  2013-04-04 09:29:00 +0100
tags:  archive
icon:  dotnet
---

In a current project, we are auto-creating deploy packages of an ASP.NET MVC web
site, using Team City. When we do, we need to perform web.config transformations
to ensure that a properly configured file ends up in the deployed package.

When we create these deploy packages, we don't perform a web deploy, but instead
build the project with one of several available configurations. This could cause 
problems, since the default behavior is that regular builds don't trigger config
transformations.

Luckily, you can trigger config transformations as part of a build process. To
achieve this, just add the configuration transformation step as a post build event:

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

These last two lines make sure that a proper target folder is created in the `obj`
folder, then perform a config transformation and place the resulting file inside
this target folder. 

Now, whenever you perform a build, a transformed version of the web.config will end
up in the proper obj folder. Use it when you create your deploy package and you are 
good to go.