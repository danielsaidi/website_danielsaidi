---
layout: default
title: Open Source
permalink: /open-source/
---

<article class="card">
  {% include cards/header.html title="Open Source" %}
  <p>
    Here is a list of some of my open source projects that I currently maintain. For a list of all public projects, please have a look at my <a href="https://github.com/{{ site.github_username| cgi_escape | escape }}">GitHub</a> profile.
  </p>
</article>

{% include grid.html items=site.data.open-source %}