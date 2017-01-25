---
title:  "Use special characters in ModelState error message"
date:   2009-08-26 07:53:00 +0100
categories: dotnet
tags: 	c# asp-net
---


I have been trying to use special characters (like &laquo; and &raquo;) in model
errors that I add to the model state.

My first approach was to avoid using the characters in ModelState.AddModelError,
since I want to keep the messages general (so that they can be displayed in both
a summary and next to the invalid control), but I have not found a way to check
if ModelState contains a certain model error, and only add these characters then.

If anyone knows how to do this, please let me know.

My temp approach is to add the special characters in the message itself and to not
use a validation summary for the view. However, this caused display problems since
the message is HTML encoded when

{% highlight c# %}
<%= Html.ValidationMessage("errorKey")%>
{% endhighlight %}

is added to the page. &laquo;, for instance, will be displayed as plain text, and
not as the two left arrows that it should be.

The workaround for this is quite simple. Since the string is HTML encoded, simply
decode the special characters, as such:

{% highlight c# %}
ModelState.AddModelError("errorKey", Server.HtmlDecode("&laquo;") + " Your custom message here");
{% endhighlight %}

This will make the view display the special characters correctly. If you add the
validation message to the right of the invalid control, the message will "point"
at the control.

It would be nice to not include the special characters in the validation string,
but this requires that we can check if a certain error message exists.