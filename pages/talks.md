---
layout: default
title: Talks
permalink: /talks/
---

<article>
  {% include kankoda/buttons/home.html %}

  <h1>Talks & Workshops</h1>
  <p>
    Here is a list of some talks and workshops that I have given at conferences, meetups, events etc. I enjoy it, but am not actively reaching out, sending in papers etc. It happens when it happens.
  </p>
</article>

<div class="talk list">
  {%- assign talks = site.talks | sort: 'date' | reverse -%}
  {% for talk in talks %}
  {% include talks/talk-list-item.html talk=talk %}
  {% endfor %}
</div>