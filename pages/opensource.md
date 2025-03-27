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
    Here are some open-source projects that I have created and currently maintain. For a list of all public projects, please have a look at my <a href="{{ site.urls.github }}">GitHub</a> profile.
  </p>

  {% include work-paragraph.html %}

  <div class="grid">
    {% for item in site.data.open-source %}
      {% include kankoda/grid/item-sdk.html item=item %}
    {% endfor %}
  </div>
</article>