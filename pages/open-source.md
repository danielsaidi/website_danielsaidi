---
layout: default
title: Open Source
permalink: /open-source/
---

<article class="card">
  {% include cards/header.html title="Open Source" %}
  <p>
    Here is a list of some open source projects that I have built and actively maintain. For a list of all publically available projects, check out my <a href="https://github.com/{{ site.github_username| cgi_escape | escape }}">GitHub</a> profile.
  </p>
</article>

{% include cards/grid.html items=site.data.open-source %}