---
title: EditorBlockFor HTML helper
date:  2011-08-28 12:00:00 +0100
tags:  .net c# web
icon:  dotnet
---

In ASP.NET MVC, Microsoft has done a nice job with creating various HTML helpers
that can be used in a form, e.g. `LabelFor`, `EditorFor`, `ValidationMessageFor`.
Let's see how we can make our own.

In my opinion, despite these nice helpers, the HTML markup still tend to become
tedious and repetitive. 

For instance, this HTML generates a form that can be used to create groups in a
web application:

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

That's quite a lot of code for handling two single properties. Furthermore, the
two editor blocks look rather similar.

To improve, we can create a small HTML helper extension that can generate an
editor block as above, with a label, and editor and a validation message. With it,
the form becomes a lot shorter and cleaner:

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

Please let me know if I have ruined the order of the universe.