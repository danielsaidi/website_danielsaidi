---
layout: default
title: Blog
permalink: /blog/
---

<script rel="javascript">
  function toggleTags() {
    let el = $(".tag-list");
    let toggle = $(".tag-list-toggle");
    el.toggleClass("expanded");
    let isExpanded = el.hasClass("expanded");
    if (isExpanded) {
      toggle.text("Hide tags");
    } else {
      toggle.text("Show tags");
      window.scrollTo({ top: 0, behavior: 'smooth' });
    }
  }
</script>

<h1>Blog</h1>

<div class="blog paper">
  <a name="tags">
  {% include kankoda/tags/list.html tags=site.tags %}
  <div class="tag-list-separator" >
    <a class="tag-list-toggle" href="javascript:toggleTags()">Show tags</a>
    <hr />
  </div>

  {% include kankoda/blog/post-list.html posts=site.posts %}
  {% include kankoda/tags/scripts.html %}
</div>