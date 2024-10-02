---
layout: default
title: Music
permalink: /music/

redirect_from: /bands/
---

<article>
  {% include kankoda/buttons/home.html %}

  <h1>Music</h1>
  
  <p>
    Here are some bands that I have played with over the years, ranging from 1996 to today. I have only included bands that recorded and released anything.
  </p>

  {%- assign bands = site.bands | sort: 'last-updated' | reverse | where:'hidden',false -%}
  <div class="grid col3 centered">
    {% for band in bands %}
      {%- assign slug = band.name | slugify -%}
      <a href="{{band.url}}" name="{{slug}}" title="{{band.name}}" class="scale">
        {% include kankoda/grid/item.html title=band.name name=band.name image=slug image-extension="jpg" image-folder="/assets/bands/" type="bands" %}
      </a>
    {% endfor %}
  </div>
</article>