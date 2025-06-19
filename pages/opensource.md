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
    Here are some of my open-source projects that I currently maintain. For more details, please have a look at my <a href="{{ site.urls.github }}">GitHub</a> profile.
  </p>

  <p>
    Many of these projects could use a proper icon/logo. If you're a designer or posess any design skills, feel free to reach out to contribute with your design prowess for the good of the community.
  </p>

  {% assign sdks = site.data.open-source %}
  {% include kankoda/grid/grid.html items=sdks type="icons" %}
</article>