---
title:  "Hide the close button of a WPF window"
date:    2011-03-14 12:00:00 +0100
categories: dotnet
tags: 	wpf
---


In a WPF application that I am currently working with, I have to be able to hide
the close button of a progress window. Instead of being closed by the user (like
an alert window or a message box), this progress window should instead be closed
by its owner window once its related operation has finished.

So, how do you hide the close button. There is no such property around (at least,
I do not find one), so it seems you have to roll up your sleeves and do some DLL
importing :)

First of all, define two constanst and two methods:


	private const int GWL_STYLE = -16;
	private const int WS_SYSMENU = 0x80000;

	[DllImport("user32.dll", SetLastError = true)]
	private static extern int GetWindowLong(IntPtr hWnd, int nIndex);

	[DllImport("user32.dll")]
	private static extern int SetWindowLong(IntPtr hWnd, int nIndex, int dwNewLong);


Then, in the window class, call the imported DLL methods as such:


	var hwnd = new WindowInteropHelper(this).Handle;
	SetWindowLong(hwnd, GWL_STYLE, GetWindowLong(hwnd, GWL_STYLE) &amp; ~WS_SYSMENU);


The most convenient way to use this (in my opinion) is to wrap the functionality
within an extension method:


	public static class WindowExtensions
	{
	  private const int GWL_STYLE = -16;
	  private const int WS_SYSMENU = 0x80000;

	  [DllImport("user32.dll", SetLastError = true)]
	  private static extern int GetWindowLong(IntPtr hWnd, int nIndex);

	  [DllImport("user32.dll")]
	  private static extern int SetWindowLong(IntPtr hWnd, int nIndex, int dwNewLong);

	  public static void HideCloseButton(this Window window)
	  {
	     var hwnd = new WindowInteropHelper(window).Handle;
	     SetWindowLong(hwnd, GWL_STYLE, GetWindowLong(hwnd, GWL_STYLE) &amp; ~WS_SYSMENU);
	  }
	}


If you know a more convenient way to hide the close button (maybe I just did not
find the correct property/method?), please tell me in the comments below.

