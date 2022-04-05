---
title: Use special characters in ModelState error message
date:  2009-08-26 07:53:00 +0100
tags:  .net c#
---

I've been trying to use special characters (like &laquo; and &raquo;) to model
errors that I add to `ModelState`. This post shows you how to do it.

My first approach was to avoid using the characters in `ModelState.AddModelError`,
since I want to keep the messages general, so that they can be displayed in both
a summary and next to the invalid control. Hopwever, I havn't found a way to check
if `ModelState` contains a certain model error.

My workaround is to add the special characters in the message itself and not use a
validation summary for the view. However, this caused display problems since the
message is HTML encoded when

```csharp
<%= Html.ValidationMessage("errorKey")%>
```

is added to the page. &laquo;, for instance, will be displayed as plain text, and
not as two left arrows.

The workaround for this is quite simple. Since the string is HTML encoded, simply
decode special characters like this:

```csharp
var laquo = Server.HtmlDecode("&laquo;");
ModelState.AddModelError("errorKey", laquo + " Your custom message here");
```

This will make the view display the special characters correctly. If you add the
validation message to the right of the invalid control, the message will "point"
at the control.

It would be nice to not include the special characters in the validation string,
but this requires that we can check if a certain error message exists.