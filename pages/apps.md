---
layout: default
title: Apps
permalink: /apps/
---

<article>
  {% include kankoda/buttons/back.html title="Home" href="/" %}
  
  <h1>Apps</h1>
  
  <p>
    Here is a list of some apps that I have built myself as well as with friends. Since I <a href="/work">freelance</a> and focus more on <a href="/open-source">open-source</a> on my spare time, most of my own apps are currently pretty old.
  </p>
  <p>
    I do freelance work, so don't hesitate to <a href="mailto:{{site.email}}">contact me</a> if you need help with development, architecture, automation, testing etc.
  </p>
</article>

{% include kankoda/grids/grid.html items=site.data.apps type="icons" %}