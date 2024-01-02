---
layout: default
title: Blog
permalink: /blog/
---

{% include kankoda/buttons/home.html %}

<h1>Blog</h1>

<div class="paper">
  {% include kankoda/tags/list.html tags=site.tags firstmost="general,swift,swiftui,guest-article" %}
  <div class="tag-list-separator" >
    <a class="tag-list-toggle" href="javascript:toggleTagList()">Show tags</a>
    <hr />
  </div>
  <a name="tag-item-list"></a>

  {% include kankoda/blog/post-list.html posts=site.posts %}
  {% include kankoda/tags/scripts.html %}
</div>