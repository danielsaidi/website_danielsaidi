---
title:  "Visual Studio and WPF force Windows 7 to apply basic theme"
date:   2011-06-27 12:00:00 +0100
tags: 	.net
---


Each time I open up my WPF projects in Visual Studio (2010) and open a XAML file,
my computer switches from the nice semi-transparent theme to Windows 7 Basic.

If this does happen to you too, this is how to solve it:

- In the main menu, select “Tools/Options”
- Under “Environment/General”, uncheck “Automatically adjust…” and “Use hardware acceleration…”
- Enjoy the Windows 7 flashy theme 🙂

Easy, right? Rumors says that it is R# that is causing this issue, but I do love
JetBrains and their God-like skills way too much to even consider that to be true.