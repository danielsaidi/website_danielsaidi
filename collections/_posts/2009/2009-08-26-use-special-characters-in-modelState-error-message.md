---
title: Use special characters in ModelState error message
date:  2009-08-26 07:53:00 +0100
tags:  archive
---

In this post, let's take a look at how to use special characters (like &laquo; and &raquo;) to model errors that we can use with `ModelState`.

My first approach was to avoid special characters in `ModelState.AddModelError` altogether, to be able to display messages in both a summary and next to the invalid control. However, I haven't found a way to check if `ModelState` contains a certain model error.

My current workaround is to add the special characters to the message itself and not use a validation summary for the view.

However, this caused display problems, since the message is HTML encoded when this...

```csharp
<%= Html.ValidationMessage("errorKey")%>
```

...is added to the page. &laquo;, for instance, will be displayed as plain text, and not as two left arrows.

The workaround for this is quite simple. Since the string is HTML encoded, simply decode any special characters like this:

```csharp
var laquo = Server.HtmlDecode("&laquo;");
ModelState.AddModelError("errorKey", laquo + " Your custom message here");
```

This makes the view display the characters correctly. If you add the validation message to the right of the invalid control, the message will "point" at the control.

It would be nice to not include special characters in the validation string, but this requires that we can check if a certain error message exists.