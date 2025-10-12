---
layout: default
title: Open-Source
permalink: /opensource

image-show: 0

redirect_from: 
  - /open-source
---

<article>
  {% include kankoda/buttons/home %}

  <h1>Open Source</h1>

  <p>
    Here are some open-source projects that I actively maintain. For more details and archived projects, please have a look at my <a href="{{ site.urls.github }}">GitHub</a> profile.
  </p>

  <p>
    If you're a designer, feel free to reach out to contribute proper icons for the good of the community.
  </p>

  {% assign sdks = site.data.open-source %}
  {% include kankoda/grids/grid items=sdks type="icons" %}
</article>