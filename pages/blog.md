---
layout: default
title: Blog
permalink: /blog/

image:  /assets/blog/header.jpg
image-show: 0
---

{% include kankoda/buttons/home.html %}

<h1>Blog</h1>

<div class="paper">
  {% include kankoda/tags/list.html tags=site.tags firstmost="general,swift,swiftui" %}
  <div class="tag-list-separator" >
    <a class="tag-list-toggle" href="javascript:toggleTagList()">Show tags</a>
    <hr />
  </div>
  <a name="tag-item-list"></a>

  {% assign posts = site.posts %}
  {% include kankoda/blog/post-list.html posts=posts %}
  {% include kankoda/tags/scripts.html %}
</div>