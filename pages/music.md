---
layout: default
title: Music
permalink: /music/

redirect_from: /bands/
---

<article>
  {% include kankoda/buttons/back.html title="Home" href="/" %}

  <h1>Music</h1>
  
  <p>
    Here are some of the bands that I have played with over the years, ranging from 1996 to today. I have only included bands that recorded and released anything.
  </p>
  <p>
    Whenever I release something new, I add it to my <a href="https://www.facebook.com/daniel.saidi.music/">Facebook page</a>. I will also start uploading new rips of the songs in the pages below.
  </p>
</article>

{%- assign bands = site.bands | sort: 'last-updated' | reverse | where:'hidden',false -%}
{% include kankoda/grids/grid.html items=bands type="bands" %}