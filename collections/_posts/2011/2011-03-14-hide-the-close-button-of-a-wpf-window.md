---
title: Hide the close button of a WPF window
date:  2011-03-14 12:00:00 +0100
tags:  archive
icon:  dotnet
---

I want to hide the close button of a WPF progress window, to prevent users from closing it manually. It was complicated to achieve, but perfectly doable.

While I could not find a property or function that removes or disables the close button, you can fix it with some obscure DLL hacking :)

First, define two constanst and two methods:

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

If you know a more convenient way to hide the close button (perhaps I just didn't find the correct property/method), reach out to me and I'll add it to the post.

