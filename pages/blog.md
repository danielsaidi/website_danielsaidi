---
layout: default
title: Blog
permalink: /blog/
---

<h1>Blog</h1>

<div class="paper">
  {% include kankoda/tags/list.html tags=site.tags %}
  <div class="tag-list-separator" >
    <a class="tag-list-toggle" href="javascript:toggleTagList()">Show tags</a>
    <hr />
  </div>

  {% include kankoda/blog/post-list.html posts=site.posts %}
  {% include kankoda/tags/scripts.html %}
</div>