---
layout: plain
title: Open Source
permalink: /open-source/
---

<main class="page-content" aria-label="Content">
  <div class="wrapper">
    <article class="card">
      {% include card-header.html title="Open Source" %}
      <p>
        Here is a list of some open source projects that I've built and is maintaining. For a list of all my projects that are publically available, check out my <a href="https://github.com/{{ site.github_username| cgi_escape | escape }}">GitHub</a> profile.
      </p>
    </article>
  </div>
</main>

{% include home-grid.html items=site.data.open-source %}