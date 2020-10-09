---
layout: default
title: Music
permalink: /music/

redirect_from: /bands/
---

<article class="card">
  {% include cards/header.html title="Music" %}
  <p>
    Here are some of the bands that I have played with over the years. If I ever start creating music again, you will find it on my insanely inactive <a href="https://www.facebook.com/daniel.saidi.music/">Facebook page</a>.
  </p>
</article>

{%- assign bands = site.bands | sort: 'last-updated' | reverse | where:'hidden',false -%}
{% include bands/grid.html items=bands %}