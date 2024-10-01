---
layout: default
title: Blog Archive
permalink: /blog/archive/

image:  /assets/blog/header.jpg
image-show: 0
---

{% include kankoda/buttons/home.html %}

<h1>{{page.title}}</h1>

<div class="paper">
  {% assign posts = site.posts-archive %}
  {% for post in site.posts %}
    {% include kankoda/blog/list-item.html post=post %}
  {% endfor %}
</div>