---
title: TeamCity 6.5.1 does not play well with NServiceBus
date:  2011-09-08 12:00:00 +0100
tags:  archive

post: http://tech.groups.yahoo.com/group/nservicebus/message/6790
---

I am currently moving some projects from an old TeamCity 5.1.2 server to a brand
new 6.5.1 server. Everything has been going great, until I tried moving a project
that uses NServiceBus.

When TeamCity tries to launch tests for NServiceBus, it fails with the following
error message:

	[xxx.Test_Name] Test(s) failed. System.InvalidOperationException : Could not load C:\TeamCity\BuildAgent3\work\c53933ba5b3b087f\xxx\bin\release\xxx.XmlSerializers.dll. Consider using ‘Configure.With(AllAssemblies.Except(“xxx.XmlSerializers.dll”))’ to tell NServiceBus not to load this file. —-> System.BadImageFormatException : Could not load file or assembly ‘file:///C:\TeamCity\BuildAgent3\work\c53933ba5b3b087f\xxx.Tests\bin\release\xxx.XmlSerializers.dll’ or one of its dependencies. This assembly is built by a runtime newer than the currently loaded runtime and cannot be loaded. at NServiceBus.Configure.GetAssembliesInDirectoryWithExtension(String path, String extension, String[] assembliesToSkip) in d:\BuildAgent-02\work\672d81652eaca4e1\src\config\NServiceBus.Config\Configure.cs:line 213 at NServiceBus.Configure.<GetAssembliesInDirectory>d__3.MoveNext() in d:\BuildAgent-02\work\672d81652eaca4e1\src\config\NServiceBus.Config\Configure.cs:line 190 at System.Linq.Buffer`1..ctor(IEnumerable`1 source) at System.Linq.Enumerable.ToArray[TSource](IEnumerable`1 source) at NServiceBus.Configure.With(String probeDirectory) in d:\BuildAgent-02\work\672d81652eaca4e1\src\config\NServiceBus.Config\Configure.cs:line 101 at NServiceBus.Configure.With() in d:\BuildAgent-02\work\672d81652eaca4e1\src\config\NServiceBus.Config\Configure.cs:line 75 at NServiceBus.Testing.Test.Initialize() in d:\BuildAgent-02\work\672d81652eaca4e1\src\testing\Test.cs:line 20 at xxx.Setup() in c:\TeamCity\BuildAgent3\work\c53933ba5b3b087f\xxx.Tests\xxxTests.cs:line 17 –BadImageFormatException at System.Reflection.Assembly._nLoad(AssemblyName fileName, String codeBase, Evidence assemblySecurity, Assembly locationHint, StackCrawlMark& stackMark, Boolean throwOnFileNotFound, Boolean forIntrospection) at System.Reflection.Assembly.nLoad(AssemblyName fileName, String codeBase, Evidence assemblySecurity, Assembly locationHint, StackCrawlMark& stackMark, Boolean throwOnFileNotFound, Boolean forIntrospection) at System.Reflection.Assembly.InternalLoad(AssemblyName assemblyRef, Evidence assemblySecurity, StackCrawlMark& stackMark, Boolean forIntrospection) at System.Reflection.Assembly.InternalLoadFrom(String assemblyFile, Evidence securityEvidence, Byte[] hashValue, AssemblyHashAlgorithm hashAlgorithm, Boolean forIntrospection, StackCrawlMark& stackMark) at System.Reflection.Assembly.LoadFrom(String assemblyFile) at NServiceBus.Configure.GetAssembliesInDirectoryWithExtension(String path, String extension, String[] assembliesToSkip) in d:\BuildAgent-02\work\672d81652eaca4e1\src\config\NServiceBus.Config\Configure.cs:line 204

Looking at the error, I really wonder where `d:\BuildAgent-02 comes` from, since
it doesn't exist on the old server and not on the new one either.

After reading up a bit, it seems that it is an NServiceBus issue, similar to the
one described [here]({{page.post}}). I have installed VS2010 and SP1 and it still
doesn't work, so I'm out of ideas.

Any ideas out there?