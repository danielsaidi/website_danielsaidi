---
title: Hide the close button of a WPF window
date:  2011-03-14 12:00:00 +0100
tags:  .net
icon:  dotnet
---

In a WPF application that I'm currently working on, I have to hide the close
button of a progress window to prevent users from closing it manually. Turns out
that it's complicated, but perfectly doable.

In the application, we want to be able to prevent the progress window from being
closed by  users, and instead close it once its related operation has finished.
However,  there doesn't seem to be a property or function for removing or disabling
the close button. At least I haven't found one.

You can however fix this yourself, with some obscure DLL hacking :)

First of all, define two constanst and two methods:

```csharp
private const int GWL_STYLE = -16;
private const int WS_SYSMENU = 0x80000;

[DllImport("user32.dll", SetLastError = true)]
private static extern int GetWindowLong(IntPtr hWnd, int nIndex);

[DllImport("user32.dll")]
private static extern int SetWindowLong(IntPtr hWnd, int nIndex, int dwNewLong);
```

Then, in the window class, call the imported DLL methods as such:

```csharp
var hwnd = new WindowInteropHelper(this).Handle;
SetWindowLong(hwnd, GWL_STYLE, GetWindowLong(hwnd, GWL_STYLE) &amp; ~WS_SYSMENU);
```

A convenient way to use this is to wrap the functionality in an extension method:

```csharp
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
```

If you know a more convenient way to hide the close button (perhaps I just didn't
find the correct property/method), reach out to me and I'll add it to the post.

