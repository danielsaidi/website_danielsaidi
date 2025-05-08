---
layout: default
title: Open-Source
permalink: /opensource

image-show: 0

redirect_from: 
  - /open-source
---

<article>
  {% include kankoda/buttons/home.html %}

  <h1>Open Source</h1>

  <p>
    Here are some open-source projects that I have created and currently maintain. For more details, have a look at my <a href="{{ site.urls.github }}">GitHub</a> profile. Some are larger <a href="#libraries">libraries</a> while some provide a single <a href="#views">view</a>.
  </p>

  {% assign sdks = site.data.open-source %}
  {% assign libraries = sdks | where:"library", 1 %}
  {% assign views = sdks | where:"view", 1 %}

  <a name="libraries"></a>
  <h2>Libraries</h2>
  {% include kankoda/grid/grid.html items=libraries type="icons" %}

  <a name="views"></a>
  <h2>Views</h2>
  {% include kankoda/grid/grid.html items=views type="icons" %}
</article>