---
title: EditorBlockFor HTML helper
date:  2011-08-28 12:00:00 +0100
tags:  .net c# web
---

In ASP.NET MVC, Microsoft has done a nice job with creating various HTML helpers
that can be used in a form, e.g. `LabelFor`, `EditorFor`, `ValidationMessageFor`
and many others.

However, despite these nice helpers, the HTML markup still tend to become rather
tedious and repetitive. For instance, this HTML generates a form that can be used
to create groups in a web application that I am currently working on:

```html
@using (Html.BeginForm())
{
	@Html.ValidationSummary(true)
	
	<div class="editor-label">
		@Html.LabelFor(model => model.Name)
	</div>
	<div class="editor-field">
		@Html.EditorFor(model => model.Name)
		@Html.ValidationMessageFor(model => model.Name)
	</div>
	
	<div class="editor-label">
		@Html.LabelFor(model => model.CollectionName)
	</div>
	<div class="editor-field">
		@Html.EditorFor(model => model.CollectionName)
		@Html.ValidationMessageFor(model => model.CollectionName)
	</div>
	
	<div class="form-buttons">
		<input type="submit" value="@this.GlobalResource(Resources.Language.Create)" />
	</div>
}
```

That is quite a lot of code for handling two single properties. Furthermore, the
two editor blocks look rather similar, don’t you think?

I therefore decided to create a small HTML helper extension, that can be used to
generate an editor block (a label, and editor and a validation message). With it,
the resulting form becomes a lot shorter and much easier to handle:

```html
@using (Html.BeginForm())
{
	@Html.ValidationSummary(true)
	@Html.EditorBlockFor(model => model.Name);
	@Html.EditorBlockFor(model => model.CollectionName);
	
	<div class="form-buttons">
		<input type="submit" value="@this.GlobalResource(Resources.Language.Create)" />
	</div>
}
```

As you see, this method is only to be used if you want to follow the conventions
that are used by auto-generated ASP.NET MVC form code. If you do, you can save a
lot of keystrokes.

I am not familiar with the `MvcHtmlString` type, which the native methods return,
so returning an `IHtmlString` instead of `MvcHtmlString` could be a big NO, that
I do not know about.

Please let me know if I have ruined the order of the universe.