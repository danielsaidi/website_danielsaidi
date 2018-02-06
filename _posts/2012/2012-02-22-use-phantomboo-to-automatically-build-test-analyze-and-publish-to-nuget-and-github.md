---
title:  "Use Phantom/Boo to build, test and publish to NuGet and GitHub"
date:    2012-02-22 12:00:00 +0100
tags: 	.net phantom boo github nuget
---


When developing my NExtra library, I used to handle the release process manually.
Since a release involves executing unit tests, bundling, zipping and uploading to
GitHub, creating new git tags etc. the process was time consuming and error-prone.

But things did not end there. After uploading a bew release to NuGet, I also had
to refresh and publish six NuGet packages. Since I used to use the NuGet Package
Explorer, I had to refresh the file and dependency specs for each package, which
took time, and added additional sources of human error to the process.

Since a release involved so many steps, I released quite seldom. Not a good sign.


## The solution

I realized that I had to do something. Unlike at work, where we use TeamCity for
all solutions, I found a build server to be a bit overkill. However, maybe I could
use a build script to automate the build and release process?

With this, I defined what the script must be able to help me out with:

- Build and test all projects in the solution
- Automatically extract the resulting version
- Create a release folder or zip with all files
- Create a new release tag and push it to GitHub
- Create a NuGet package for each project and publish to NuGet

The only piece of the process not covered by this was uploading the release file
to GitHub, but that would probably be easy once I had a script that automatically
generating a release zip for me.


## Selecting a build system

In order to handle the release process, I needed a build system. I decided to go
with Phantom, since I use it at work as well. It is a convenient tool (although a
new, official version would be nice), but left me with an annoying problem, which
I will describe further down.

I simply added Phantom 0.3 to a sub folder under the solution root. No config is
needed – the `build.bat` and `build.boo` (read on) files take care of everything.


## The build.bat file

build.bat is used to trigger a build, build a .zip or perform a full publish from
the command prompt. It is placed it in the solution root and looks like this:

	@echo off
	 
	:: Change to the directory that this batch file is in
	for /f %%i in ("%0") do set curpath=%%~dpi
	cd /d %curpath%
	 
	:: Fetch input parameters
	set target=%1
	set config=%2
	 
	:: Set default target and config if needed
	if "%target%"=="" set target=default
	if "%config%"=="" set config=release
	 
	:: Execute the boo script with input params - accessible with env("x")
	resources\phantom\phantom.exe -f:build.boo %target% -a:config=%config%
 

Those of you who read Joel Abrahamsson’s blog, probably recognize the first part.
These two lines moves the script to the folder that contains the `.bat` file and
executes from there.

The second section copies input parameters of interest. Here, the `target` param
determines the operation to launch (build, deploy, zip or publish). The `config`
parameter determibes what kind of build config to use (debug, release etc.)

The third section handles parameter fallback, in case some input parameters have
not been defined. This means that if I only provide `target`, `config` will fall
back to “release”. If I define no params at all, `target` will fall back to “default”.

Finally, the bat file calls phantom.exe, using the `build.boo` file. It tells it
to launch the provided “target” and provides “config” as an environment variable
(the `-a:config part`).

All in all, the build.bat file is really simple. It sets a target and config and
uses the values to trigger the build script.


## The build.boo file

The build.boo build script file is bigger than the .bat file. It is also located
in the solution root and looks like this:

	import System.IO
	 
	project_name = "NExtra"
	assembly_file = "SharedAssemblyInfo.cs"
	 
	build_folder = "_tmpbuild_/"
	build_version = ""
	build_config = env('config')
	 
	test_assemblies = (
	 "${project_name}.Tests/bin/${build_config}/${project_name}.Tests.dll",
	 "${project_name}.Web.Tests/bin/${build_config}/${project_name}.Web.Tests.dll",
	 "${project_name}.Mvc.Tests/bin/${build_config}/${project_name}.Mvc.Tests.dll",
	 "${project_name}.WPF.Tests/bin/${build_config}/${project_name}.WPF.Tests.dll",
	 "${project_name}.WebForms.Tests/bin/${build_config}/${project_name}.WebForms.Tests.dll",
	 "${project_name}.WinForms.Tests/bin/${build_config}/${project_name}.WinForms.Tests.dll",
	)
	  
	 
	target default, (compile, test):
	 pass
	 
	target zip, (compile, test, copy):
	 zip("${build_folder}", "${project_name}.${build_version}.zip")
	 rmdir(build_folder)
	 
	target deploy, (compile, test, copy):
	 with FileList(build_folder):
	 .Include("**/**")
	 .ForEach def(file):
	 file.CopyToDirectory("{project_name}.${build_version}")
	 rmdir(build_folder)
	 
	target publish, (zip, publish_nuget, publish_github):
	 pass
	  
	 
	target compile:
	 msbuild(file: "${project_name}.sln", configuration: build_config, version: "4")
	 
	 //Probably a really crappy way to retrieve assembly
	 //version, but I cannot use System.Reflection since
	 //Phantom is old and if I recompile Phantom it does
	 //not work. Also, since Phantom is old, it does not
	 //find my plugin that can get new assembly versions.
	 content = File.ReadAllText("${assembly_file}")
	 start_index = content.IndexOf("AssemblyVersion(") + 17
	 content = content.Substring(start_index)
	 end_index = content.IndexOf("\"")
	 build_version = content.Substring(0, end_index)
	 
	target test:
	 nunit(assemblies: test_assemblies, enableTeamCity: true, toolPath: "resources/phantom/lib/nunit/nunit-console.exe", teamCityArgs: "v4.0 x86 NUnit-2.5.5")
	 exec("del TestResult.xml")
	 
	target copy:
	 rmdir(build_folder)
	 mkdir(build_folder)
	 
	 File.Copy("README.md", "${build_folder}/README.txt", true)
	 File.Copy("Release-notes.md", "${build_folder}/Release-notes.txt", true)
	 
	 with FileList(""):
	 .Include("**/bin/${build_config}/*.dll")
	 .Include("**/bin/${build_config}/*.pdb")
	 .Include("**/bin/${build_config}/*.xml")
	 .Exclude("**/bin/${build_config}/*.Tests.*")
	 .Exclude("**/bin/${build_config}/nunit.framework.*")
	 .Exclude("**/bin/${build_config}/nsubstitute.*")
	 .ForEach def(file):
	 File.Copy(file.FullName, "${build_folder}/${file.Name}", true)
	 
	target publish_nuget:
	 File.Copy("README.md", "Resources\\README.txt", true)
	 File.Copy("Release-notes.md", "Resources\\Release-notes.txt", true)
	 
	 exec("nuget" , "pack ${project_name}\\${project_name}.csproj -prop configuration=release")
	 exec("nuget" , "pack ${project_name}.web\\${project_name}.web.csproj -prop configuration=release")
	 exec("nuget" , "pack ${project_name}.mvc\\${project_name}.mvc.csproj -prop configuration=release")
	 exec("nuget" , "pack ${project_name}.wpf\\${project_name}.wpf.csproj -prop configuration=release")
	 exec("nuget" , "pack ${project_name}.webforms\\${project_name}.webforms.csproj -prop configuration=release")
	 exec("nuget" , "pack ${project_name}.winforms\\${project_name}.winforms.csproj -prop configuration=release")
	 
	 exec("nuget push ${project_name}.${build_version}.nupkg")
	 exec("nuget push ${project_name}.web.${build_version}.nupkg")
	 exec("nuget push ${project_name}.mvc.${build_version}.nupkg")
	 exec("nuget push ${project_name}.wpf.${build_version}.nupkg")
	 exec("nuget push ${project_name}.webforms.${build_version}.nupkg")
	 exec("nuget push ${project_name}.winforms.${build_version}.nupkg")
	 
	 exec("del *.nupkg")
	 exec("del Resources\\README.txt")
	 exec("del Resources\\Release-notes.txt")
	 
	target publish_github:
	 exec("git add .")
	 exec('git commit . -m "Publishing ${project_name} ' + "${build_version}" + '"')
	 exec("git tag ${build_version}")
	 exec("git push origin master")
	 exec("git push origin ${build_version}")


Topmost, we see a `System.IO` import. This allows us to use `System.IO` for file
operations. After that, I define some variables and list test assemblies to test.

Two variables worth mentioning is `build_version`, which is set in the `compile`
step, as well as `build_config`, which is set by the input parameter in build.bat.

The next section of the file defines all public targets, that are intended to be
callable by the user. These map directly to target in build.bat.

If we look at the public targets, we have:

- default – Executes “compile” and “test”
- zip – Executes “compile” and “test”, then creates a zip file
- deploy – Executes “compile” and “test” then creates a folder
- publish – Executes “zip”, then publishes to NuGet and GitHub

If we look at the private targets (that do the real work) we have:

- compile – Compiles the solution and extract the version number
- test – Runs the NUnit builtin with the .NExtra test assemblies
- copy – Copies all relevant files to the temporary build_folder
- publish_nuget – Pack and publish each .NExtra project to NuGet
- publish_github – Commit all changes, create a tag then push it

It is not that complicated, but it is rather much. You could take these two files
and tweak them, and they would probably work for your project as well.

However, read on for some hacks that I had to do to get the build process working
as smooth as it does.


## One assembly file to rule them all

I recently decided to extract common information from each NExtra project into a
shared assembly file. The shared assembly file looks like this:

	using System.Reflection;
	 
	// General Information about an assembly is controlled through the following
	// set of attributes. Change these attribute values to modify the information
	// associated with an assembly.
	[assembly: AssemblyCompany("Daniel Saidi")]
	[assembly: AssemblyProduct("NExtra")]
	[assembly: AssemblyCopyright("Copyright © Daniel Saidi 2009-2012")]
	[assembly: AssemblyTrademark("")]
	 
	// Make it easy to distinguish Debug and Release (i.e. Retail) builds;
	// for example, through the file properties window.
	#if DEBUG
	[assembly: AssemblyConfiguration("Debug")]
	#else
	[assembly: AssemblyConfiguration("Retail")]
	#endif
	 
	// Version information for an assembly consists of the following four values:
	//
	// Major Version
	// Minor Version
	// Build Number
	// Revision
	//
	// You can specify all the values or you can default the Build and Revision Numbers
	// by using the '*' as shown below:
	[assembly: AssemblyVersion("2.6.3.4")]
	[assembly: AssemblyFileVersion("2.6.3.4")]

The file defines shared assembly information like version etc. By using a shared
file, I can specify shared properties that applies to all projects in one place.
I then link this file into each project and then remove the information from the
project specific assembly info file.

Since the NExtra version management is a manual process (the way I want it to be),
I manage the library version here and parse the file during the build process to
retrieve the version number. The best way would be to use `System.Reflection` to
analyze the library files, but this does not work, since Phantom uses .NET 3.5.

I tried recompiling Phantom to solve this, but then other things started to crash.
So...the file parse approach is ugly, but works.


## Tweaking NuGet

After installing `NuGet`, typing "nuget" in the command prompt still triggered a
warning message, since "nuget" was unknown. To solve this, either add the NuGet
executable path to PATH or be lazy and use the nuget command line bootstrapper,
which finds NuGet for you. You can download it from CodePlex or grab it from the
NExtra Resources root folder.

Regarding each project’s nuspec file, they were easily created by calling `nuget
spec x` where x is the path to the project file. A nuspec file is then generated.

I then added some information that cannot be extracted from the assembly, like a
project URL, icon etc. for each of these generated spec files.


## Conclusion

This post became a rather long, but I hope it did explain my way of handling the
NExtra release process.

Using the build script, I can now call build.bat in the following ways:

- `build` – build and test the solution
- `build zip` – build and test the solution and generate a nextra.<version>.zip file
- `build deploy` – build and test the solution and generate a nextra.<version>.zip folder
- `build publish` – the same as build zip, but also publishes to NuGet and GitHub.

The build script has saved me a lot of work. It saves me time, increases quality
by reducing the amount of manual work and makes releasing new versions a breeze.

I still have to upload the zip file to the GitHub download area, but I find this
to be a small task compared to all other steps. Maybe I’ll automate this one day,
but this will do for now.

I strongly recommend all projects to use a build script, even for small projects
where a build server is a bit overkill.

Automating the release process is a ticket to heaven. Or very close to that.

