---
layout: default
title: Blog Archive
permalink: /blog/archive/

image:  /assets/blog/header.jpg
image-show: 0
---

{% include kankoda/buttons/home %}

<h1>{{page.title}}</h1>

<div class="paper">
  {% for post in site.posts-archive %}
    {% include kankoda/blog/list-item post=post %}
  {% endfor %}
</div>