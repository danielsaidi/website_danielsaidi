---
layout: default
title: Talks
permalink: /talks/

image:  /assets/headers/talks.jpg
image-show: 0
---

{% include kankoda/buttons/home.html %}

<div class="searchbar-header">
  <h1>Talks</h1>
  {% include kankoda/search/searchbar class="discrete-dark" placeholder="Search talks..." %}
</div>

<div class="paper">
  {% include kankoda/tags/tag-list-for-collection collection=site.talks firstmost="slides" %}

  <p></p>
  <p>
    Here's a list of talks and workshops that I have given at various conferences, meetups, and events. It happens when it happens.
  </p>
  
  <a name="tag-item-list"></a>
  {%- assign talks = site.talks | sort: 'date' | reverse -%}
  {% for talk in talks %}
    {% include talks/talk-list-item.html talk=talk %}
  {% endfor %}
  {% include kankoda/tags/tag-scripts.html %}
</div>