---
layout: default
title: Open Source
permalink: /open-source/
---

<article>
  {% include kankoda/buttons/back.html title="Home" href="/" %}

  <h1>Open Source</h1>
  
  <p>
    Here is a list of some of my open source projects that I currently maintain. For a list of all public projects, please have a look at my <a href="https://github.com/{{ site.github_username| cgi_escape | escape }}">GitHub</a> profile.
  </p>
</article>

{% include kankoda/grids/grid.html items=site.data.open-source type="icons" %}